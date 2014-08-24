<?php
  // save changes
  if (isset($_POST['submitted'])) {
    $xml = new SimpleXMLExtended('<product/>');
    $fields = new ProductFields(GSDATAOTHERPATH . $this->id . '/fields.xml');
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
    
    // save xml file
    $succ = (bool) $xml->saveXML(GSDATAOTHERPATH . $this->id . '/products/' . $_GET['products'] . '.xml');
    
    if ($succ) {
      $this->deleteI18nSearchIndex();
      $msg = i18n_r($this->id . '/PROD_EDIT_SUCC');
      $isSuccess = true;
    }
    else {
      $msg = i18n_r($this->id . '/PROD_EDIT_FAIL');
      $isSuccess = false;
    }
  }
  
  // load categories and products
  $categories = new CatalogCategories(GSDATAOTHERPATH . $this->id . '/categories/*.xml', $catalogurl, $slugged);
  $categories = $categories->getCategories();
  $products = new CatalogProducts(GSDATAOTHERPATH . $this->id . '/products/*.xml', null, $slugged);
  $products = $products->getProducts();
  $product = $products[$_GET['products']];
?>
<h3><?php i18n($this->id . '/EDIT_PRODUCT'); ?></h3>
<form action="" method="post">
  <?php include('product_form.php'); ?>
  <p id="submit_line">
		<span><input class="submit" name="submitted" value="<?php i18n('BTN_SAVECHANGES'); ?>" type="submit"></span>
		&nbsp;&nbsp;<?php i18n('OR'); ?>&nbsp;&nbsp; <a class="cancel" href="load.php?id=<?php echo $this->id; ?>&products"><?php i18n('CANCEL'); ?></a>
	</p>
</form>
