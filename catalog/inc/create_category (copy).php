<?php

  $categories = new CatalogCategories(GSDATAOTHERPATH . $this->id . '/categories/*.xml', $catalogurl, $slugged);
  $categories = $categories->getCategories();
  $category   = new CatalogCategory(null, $slugged);
?>
<h3><?php i18n($this->id . '/CREATE_CATEGORY'); ?></h3>
<form action="load.php?id=<?php echo $this->id; ?>&categories" method="post">
  <?php include('category_form.php'); ?>
  <p id="submit_line">
		<span><input class="submit" name="submitted" value="<?php i18n('BTN_SAVECHANGES'); ?>" type="submit"></span>
		 &nbsp;&nbsp;<?php i18n('OR'); ?>&nbsp;&nbsp; <a class="cancel" href="load.php?id=<?php echo $this->id; ?>&categories"><?php i18n('CANCEL'); ?></a>
	</p>
</form>
