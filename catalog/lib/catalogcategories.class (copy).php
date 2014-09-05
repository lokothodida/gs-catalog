<?php
/**
 * Factory for loading multiple categories (CatalogCategory objects)
 */
class CatalogCategories {
  /** properties */
  private $categories = array();
  private $urls = array();
  private $slugged;
  private $sort = array('by' => false, 'ascDesc' => false);

  /** methods */
  public function __construct($dir, $baseurl = null, $slugged) {
    $this->slugged = $slugged;
    $this->loadCategories($dir);
    $this->setUrls($baseurl);
  }

  private function loadCategories($dir) {
    $categories = glob($dir);

    foreach ($categories as $category) {
      $obj = new CatalogCategory($category, $this->slugged);
      $this->categories[$obj->getId()] = $obj;
    }
  }

  private function setUrls($baseurl) {
    foreach ($this->categories as &$category) {
      $stack = new SplStack;
      $stack->push($category);
      $id = $category->getId();
      $this->urls[$id] = array();

      while (!$stack->isEmpty()) {
        $child = $stack->pop();
        array_unshift($this->urls[$id], $child->getId());

        $parent = (string) $child->getCategory();
        if (!empty($parent)) {
          $stack->push($this->categories[$parent]);
        }
      }
      array_unshift($this->urls[$id], $baseurl);
      $category->setParents($this->urls[$id], true);
    }
  }

  private function sortCategoryOrder(CatalogCategory $a, CatalogCategory $b) {
    return (int) $a->getOrder() - (int) $b->getOrder();
  }

  private function sortCategory(CatalogCategory $a, CatalogCategory $b) {
    $field = $this->sort['by'];
    $ord = strcmp($a->getField($field), $b->getField($field));

    return ($this->sort['ascDesc'] == 'asc') ? $ord : -$ord;
  }

  public function getCategories($hierarchy = false, $sort = false) {
    // sorting
    if (is_array($sort)) {
      $this->sort['by'] = $sort['sortBy'];
      $this->sort['ascDesc'] = $sort['ascDesc'];

      uasort($this->categories, array($this, 'sortCategory'));
    } elseif ($sort == 'order') {
      uasort($this->categories, array($this, 'sortCategoryOrder'));
    }

    // give hierarchical structure
    if ($hierarchy) {
      $parents = array();
      $children = array();

      foreach ($this->categories as $k => $category) {
        $par = (string) $category->getCategory();
        if (!empty($par)) {
          $children[$par][] = $category;
        }
        else {
          $parents[$category->getId()] = $category;
        }
      }

      return array('parents' => $parents, 'children' => $children);
    }
    // just give the categories
    else {
      return $this->categories;
    }
  }

  public function displayCategories($parents, $children, $template) {
    foreach ($parents as $category) {
      $this->displayCategoriesRec($category, $children, $template);
    }
  }

  private function displayCategoriesRec(CatalogCategory $category, $children, $template) {
    echo '<li class="category ' . $category->getId() . '">';
    eval('?>' . $template);
    echo '</li>';

    if (isset($children[$category->getId()])) {
      echo '<ul>';
      foreach ($children[$category->getId()] as $child) {
        $this->displayCategoriesRec($child, $children, $template);
      }
      echo '</ul>';
    }
  }
}

?>
