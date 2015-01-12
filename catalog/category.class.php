<?php

require_once(CATALOGPLUGINPATH . 'data.class.php');

class CatalogCategory extends CatalogData {
  // == PROPERTIES ==
  protected static $classname = __CLASS__,
                   $noExist = 'CATEGORY_DOES_NOT_EXIST',
                   $folder = 'categories',
                   $type = 'category';

  // == STATIC (SINGLETON) METHODS ==
  public static function getCategory($id, $lang = false) {
    return parent::getItem($id, $lang);
  }

  public static function getCategories(array $params = array('order' => 'custom')) {
    $categories = parent::getItems($params);

    if (isset($params['lang'])) {
      foreach ($categories as $i => $category) {
        $langs = $category->getAvailableLanguages();
        if (in_array($params['lang'], $langs)) {
          $cat = static::getCategory($category->getId(), $params['lang']);
          $categories[$i] = $cat;
        }
      }
    }

    // order
    if (!empty($params['order'])) {
      if ($params['order'] == 'custom') {
        usort($categories, 'static::sortCustom');
      } elseif ($params['order'] == 'desc') {
        $categories = array_reverse($categories);
      }
    }

    if (!empty($params['key'])) {
      if ($params['key'] == 'slug') {
        foreach ($categories as $k => $cat) {
          $categories[$cat->get('slug')] = $cat;
          unset($categories[$k]);
        }
      }
    }

    if (!empty($params['view'])) {
      if ($params['view'] == 'nested') {
        $categories = static::splitChildrenandParents($categories);
      }
    } 

    return $categories;
  }

  protected static function sortCustom($a, $b) {
    return (int) $a->get('order') - (int) $b->get('order');
  }

  protected static function splitChildrenandParents(array $categories) {
    $parents = array();
    $children = array();
    
    foreach ($categories as $category) {
      $parent = $category->get('parent');

      if (!empty($parent)) {
        $children[$parent][] = $category;
      } else {
        $parents[] = $category;
      }
    }

    return array('parents' => $parents, 'children' => $children);
  }

  public static function createCategoriesIndex() {
    return parent::createItemIndex();
  }

  // == DYNAMIC (OBJECT) METHODS ==
}

?>