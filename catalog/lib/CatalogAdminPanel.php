<?php

/**
 * Display the administration panel
 */
class CatalogAdminPanel {
  /** properties */
  private $id,
          $settings,
          $pluginDir,
          $dataDir,
          $setup,
          $adminUrl;

  /** methods */
  public function __construct(array $params) {
    $this->id        = $params['id'];
    $this->settings  = $params['settings'];
    $this->setup     = $params['setup'];
    $this->pluginDir = $params['pluginDir'];
    $this->dataDir   = $params['dataDir'];
    $this->adminUrl  = $params['adminUrl'];
  }

  public function displayPage() {
    $generalSettings      = $this->settings->get('general');
    $themeSettings        = $this->settings->get('themes');
    $customFieldsSettings = $this->settings->get('customfields');

    // products page
    if (isset($_GET['products'])) {
      if ($_GET['products'] == 'create') {
        include($this->pluginDir . '/inc/create_product.php');
      } elseif (!empty($_GET['products'])) {
        include($this->pluginDir . '/inc/edit_product.php');
      } else {
        // view products
        include($this->pluginDir . '/inc/view_products.php');
      }
    } elseif (isset($_GET['options'])) {
      // options page
      include($this->pluginDir . '/inc/options.php');
    } elseif (!empty($_GET['categories']) && $_GET['categories'] == 'create') {
      // categories pages
      // catalog&categories=create
      include($this->pluginDir . '/inc/create_category.php');
    } elseif (!empty($_GET['categories']) && empty($_GET['delete'])) {
      // catalog&categories=categoryid
      include($this->pluginDir . '/inc/edit_category.php');
    } elseif (isset($_GET['categories'])) {
      // catalog&categories or just catalog
      include($this->pluginDir . '/inc/view_categories.php');
    } else {
      include($this->pluginDir . '/inc/admin.php');
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
