<?php namespace Neomerx\Core\Repositories\Categories;

use \Carbon\Carbon;
use \Neomerx\Core\Support as S;
use \Neomerx\Core\Models\Category;
use \Neomerx\Core\Repositories\BaseRepository;
use \Neomerx\Core\Exceptions\InvalidArgumentException;

/**
 * @package Neomerx\Core
 */
class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function __construct()
    {
        parent::__construct(Category::class);
    }

    /**
     * @inheritdoc
     */
    public function index(array $scopes = [], array $columns = ['*'])
    {
        $result  = $this->readDescendants(Category::ROOT_INDEX, $scopes, $columns);

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function create($ancestorId, array $attributes)
    {
        /** @var Category $parent */
        $parent = $this->read($ancestorId);

        /** @var Category $resource */
        $resource = $this->createModel();

        $parentRight = $parent->getAttributeValue(Category::FIELD_RGT);
        $resource->setAttribute(Category::FIELD_LFT, $parentRight);
        $resource->setAttribute(Category::FIELD_RGT, $parentRight + 1);
        $this->fillModel($resource, $attributes, $this->getRelationships($ancestorId));

        // orderBy clause covers this MySQL bug http://bugs.mysql.com/bug.php?id=18913
        /** @noinspection PhpUndefinedMethodInspection */
        $allNextRgt = $resource->newQuery()
            ->where(Category::FIELD_RGT, '>=', $parentRight)
            ->orderBy(Category::FIELD_RGT, 'desc');
        /** @noinspection PhpUndefinedMethodInspection */
        $allNextLft = $resource->newQuery()
            ->where(Category::FIELD_LFT, '>', $parentRight)
            ->orderBy(Category::FIELD_LFT, 'desc');

        $this->executeInTransaction(function () use ($resource, $allNextRgt, $allNextLft) {
            /** @noinspection PhpUndefinedMethodInspection */
            $allNextRgt->increment(Category::FIELD_RGT, 2);
            /** @noinspection PhpUndefinedMethodInspection */
            $allNextLft->increment(Category::FIELD_LFT, 2);
            $resource->saveOrFail();
        });

        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function readDescendants($index, array $scopes = [], array $columns = ['*'])
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $query = $this->getUnderlyingModel()->newQuery()
            ->where(Category::FIELD_ID_ANCESTOR, '=', $index)
            ->where(Category::FIELD_CODE, '<>', Category::ROOT_CODE)
            ->orderBy(Category::FIELD_LFT, 'asc');

        /** @noinspection PhpUndefinedMethodInspection */
        empty($scopes) === true ?: $query->with($scopes);

        /** @noinspection PhpUndefinedMethodInspection */
        $descendants = $query->get($columns);

        return $descendants;
    }

    /**
     * @inheritdoc
     */
    public function update(Category $category, $ancestorId = null, array $attributes = [])
    {
        // root category can't be changed
        $allowUpdate = $category->getAttributeValue(Category::FIELD_CODE) !== Category::ROOT_CODE;
        $allowUpdate === true ?: S\throwEx(new InvalidArgumentException('category'));

        /** @var Category|null $newParent */
        $newParent =
            ($ancestorId === null || $category->getAttributeValue(Category::FIELD_ID_ANCESTOR) === $ancestorId) ? null :
                $this->read($ancestorId);

        $relationships = $this->getRelationships($ancestorId);
        $this->executeInTransaction(function () use ($category, $relationships, $attributes, $newParent) {
            $this->updateWith($category, $attributes, $relationships);
            $newParent === null ?: $this->attach($category, $newParent);
        });
    }

    /**
     * @inheritdoc
     */
    public function delete($index)
    {
        /** @var Category $category */
        $category = $this->read($index);

        // root category can't be deleted
        if ($category->getAttributeValue(Category::FIELD_CODE) === Category::ROOT_CODE) {
            return false;
        }

        $left  = $category->getAttributeValue(Category::FIELD_LFT);
        $right = $category->getAttributeValue(Category::FIELD_RGT);
        $shift = $right - $left + 1;

        /** @noinspection PhpUndefinedMethodInspection */
        $deleteQry = $category->newQuery()->whereBetween(Category::FIELD_LFT, [$left, $right]);

        // remove a gap after the move, orderBy clause covers this MySQL bug http://bugs.mysql.com/bug.php?id=18913
        /** @noinspection PhpUndefinedMethodInspection */
        $deleteLftGapQry = $category->newQuery()
            ->where(Category::FIELD_LFT, '>', $right)
            ->orderBy(Category::FIELD_LFT, 'asc');
        /** @noinspection PhpUndefinedMethodInspection */
        $deleteRgtGapQry = $category->newQuery()->where(Category::FIELD_RGT, '>', $right)
            ->orderBy(Category::FIELD_RGT, 'asc');

        $result = null;
        $deleteClosure = function () use ($index, $shift, $deleteQry, $deleteLftGapQry, $deleteRgtGapQry, &$result) {
            $result = parent::delete($index);

            /** @noinspection PhpUndefinedMethodInspection */
            $deleteQry->delete();
            /** @noinspection PhpUndefinedMethodInspection */
            $deleteLftGapQry->decrement(Category::FIELD_LFT, $shift);
            /** @noinspection PhpUndefinedMethodInspection */
            $deleteRgtGapQry->decrement(Category::FIELD_RGT, $shift);
        };

        $this->executeInTransaction($deleteClosure);

        return $result;
    }

    /**
     * Switch the subtree node with a node on the left if there are any with the same parent.
     *
     * @param Category $category
     *
     * @return void
     */
    public function moveLeft(Category $category)
    {
        /** @var Category $nodeOnTheLeft */
        $nodeOnTheLeft = $category->newQuery()
            ->where(Category::FIELD_RGT, '=', $category->getAttributeValue(Category::FIELD_LFT) - 1)
            ->first();
        if ($nodeOnTheLeft !== null) {
            $this->moveToPosition(
                $category,
                $nodeOnTheLeft->getAttributeValue(Category::FIELD_LFT),
                [$category->getKey(), $nodeOnTheLeft->getKey()]
            );
        }
    }

    /**
     * Switch the subtree node with a node on the right if there are any with the same parent.
     *
     * @param Category $category
     *
     * @return void
     */
    public function moveRight(Category $category)
    {
        /** @var Category $nodeOnTheRight */
        $nodeOnTheRight = $category->newQuery()
            ->where(Category::FIELD_LFT, '=', $category->getAttributeValue(Category::FIELD_RGT) + 1)
            ->first();

        if ($nodeOnTheRight !== null) {
            $this->moveToPosition(
                $category,
                $nodeOnTheRight->getAttributeValue(Category::FIELD_RGT) + 1,
                [$category->getKey(), $nodeOnTheRight->getKey()]
            );
        }
    }

    /**
     * @param int $ancestorId
     *
     * @return array
     */
    protected function getRelationships($ancestorId)
    {
        return $this->filterNulls([
            Category::FIELD_ID_ANCESTOR => $ancestorId,
        ]);
    }

    /**
     * @param Category $category
     * @param Category $newParent
     *
     * @throws InvalidArgumentException
     */
    private function attach(Category $category, Category $newParent)
    {
        // check 'new parent' is not actually a it's own child
        $newParentLeft  = $newParent->getAttributeValue(Category::FIELD_LFT);
        $newParentRight = $newParent->getAttributeValue(Category::FIELD_RGT);
        $left  = $category->getAttributeValue(Category::FIELD_LFT);
        $right = $category->getAttributeValue(Category::FIELD_RGT);
        if (($left <= $newParentLeft && $newParentLeft <= $right) ||
            ($left <= $newParentRight && $newParentRight <= $right)
        ) {
            throw new InvalidArgumentException('newParent');
        }

        $newParentId = $newParent->getKey();
        $this->moveToPosition(
            $category,
            $newParentRight,
            [$category->getKey(), $category->getAttributeValue(Category::FIELD_ID_ANCESTOR), $newParentId],
            $newParentId
        );
    }

    /**
     * @param Category $category
     * @param int      $newLft
     * @param array    $touchIds
     * @param int|null $updateAncestorIdTo
     *
     * @return void
     */
    private function moveToPosition(Category $category, $newLft, array $touchIds, $updateAncestorIdTo = null)
    {
        // during update we will update timestamps manually. Functions 'increment' and 'decrement' change
        // timestamps for every row they change. In most cases we don't want that as even simple move may
        // affect almost every record in the database when only a couple of changes are significant for user.
        $usesTimestamps = $category->timestamps;

        $updateClosure = function () use ($category, $usesTimestamps, $newLft, $touchIds, $updateAncestorIdTo) {
            $left     = $category->getAttributeValue(Category::FIELD_LFT);
            $right    = $category->getAttributeValue(Category::FIELD_RGT);
            $shift    = $right - $left + 1;
            $distance = $newLft - $left;
            $tmpPos   = $left;

            if ($distance < 0) {
                $distance -= $shift;
                $tmpPos   += $shift;
            }

            // turn off auto timestamp update and update required timestamps
            if ($usesTimestamps === true) {
                $category->timestamps = false;

                // update 'updated_at' timestamp
                /** @noinspection PhpUndefinedMethodInspection */
                $category
                    ->newQuery()
                    ->whereIn(Category::FIELD_ID, $touchIds)
                    ->update([Category::UPDATED_AT => Carbon::now()]);
            }

            // add some space for subtree, orderBy clause covers this MySQL bug http://bugs.mysql.com/bug.php?id=18913
            /** @noinspection PhpUndefinedMethodInspection */
            $category->newQuery()
                ->where(Category::FIELD_LFT, '>=', $newLft)
                ->orderBy(Category::FIELD_LFT, 'desc')
                ->increment(Category::FIELD_LFT, $shift);
            /** @noinspection PhpUndefinedMethodInspection */
            $category->newQuery()
                ->where(Category::FIELD_RGT, '>=', $newLft)
                ->orderBy(Category::FIELD_RGT, 'desc')
                ->increment(Category::FIELD_RGT, $shift);

            // Move the subtree. We basically need to update all left and right numbers in [$tmpPos, $tmpPos + $shift)
            // range however as we move left first we have to change the second update to check only 'right' parts as
            // 'left' numbers are already out of this range.
            $category->newQuery()
                ->where(Category::FIELD_LFT, '>=', $tmpPos)
                ->where(Category::FIELD_RGT, '<', $tmpPos + $shift)
                ->increment(Category::FIELD_LFT, $distance);
            $category->newQuery()
                ->where(Category::FIELD_RGT, '>=', $tmpPos)
                ->where(Category::FIELD_RGT, '<', $tmpPos + $shift)
                ->increment(Category::FIELD_RGT, $distance);

            // remove a gap after the move
            /** @noinspection PhpUndefinedMethodInspection */
            $category->newQuery()
                ->where(Category::FIELD_LFT, '>', $right)
                ->orderBy(Category::FIELD_LFT, 'asc')
                ->decrement(Category::FIELD_LFT, $shift);
            /** @noinspection PhpUndefinedMethodInspection */
            $category->newQuery()
                ->where(Category::FIELD_RGT, '>', $right)
                ->orderBy(Category::FIELD_RGT, 'asc')
                ->decrement(Category::FIELD_RGT, $shift);

            // update ancestor if requested
            if ($updateAncestorIdTo !== null) {
                $category->newQuery()
                    ->where(Category::FIELD_ID, '=', $category->{Category::FIELD_ID})
                    ->update([Category::FIELD_ID_ANCESTOR => $updateAncestorIdTo]);
            }
        };

        $finallyClosure = function () use ($category, $usesTimestamps) {
            $category->timestamps = $usesTimestamps;
        };

        $this->executeInTransaction($updateClosure, $finallyClosure);
    }
}
