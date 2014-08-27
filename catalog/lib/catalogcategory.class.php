<?php

class CatalogCategory {
  private $slugged;
  private $data = array();
  private $url;
  
  public function __construct($file = null, $slugged) {
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
      return 'boo';
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
    else throw new Exception('');
  }
  
  public function setParents($url) {
    $this->url = $url;
  }
  
  public function getUrl() {
    if ($this->slugged) {
      return $this->url[0] . implode('/', array_slice($this->url, 1)) . (count($this->url) > 1 ? '/' : null);
    }
    else {
      $id = substr($this->data['id'], 0, strpos($this->data['id'], '-'));
      return $this->url[0] . '&category=' . $id;
    }
  }
}

?>
