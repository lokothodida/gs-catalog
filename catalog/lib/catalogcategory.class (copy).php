<?php

class CatalogCategory {
  private $slugged;
  private $data = array();
  private $directory;
  private $url;
  
  public function __construct($file = null, $slugged) {
    $this->directory = dirname($file);
    var_dump($file);
    $this->loadCategory($file);
    $this->slugged = $slugged;
  }
  
  private function loadCategory($file) {
    if (file_exists($file)) {
      $xml = simplexml_load_file($file);
      foreach ($xml as $field => $value) {
        $this->data[$field] = $value;
      }
      $this->data['id'] = basename($file, '.xml');
    }
  }

  public function getField($field) {
    $field = strtolower($field);

    if ($field == 'photos') {
      // format photos field
      if (isset($this->data[$field]->photo)) {
        return (array) $this->data[$field]->photo;
      } else {
        return array();
      }
    } elseif ($field == 'url') {
      return $this->getUrl();
    } elseif (isset($this->data[$field])) {
      // normal field
      return (string) $this->data[$field];
    } else {
      return null;
    }
  }

  public function __call($name, $args) {
    if (substr($name, 0, 3) == 'get') {
      $field = strtolower(substr($name, 3));
      
      // format photos field
      if ($field == 'photos') {
        if (isset($this->data[$field]->photo)) {
          return (array) $this->data[$field]->photo;
        }
        else {
          return array();
        }
      }
      
      // normal field
      if (isset($this->data[$field])) {
        return $this->data[$field];
      }
      else return null;
    }
    else throw new Exception('Field not found');
  }

  public function setParents($categories = false, $structured = true) {
    if (!isset($url)) {
      $url = array();
    }

    if ($categories !== false && $structured) {
      $this->url = $categories;
      // we have a structured list of categories, so find the parents for this category
    } elseif ($categories !== false && !$structured) {
      // we have a list of categories, but need to determine the structure
      $category = array_shift(array_values($categories));
      $stack = new SplStack;
      $stack->push($category);
      $id = $category->getId();
      $url[$id] = array();

      while (!$stack->isEmpty()) {
        $child = $stack->pop();
        array_unshift($url[$id], $child->getId());

        $parent = (string) $child->getCategory();
        if (!empty($parent)) {
          $stack->push($categories[$parent]);
        }
      }
      $this->url = $url[$id];
    } else {
      // create a list of all of the categories and run the function again
      $glob = glob($this->directory . '/*.xml');
      var_dump($glob);
      $cats = array();

      foreach ($glob as $file) {
        $obj = new CatalogCategory($file, $this->slugged);
        $cats[$obj->getId()] = $obj;
      }

      $this->setParents($cats, false);
    }
  }
  
  public function getUrl() {
    if ($this->slugged) {
      return $this->url[0] . implode('/', array_slice($this->url, 1)) . (count($this->url) > 1 ? '/' : null);
      //return $this->url[0] . implode('/', array_slice($this->url, 1)) . (count($this->url) > 1 ? '/' : null);
    }
    else {
      $id = substr($this->data['id'], 0, strpos($this->data['id'], '-'));
      return $this->url[0] . '&category=' . $id;
    }
  }
}

?>
