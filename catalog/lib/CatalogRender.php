<?php

/**
 * Render/display the front end of the catalog
 */
class CatalogRender {
  /** properties */
  private $data,
          $settings,
          $router,
          $pageTitle,
          $pageContent;

  /** methods */
  // Constructor
  public function __construct(array $params) {
    $this->data     = $params['data'];
    $this->settings = $params['settings'];
    $this->generalSettings = $this->settings->get('general');
    $this->themeSettings   = $this->settings->get('theme');
    $this->router   = $params['router'];
  }

  // Are we in the catalog?
  private function inCatalog() {
    return $this->router->inCatalog();
  }

  // Does the requested page exist?
  private function pageExists($page) {
    $method = 'render' . ucfirst($page);
    return method_exists($this, 'render' . ucfirst($page)) ? $method : false;
  }

  // Display the page
  public function displayPage() {
    $page   = $this->router->getPageType();
    $params = $this->router->getParams();

    if ($this->inCatalog()) {
      ob_start();

      eval('?>' . $this->themeSettings->get('header'));

      if ($method = $this->pageExists($page)) {
        // render the correct page
        $this->{$method}($params);
      } else {
        // main page
        $this->renderIndex();
      }

      eval('?>' . $this->themeSettings->get('footer'));

      $this->pageContent = ob_get_contents();
      ob_clean();

      $this->data->title = $this->pageTitle;
      $this->data->content = $this->pageContent;
    }
  }

  /** Pages to render */
  // Index/home page
  private function renderIndex() {
    $this->pageTitle = $this->generalSettings->get('title');
  }

  // Category
  private function renderCategory($params) {
    $category = $params['category'];
    $products = $params['products'];

    $this->pageTitle = $category->getField('title');

    eval('?>' . $this->themeSettings->get('categoryHeader'));

    foreach ($products as $product) {
      eval('?>' . $this->themeSettings->get('categoryProducts'));
    }
  }

  // Product
  private function renderProduct($params) {
    $product = $params['product'];
    $categories = $params['categories'];

    $this->pageTitle = $product->getField('title');

    eval('?>' . $this->themeSettings->get('product'));
  }
}

?>
