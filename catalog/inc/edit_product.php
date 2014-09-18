<?php
  // choose correct product id
  $productId = $_GET['products'];

  if ($this->setup->i18nExists() && isset($_GET['lang']) && $_GET['lang'] != return_i18n_default_language()) {
    $productId .= '_' . $_GET['lang'];
  }

  // save changes
  if (isset($_POST['submitted'])) {
    $xml = new SimpleXMLExtended('<product/>');
    $fields = new CatalogSettingsFields(array('file' => $this->dataDir . '/fields.xml'));
    $fields = $fields->getFields();

    // title
    $xml->title = null;
    $xml->title->addCData($_POST['title']);

    // categories
    $xml->categories = null;
    if (!empty($_POST['categories'])) {
      foreach ($_POST['categories'] as $k => $cat) {
        $xml->categories->category[$k] = $cat;
      }
    }

    // custom fields
    foreach ($fields as $field) {
      if (isset($_POST[$field->name])) {
        $xml->{$field->name} = null;
        $xml->{$field->name}->addCData($_POST[$field->name]);
      }
      // checkbox is set to 'n'
      elseif ($field->type == 'checkbox') {
        $xml->{$field->name} = null;
        $xml->{$field->name}->addCData('n');
      }
    }

    // dates
    $xml->credate = null;
    $xml->credate->addCData($_POST['credate']);
    $xml->pubdate = null;
    $xml->pubdate->addCData(time());

    // save xml file
    $succ = (bool) $xml->saveXML($this->dataDir . '/products/' . $productId . '.xml');
    
    if ($succ) {
      $this->setup->deleteI18nSearchIndex();
      $msg = i18n_r($this->id . '/PROD_EDIT_SUCC');
      $isSuccess = true;
    } else {
      $msg = i18n_r($this->id . '/PROD_EDIT_FAIL');
      $isSuccess = false;
    }
  }

  // load categories
  $categoriesParams = array(
    'wildcard' => $this->dataDir . '/categories/*.xml',
    'settings' => $this->settings,
  );

  $categories = new CatalogCategories($categoriesParams);
  $categories = $categories->getCategories();

  // load products
  $productsParams = array(
    'wildcard' => $this->dataDir . '/products/*.xml',
    'settings' => $this->settings,
  );
  $products = new CatalogProducts($productsParams);

  $productParams = array(
    'file'     => $this->dataDir . '/products/' . $productId . '.xml',
    'settings' => $this->settings,
  );
  $product = new CatalogProduct($productParams);
?>
<h3><?php i18n($this->id . '/EDIT_PRODUCT'); ?></h3>
<form action="" method="post">
  <?php include('product_form.php'); ?>
  <p id="submit_line">
    <span><input class="submit" name="submitted" value="<?php i18n('BTN_SAVECHANGES'); ?>" type="submit"></span>
    &nbsp;&nbsp;<?php i18n('OR'); ?>&nbsp;&nbsp; <a class="cancel" href="load.php?id=<?php echo $this->id; ?>&products"><?php i18n('CANCEL'); ?></a>
  </p>
</form>
