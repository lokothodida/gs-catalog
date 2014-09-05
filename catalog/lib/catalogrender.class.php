<?php

class CatalogRender {
  /** properties */
  private $pageData, 
          $pageType,
          $pageParams,
          $settings;

  /** methods */
  /**
   * Constructor
   * Initialize the variables from @params
   */
  public function __construct(array $params) {
    $this->pageData = $params['data'];
    $this->pageType = ucfirst($params['type'] ? $params['type'] : 'index');
    $this->pageParams = $params['params'];
  }

  /**
   * Render the correct page
   */
  public function render() {
    $this->{'render' . $this->pageType}($this->pageParams);
  }

  /** Pages */
  /**
   * Render the main page (index)
   */
  private function renderIndex(array $params) {
  }

  /**
   * Render the categories page
   */
  private function renderCategories(array $params) {
  }

  /**
   * Render an individual category
   */
  private function renderCategory(array $params) {
  }

  /**
   * Render the products
   */
  private function renderProducts(array $params) {
  }

  /**
   * Render an individual product page
   */
  private function renderProduct(array $params) {
  }

  /**
   * Render the search page
   */
  private function renderSearch(array $params) {
  }
}

?>
