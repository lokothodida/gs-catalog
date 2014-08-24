<?php

class CatalogDisplay {
  private $baseurl;
  private $breadcrumbs = array();

  public function __construct($baseurl, $breadcrumbs) {
    $this->baseurl = $baseurl;
    $this->breadcrumbs = $breadcrumbs;
  }
  
  public function getBreadCrumbs() {
    foreach ($this->breadcrumbs as $breadcrumb) {
      echo '<li class="breadcrumb"><a href="' . $this->baseurl . $breadcrumb['url'] . '">' . $breadcrumb['title'] . '</a></li>';
    }
  }
  
  public function getUrl() {
    return (string) $this->baseurl;
  }
}

?>
