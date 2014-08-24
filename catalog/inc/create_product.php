<?php
  // load categories and products
  $categories = new CatalogCategories(GSDATAOTHERPATH . $this->id . '/categories/*.xml', $catalogurl, $slugged);
  $categories = $categories->getCategories();
  $product = new CatalogProduct(null, $slugged);
?>
<h3><?php i18n($this->id . '/CREATE_PRODUCT'); ?></h3>
<form action="load.php?id=<?php echo $this->id; ?>&products" method="post">
  <?php include('product_form.php'); ?>
  <p id="submit_line">
		<span><input class="submit" name="submitted" value="<?php i18n('BTN_SAVECHANGES'); ?>" type="submit"></span>
		&nbsp;&nbsp;<?php i18n('OR'); ?>&nbsp;&nbsp; <a class="cancel" href="load.php?id=<?php echo $this->id; ?>&products"><?php i18n('CANCEL'); ?></a>
	</p>
</form>
