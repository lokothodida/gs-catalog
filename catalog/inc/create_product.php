<?php
  // load categories
  $categoriesParams = array(
    'wildcard' => $this->dataDir . '/categories/*.xml',
    'settings' => $this->settings,
  );

  $categories = new CatalogCategories($categoriesParams);
  $categories = $categories->getCategories();

  // load product
  $productParams = array(
    'file'     => null,
    'settings' => $this->settings,
  );
  $product = new CatalogProduct($productParams);
?>
<h3><?php i18n($this->id . '/CREATE_PRODUCT'); ?></h3>
<form action="load.php?id=<?php echo $this->id; ?>&products" method="post">
  <?php include('product_form.php'); ?>
  <p id="submit_line">
    <span><input class="submit" name="submitted" value="<?php i18n('BTN_SAVECHANGES'); ?>" type="submit"></span>
    &nbsp;&nbsp;<?php i18n('OR'); ?>&nbsp;&nbsp; <a class="cancel" href="load.php?id=<?php echo $this->id; ?>&products"><?php i18n('CANCEL'); ?></a>
  </p>
</form>
