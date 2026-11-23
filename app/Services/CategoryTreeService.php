<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class CategoryTreeService
{
    public function getTree(): array
    {
        $cache = Cache::store('redis');

        $tree = $cache->get('categories:tree');

        if ($tree !== null) {
            return $tree;
        }

        $categories = Category::query()
            ->select('id', 'name', 'parent_id', 'is_active')
            ->get();

        $categoryLookup = $categories->keyBy('id');
        $categoryMap = [];

        foreach ($categories as $category) {
            if ($category->is_active) {
                $categoryMap[$category->id] = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'children' => [],
                ];
            }
        }

        $tree = [];

        foreach ($categories as $category) {
            if (!$category->is_active) {
                continue;
            }

            $parentId = $category->parent_id;

            while ($parentId !== null) {
                if (isset($categoryMap[$parentId])) {
                    break;
                }

                $parent = $categoryLookup->get($parentId);
                $parentId = $parent?->parent_id;
            }

            if ($parentId !== null) {
                $categoryMap[$parentId]['children'][] =
                    &$categoryMap[$category->id];
            } else {
                $tree[] = &$categoryMap[$category->id];
            }
        }

        if (!empty($tree)) {
            $cache->put(
                'categories:tree',
                $tree,
                now()->addHour()
            );
        }

        return $tree;
    }
}
