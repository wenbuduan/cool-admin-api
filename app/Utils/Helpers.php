<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redis;

/*
 * 添加或者删除函数后执行composer dump-autoload
 * */

function getAppConfig(string $key) {
    return Config::get("self.$key");
}

function buildTree($collection, $parentId = null)
{
    $tree = [];

    foreach ($collection as $item) {
        if ($item->parent_id === $parentId) {
            $children = buildTree($collection, $item->id);
            if ($children) {
                $item->children = $children;
            }
            $tree[] = $item;
        }
    }

    return $tree;
}
