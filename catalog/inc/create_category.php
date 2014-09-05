<?php
  // load categories
  $categoriesParams = array(
    'wildcard' => $this->dataDir . '/categories/*.xml',
    'settings' => $this->settings,
  );

  $categoryParams = array(
    'file' => null,
    'settings' => $this->settings,
  );

  $categories = new CatalogCategories($categoriesParams);
  $categories = $categories->getCategories();
  $category   = new CatalogCategory($categoryParams);
?>
<h3><?php i18n($this->id . '/CREATE_CATEGORY'); ?></h3>
<form action="<?php echo $this->adminUrl; ?>&categories" method="post">
  <?php include('category_form.php'); ?>
  <p id="submit_line">
    <span><input class="submit" name="submitted" value="<?php i18n('BTN_SAVECHANGES'); ?>" type="submit"></span>
     &nbsp;&nbsp;<?php i18n('OR'); ?>&nbsp;&nbsp; <a class="cancel" href="<?php echo $this->adminUrl; ?>&categories"><?php i18n('CANCEL'); ?></a>
  </p>
</form>
