<?php

class CatalogPlugin {
  public  $id, $title, $version, $author, $website, $desc, $page;
  private $dir, $options;

  // constructor to set the id and plugin directory path
  public function __construct($id, $dir) {
    $this->id = $id;
    $this->root = $dir;
    $this->title    = 'PLUGIN_TITLE';
    $this->version  = '0.2';
    $this->author   = 'Lawrence Okoth-Odida';
    $this->website  = 'https://github.com/lokothodida/';
    $this->desc     = 'PLUGIN_DESC';
    $this->admin    = 'back';
  }

  // load language files
  public function i18nMerge() {
    i18n_merge($this->id) || i18n_merge($this->id, 'en_US');
  }

  // queue autoload functions
  public function autoload() {
    spl_autoload_register(array($this, 'autoloader'));
  }

  // our actual autoloader
  private function autoloader($class) {
    $file = $this->root . '/lib/' . strtolower($class) . '.class.php';

    // load the class file if it exists
    if (file_exists($file)) {
      include($file);
    }
  }
  
  // wrapper function for adding filters native to this class (not really necessary)
  public function addFilter($name, $method) {
    add_filter($name, array($this, $method));
  }
  
  // wrapper function for adding hooks native to this class (again, not really necessary)
  public function addAction($hook, $method, $args = array()) {
    add_action($hook, array($this, $method), $args);
  }
  
  // wrapper function for adding sidebars (...yep, you guessed it)
  public function addSidebar($name, $flag = false, $page = null) {
    if ($page == null) $page = $this->id;
    add_action($page . '-sidebar','createSideMenu', array($this->id, i18n_r($this->id . '/' . $name), $flag));
  }
  
  // register the plugin
  public function register() {
    // register the plugin
    // note that some properties are used in conjunction with i18n_r for language-flexibility
    register_plugin(
      $this->id,
      i18n_r($this->id . '/' . $this->title), 
      $this->version,
      $this->author,
      $this->website,
      i18n_r($this->id . '/' . $this->desc),
      $this->id,
      array($this, $this->admin)
    );
  }
  
  // any initialization that should be done
  public function init() {
    // make the directories
    $this->mkDirs();
    
    // actions
    $this->addAction('error-404', 'front');
    add_action('nav-tab', 'createNavTab', array($this->id, $this->id, i18n_r($this->id . '/PLUGIN_TITLE'), 'home'));
    
    // i18n search actions
    add_action('search-index', array($this, 'i18nSearchIndex'));
    add_filter('search-item', array($this, 'i18nSearchItem'));
    add_filter('search-display', array($this, 'i18nSearchDisplay'));
    
    // sidebar links
    $this->addSideBar('HOME', 'home');
    $this->addSideBar('CATEGORIES', 'categories');
    $this->addSideBar('PRODUCTS', 'products');
    $this->addSideBar('OPTIONS', 'options');
    
    // the below registers the codemirror css and javascript files
    register_script('codemirror', $GLOBALS['SITEURL'] . $GLOBALS['GSADMIN'] . '/template/js/codemirror/lib/codemirror-compressed.js', '0.2.0', FALSE);
    register_style('codemirror-css', $GLOBALS['SITEURL'] . $GLOBALS['GSADMIN'] . '/template/js/codemirror/lib/codemirror.css','screen',FALSE);
    register_style('codemirror-theme', $GLOBALS['SITEURL'] . $GLOBALS['GSADMIN'] . '/template/js/codemirror/theme/default.css','screen',FALSE);
    
    register_script('simpleCart', $GLOBALS['SITEURL'] . '/plugins/' . $this->id . '/assets/js/simpleCart.min.js', '1.0', FALSE);
    
    // queue the scripts and styles
    queue_script('codemirror', GSBACK);
    queue_style('codemirror-css', GSBACK);
    queue_style('codemirror-theme', GSBACK);
    
    queue_script('jquery', GSFRONT); 
    queue_script('simpleCart', GSFRONT);
    
    // options
    $this->initOptions();
  }
  
  private function initOptions() {
    if (empty($this->options)) {
      $this->options = new CatalogOptions(GSDATAOTHERPATH . $this->id . '/');
      $this->options = $this->options->getOptions();
    }
  }
  
  private function deleteI18nSearchIndex() {
    if (function_exists('delete_i18n_search_index')) delete_i18n_search_index();
  }
  
  public function i18nSearchIndex() {
    $options = new CatalogOptions(GSDATAOTHERPATH . $this->id . '/');
    $options = $options->getOptions();
    $fullurl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $general = &$options->general;
    $templates = &$options->templates;
    $fields = &$options->fields;
    $catalogurl = $GLOBALS['SITEURL'] . $general->baseurl;
    
    $slugged = (string) $options->general->slugged == 'y' ? true : false;
  
  
    // index categories
    $categories = new CatalogCategories(GSDATAOTHERPATH . $this->id . '/categories/*.xml', $GLOBALS['SITEURL'] . $general->baseurl, $slugged);
    $categories = $categories->getCategories();
    
    /*
    foreach ($categories as $category) {
      i18n_search_index_item('cat:' . $category->getId(), null, time(), time(), null, (string) $category->getTitle(), (string) $category->getDescription());
    }
    */
    // index products
    
    
    $products = new CatalogProducts(GSDATAOTHERPATH . $this->id . '/products/*.xml', $fields, $slugged);
    $prods = $products->getProducts();
    
    foreach ($prods as $product) {
      // content field
      $content = '';
      
      // set categories (and add the titles to the content variable)
      foreach ($product->getField('categories')->category as $cat) {
        //$product->setCategory($categories[(string) $cat]);
        
        $content .= $categories[(string) $cat]->getTitle() . ' ';
      }
      //$product->setUrl($categories[$cat]);
      
      // load each field into the content variable (so they are searchable)
      foreach ($fields as $field) {
        
        if ($field->index == 'y') {
          $content .= $product->getField($field->name) . ' ';
        }
      }
      
      // tags
      $tags = array($general->slug);
      $tags = implode(', ', $tags);
      
      i18n_search_index_item('cat:' . $product->getField('id'), null, time(), time(), null, (string) $product->getField('title'), $content);
    }
  }
  
  public function i18nSearchItem($id, $language, $creDate, $pubDate, $score) {
    if (substr($id, 0, 4) == 'cat:') {
      return new I18nSearchCatalogResultItem($id, $language, $creDate, $pubDate, $score);
    }
    return null;
  }
  
  public function i18nSearchDisplay($item, $showLanguage, $showDate, $dateFormat, $numWords) {
    if (substr($item->id, 0, 4) == 'cat:') {
      $options = new CatalogOptions(GSDATAOTHERPATH . $this->id . '/');
      $options = $options->getOptions();
      $templates = &$options->templates;
      
      var_dump($item);
      echo '<h3><a href="">Title: '. $item->title . '</a></h3>';
      eval('?>' . $templates['i18nsearch-product']);
      
      return true;
    }
    return false;
  }
  
  // creates the necessary directories and files
  private function mkDirs() {
    $dirs = array('', '/products', 'categories', 'templates', 'tmp');
    $defaults = array('general', 'fields', 'templates');
    
    // make all the directories
    foreach ($dirs as $dir) {
      $file = GSDATAOTHERPATH . $this->id . '/' . $dir;
      if (!file_exists($file)) {
        mkdir($file, 0755, true);
      }
    }

    // data/other/catalog/.htaccess (deny all)
    if (!file_exists(GSDATAOTHERPATH . $this->id . '/.htaccess')) {
      file_put_contents(GSDATAOTHERPATH . $this->id . '/.htaccess', 'Deny from all');
    }

    // data/other/catalog/tmp/.htaccess (allow all)
    if (!file_exists(GSDATAOTHERPATH . $this->id . '/tmp/.htaccess')) {
      file_put_contents(GSDATAOTHERPATH . $this->id . '/tmp/.htaccess', 'Allow from all');
    }

    // copy defaults
    $this->makeDefaultFiles();
  }
  
  private function makeDefaultFiles($overwrite = false) {
    $defaults = array('general', 'fields', 'templates', 'identifiers', 'cart');
    $success = array();
    foreach ($defaults as $default) {
      $def = $this->root . '/assets/defaults/' . $default . '.xml';
      $f = GSDATAOTHERPATH . $this->id . '/' . $default . '.xml';

      if ($default == 'templates') {
        // copy the basic themes available
        $themes = glob($this->root . '/assets/defaults/templates/*.xml');

        foreach ($themes as $theme) {
          $f = GSDATAOTHERPATH . $this->id . '/templates/' . basename($theme);
          if (!file_exists($f) || $overwrite) {
            $success[] = copy($theme, $f);
          }
        }
      }

      if (!file_exists($f) || $overwrite) {
        $success[] = copy($def, $f);
      }
    }

    return $success;
  }
  
  private function clearCatalog() {
    $success = array();
    
    // categories
    foreach (glob(GSDATAOTHERPATH . $this->id . '/categories/*.xml') as $category) {
      $success[] = unlink($category);
    }
    // products
    foreach (glob(GSDATAOTHERPATH . $this->id . '/products/*.xml') as $product) {
      $success[] = unlink($product);
    }
    
    return $success;
  }
  
  // slugification
  // http://gilbert.pellegrom.me/php-quick-convert-string-to-slug/
  public function toSlug($string) {
    return rtrim(strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $this->transliterate($string)))), '-');
  }
  
  // transliteration
  public function transliterate($string) {
    if (isset($GLOBALS['i18n']['TRANSLITERATION']) && is_array($translit = $GLOBALS['i18n']['TRANSLITERATION']) && count($translit > 0)) {
      $string =  str_replace(array_keys($translit), array_values($translit), $string);
    }
    return $string;
  }
  
  public function printPaginatedLinks($pages, $current, $url) {
    if ($pages > 0) {
      $i = 0;
      
      // first
      echo '<a href="' . $url . '1">&laquo;</a>';
      
      // prev
      if (($pages > 0) && ($current > 1)) {
        echo '<a href="' . $url . ($current - 1) . '">&lsaquo;</a>';
      }
      
      // print pages 1-7
      if ($pages < 7) {
        while ($i < $pages) {
          $i++;
          if ($i == $current) {
            echo '<span class="current">' . $i . '</span>';
          }
          else {
            echo '<a href="' . $url . $i .'">' . $i . '</a>';
          }
        }
      }
      // 7 or more pages
      else {
        if ($current > $pages - 3) {
          $i = $pages - 4;
        }
        else {
          $i = $current - 1;
        }
        
        while (($i < $current + 3) && $i < $pages) {
          $i++;
          if ($i == $current) {
            echo '<span class="current">' . $i . '</span>';
          }
          else {
            echo '<a href="' . $url . $i .'">' . $i . '</a>';
          }
        }
      }
      
      
      // next
      if (($pages > 0) && ($current < $pages)) {
        echo '<a href="' . $url . ($current + 1) . '">&rsaquo;</a>';
      }
      echo '<a href="' . $url . $pages . '">&raquo;</a>';
    }
  }

  // front-end callback
  public function front() {
    global $data_index;

    $options = new CatalogOptions(GSDATAOTHERPATH . $this->id . '/');
    $options = $options->getOptions();
    $fullurl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    
    $slugged = (string) $options->general->slugged == 'y' ? true : false;
    
    $url     = new CatalogRouter($fullurl, $GLOBALS['SITEURL'], $slugged);

    $params  = $url->getParams($slugged);
    $general = &$options->general;
    $templates = &$options->templates;
    $fields = &$options->fields;
    $catalogurl = $GLOBALS['SITEURL'] . $general->baseurl;
    $cart = new CatalogCart(GSDATAOTHERPATH . $this->id . '/cart.xml');

    $slugged = ((string) $general->slugged == 'y' ? true : false);

    if ($params[0] == $options->general->slug) {
      // initialize catagories
      $categories = new CatalogCategories(GSDATAOTHERPATH . $this->id . '/categories/*.xml', $GLOBALS['SITEURL'] . $general->baseurl, ((string) $general->slugged == 'y' ? true : false));
      $categories = $categories->getCategories();

      // initialize breadcrumbs
      $breadcrumbs = array();
      $breadcrumbs[] = array('title' => $general->title, 'url' => null);
      
      $end = end($params);
      
      // header
      ob_start();
      
      // category
      if (file_exists(GSDATAOTHERPATH . $this->id . '/categories/' . $end .'.xml')) {
        $cats = new CatalogCategories(GSDATAOTHERPATH . $this->id . '/categories/*.xml', $GLOBALS['SITEURL'] . $general->baseurl, ((string) $general->slugged == 'y' ? true : false));
        $data_index->title = $categories[$end]->getTitle();
        
        $products = new CatalogProducts(GSDATAOTHERPATH . $this->id . '/products/*.xml', $fields, $slugged);
        
        // breadcrumbs
        $buildup = '';
        foreach ($params as $param) {
          if (isset($categories[$param])) {
            if ($slugged) {
              $buildup .= $param . '/';
              $breadcrumbs[] = array('title' => $categories[$param]->getTitle(), 'url' => $buildup);
            }
            else {
              $breadcrumbs[] = array('title' => $categories[$param]->getTitle(), 'url' => str_replace($catalogurl, '', $categories[$param]->getUrl()));
            }
          }
        }
        
        $catalog = new CatalogDisplay($catalogurl, $breadcrumbs);
        eval('?>' . $templates['header']);
        
          // if the category view is 'parents', we need to show the children
          if ($general->categoryview == 'parents') {
            $categories = $cats->getCategories(true);
            
            if (isset($categories['children'][$end])) {
              echo '<ul class="categories">';
              foreach ($categories['children'][$end] as $category) {
                echo '<li class="category ' . $category->getId() . '">';
                  eval('?>' . $templates['main']);
                echo '</li>';
              }
              echo '</ul>';
            }
          }
          
          $search = (!empty($_GET['search']) && $general->internalsearch == 'y') ? $_GET['search'] : false;
          $page = !empty($_GET['page']) ? (int) $_GET['page'] : 1;

          $categories = $cats->getCategories();
          $products = $products->getProducts($end, $search);
          
          $prods = array_slice($products, ($page - 1) * (int) $general->productsperpage, (int) $general->productsperpage);
          
          // top pagaination
          if ($general->pagination == 'top' || $general->pagination == 'both') {
            echo '<div class="pagination">';
            $this->printPaginatedLinks(ceil(count($products) / (int) $general->productsperpage), $page, $categories[$end]->getUrl() . ($slugged ? '?page=' : '&page='));
            echo '</div>';
          }
          
          echo '<div class="products">';
          
          foreach ($prods as $product) {
            foreach ($product->getField('categories')->category as $cat) {
              $product->setCategory($categories[(string) $cat]);
            }
            $product->setUrl($categories[$end]);
            eval('?>' . $templates['category']);
          }
          echo '</div>';
          
          // bottom pagination
          if ($general->pagination == 'bottom' || $general->pagination == 'both') {
            echo '<div class="pagination">';
            $this->printPaginatedLinks(ceil(count($products) / (int) $general->productsperpage), $page, $categories[$end]->getUrl() . ($slugged ? '?page=' : '&page='));
            echo '</div>';
          }
          
          $content = ob_get_contents();
        
        
        $data_index->content = $content;
      }
      
      // product
      elseif (file_exists(GSDATAOTHERPATH . $this->id . '/products/' . basename($end, '.htm') .'.xml')) {
        $product = new CatalogProduct(GSDATAOTHERPATH . $this->id . '/products/' . basename($end, '.htm') .'.xml', null, $slugged);
        foreach ($product->getField('categories')->category as $cat) {
          $product->setCategory($categories[(string) $cat]);
        }
        
        // get the current category
        $cat = end($params);
        $cat = prev($params);
        
        $product->setUrl($categories[$cat]);
        
        $data_index->title = $product->getField('title');
        
          // breadcrumbs
          
          $buildup = '';
          foreach ($params as $param) {
            if (isset($categories[$param])) {
              
              if ($slugged) {
                $buildup .= $param . '/';
                $breadcrumbs[] = array('title' => $categories[$param]->getTitle(), 'url' => $buildup);
              }
              else {
                $breadcrumbs[] = array('title' => $categories[$param]->getTitle(), 'url' => str_replace($catalogurl, '', $categories[$param]->getUrl()));
              }
            
            }
          
          }
          $breadcrumbs[] = array('title' => $product->getField('title'), 'url' => str_replace($catalogurl, '', $product->getUrl()));
          
          
          $catalog = new CatalogDisplay($catalogurl, $breadcrumbs);
          eval('?>' . $templates['header']);
        
          eval('?>' . $templates['product']);
          $content = ob_get_contents();
        
        
        $data_index->content = $content;
      }
      /*
      // featured
      elseif ($end == 'featured') {
        $data_index->title = 'featured';
      }
      */
      // main
      elseif ($end == (string) $general->slug) {
        $categories = new CatalogCategories(GSDATAOTHERPATH . $this->id . '/categories/*.xml', $GLOBALS['SITEURL'] . $general->baseurl, ((string) $general->slugged == 'y' ? true : false));
        
        // meta information
        $data_index->title = (string) $general->title;

          echo '<ul class="categories">';
          
          // view all categories hierarchically
          if ($general->categoryview == 'hierarchical') {
            $cats = $categories->getCategories(true, 'order');
            $categories->displayCategories($cats['parents'], $cats['children'], $templates['main']);
          }
          
          // view parents only
          elseif ($general->categoryview == 'parents') {
            $categories = $categories->getCategories(true, 'order');
            
            foreach ($categories['parents'] as $category) {
              echo '<li>';
              eval('?>' . $templates['main']);
              echo '</li>';
            }
          }
          
          // view all categories (in order
          else {
            $categories = $categories->getCategories(false, 'order');

            foreach ($categories as $category) {
              echo '<li>';
              eval('?>' . $templates['main']);
              echo '</li>';
            }
          }
          
          echo '</ul>';
        
          $content = ob_get_contents();
        
        
        $data_index->content = $content;
      }
      else {
        $data_index->title   = (string) $general->title;
        $data_index->content = (string) $general->pageerror;
      }
      
      ob_clean();
      
      if ($cart->getEnabled() == 'y') {
        ?>
        <style>
          <?php echo $cart->getCSS(); ?>
        </style>
        <script>
          simpleCart({
            cartColumns: [
              { attr: "name", label: "<?php echo $cart->getLabelName(); ?>"},
              { view: "currency", attr: "price", label: "<?php echo $cart->getLabelPrice(); ?>"},
              { view: "decrement", label: false},
              { attr: "quantity", label: "<?php echo $cart->getLabelQuantity(); ?>"},
              { view: "increment", label: false},
              { view: "currency", attr: "total", label: "<?php echo $cart->getLabelTotal(); ?>" },
              { view: "remove", text: "Remove", label: false}
            ],
            cartStyle: "<?php echo $cart->getCartStyle(); ?>",
            checkout: {
              type: "<?php echo $cart->getCheckoutType(); ?>",
              email: "<?php echo $cart->getCheckoutEmail(); ?>"
            },
            currency: "<?php echo $cart->getCurrency(); ?>",
            language: "<?php echo $cart->getLanguage(); ?>",
            shippingFlatRate: <?php echo $cart->getShippingFlatRate(); ?>,
          });
        </script>
        <?php
      }
      $data_index->content .= ob_get_contents();
      ob_end_clean();
    }
  }
  
  // admin panel callback
  public function back() {

    // load settings/options
    $options = new CatalogOptions(GSDATAOTHERPATH . $this->id . '/');
    $options = $options->getOptions();
    $general = new CatalogGeneralOptions(GSDATAOTHERPATH . $this->id . '/general.xml');
    $templates = &$options->templates;
    $fields = &$options->fields;

    $slugged = (string) $options->general->slugged == 'y' ? true : false;
    $catalogurl = $GLOBALS['SITEURL'] . $general->getBaseurl();
    $adminUrl = 'load.php?id=' . $this->id;

    // products page
    if (isset($_GET['products'])) {
      if ($_GET['products'] == 'create') {
        include($this->root . '/inc/create_product.php');
      }
      elseif (!empty($_GET['products'])) {
        include($this->root . '/inc/edit_product.php');
      }
      else {
        // view products
        include($this->root . '/inc/view_products.php');
      }
    }
    // options page
    elseif (isset($_GET['options'])) {
      include($this->root . '/inc/options.php');
    }
    // categories pages
    // catalog&categories=create
    elseif (!empty($_GET['categories']) && $_GET['categories'] == 'create') {
      include($this->root . '/inc/create_category.php');
    }
    // catalog&categories=categoryid
    elseif (!empty($_GET['categories']) && empty($_GET['delete'])) {
      include($this->root . '/inc/edit_category.php');
    }
    // catalog&categories or just catalog
    elseif (isset($_GET['categories'])) {
      include($this->root . '/inc/view_categories.php');
    }
    else {
      include($this->root . '/inc/admin.php');
    }

    // error handling, taking from wiki
    if (isset($msg)) {
      if (isset($undo)) $msg .= ' <a href="load.php?id=' . $undo . '">' . i18n_r('UNDO') . '</a>' 
    ?>
    <script type="text/javascript">
      $(function() {
        $('div.bodycontent').before('<div class="<?php echo $isSuccess ? 'updated' : 'error'; ?>" style="display:block;">'+
                <?php echo json_encode($msg); ?>+'</div>');
        $(".updated, .error").fadeOut(500).fadeIn(500);
      });
    </script>
    <?php 
    } 
  }
}

?>
