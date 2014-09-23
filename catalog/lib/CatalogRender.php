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

      $this->executeTemplate($this->themeSettings->get('header'));

      if ($method = $this->pageExists($page)) {
        // render the correct page
        $this->{$method}($params);
      } else {
        // main page
        $this->renderIndex();
      }

      $this->executeTemplate($this->themeSettings->get('footer'));

      $this->pageContent = ob_get_contents();
      ob_clean();

      $this->data->title = $this->pageTitle;
      $this->data->content = $this->pageContent;
    }
  }

  /** Pages to render */
  // Index/home page
  private function renderIndex($params) {
    // set page title
    $this->pageTitle = $this->generalSettings->get('title');

    // set parameters
    $categories   = $params['categories'];
    $categoryView = $this->generalSettings->get('categoryview');

    $this->executeTemplate($this->themeSettings->get('indexHeader'));

    // format
    if ($categoryView == 'hierarchical') {
      // hierarchical view
      ?>
      <ul><?php $this->renderCategoryRec($categories, $this->themeSettings->get('indexCategories')); ?></ul>
      <?php
    } else {
      // ordered view
      foreach ($categories as $category) {
        $this->executeTemplate($this->themeSettings->get('indexCategories'),
          array(
            'category' => $category,
          )
        );
      }
    }

    $this->executeTemplate($this->themeSettings->get('indexFooter'));
  }

  // Category (hierarchical) for index
  private function renderCategoryRec($categories, $template) {
    foreach ($categories as $category) :
      $children = $category['children'];
      $category = $category['category'];
    ?>
    <li>
      <?php
        $this->executeTemplate($template, array('category' => $category, 'children' => $children));

        // render the children
        if ($children) {
          ?>
          <ul>
          <?php $this->renderCategoryRec($children, $template); ?>
          </ul>
          <?php
        }
      ?>
    </li>
    <?php
    endforeach;
  }

  // Category
  private function renderCategory($params) {
    $category = $params['category'];
    $products = $params['products'];
    $pagination = $this->generalSettings->get('pagination');

    $this->pageTitle = $category->getField('title');

    // pagination
    if ($pagination != 'bottom') {
      echo $params['navigation'];
    }

    // display category and products
    $this->executeTemplate($this->themeSettings->get('category'),
      array(
        'category' => $category,
        'products' => $products,
      )
    );

    // pagination
    if ($pagination != 'top') {
      echo $params['navigation'];
    }
  }

  // Product
  private function renderProduct($params) {
    $product = $params['product'];
    $categories = $params['categories'];

    $this->pageTitle = $product->getField('title');

    $this->executeTemplate($this->themeSettings->get('product'),
      array(
        'categories' => $categories,
        'product'    => $product,
      ));
  }

  // Search page
  private function renderSearch($params) {
    $results = $params['results'];
    include('../inc/search.php');
  }

  // Featured page
  private function renderFeatured($params) {
    // ...
  }

  // Evaluate
  private function evaluate($expr) {
    return eval('?>' . $expr);
  }

  private function executeTemplate($template, $vars = array()) {
    // import variables
    foreach ($vars as $k => $var) {
      ${$k} = $var;
    }

    // evaluate template string
    eval('?>' . $template);
  }
}

?>
