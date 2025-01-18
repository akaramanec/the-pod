<?php

namespace src\helpers;

use Yii;
use yii\helpers\ArrayHelper;

class CategoryHelp
{
    private static $_parents = [];
    private static $_children = [];

    public static function parents($category, $mode, $self = true)
    {
        if ($self) {
            self::$_parents[$category->id] = $category;
        }

        self::parentsRecursive($category);
        if ($mode == 'string') {
            return self::nameStr();
        }
        if ($mode == 'breadcrumbs') {
            return self::breadcrumbs();
        }
        if ($mode == 'microMarking') {
            return self::microMarking();
        }
    }

    public static function getChildren($category)
    {
        self::$_children[$category->id] = $category;
        if ($category->children) {
            foreach ($category->children as $item) {
                self::$_children[$item->id] = $item;
                self::getChildren($item);
            }
        }
        return self::$_children;
    }

    public static function getChildrenWithoutParent($category)
    {
        if ($category->children) {
            foreach ($category->children as $item) {
                self::$_children[$item->id] = $item;
                self::getChildren($item);
            }
        }
        return self::$_children;
    }

    public static function getChildrenId($category)
    {
        $categories = self::getChildrenWithoutParent($category);
        self::$_children = [];
        return ArrayHelper::getColumn($categories, 'id');
    }

    public static function parentsRecursive($category)
    {
        if ($category->parent) {
            self::$_parents[$category->parent->id] = $category->parent;
            self::parentsRecursive($category->parent);
        }
    }

    public static function breadcrumbs()
    {
        $items = [];
        foreach (self::$_parents as $item) {
            $items[] = [
                'label' => $item->name,
                'url' => ['/rent/category/index', 'slug' => $item->slug]
            ];
        }
        return array_reverse($items);
    }

    public static function microMarking()
    {
        $items = [];
        $x = 1;
        foreach (array_reverse(self::$_parents) as $item) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $x,
                'name' => $item->name,
                'item' => Yii::$app->params['homeUrl'] . '/catalog/' . $item->slug
            ];
            $x++;
        }
        return $items;
    }

    static function nameStr()
    {
        $name = '';
        foreach (array_reverse(self::$_parents) as $item) {
            $name .= $item->name . ' / ';
        }
        self::$_parents = [];
        return substr($name, 0, -3);
    }



}
