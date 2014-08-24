<?php
  // save changes
  if (isset($_POST['submitted'])) {
    $xml = new SimpleXMLExtended('<category/>');
    $fields = array('title', 'category', 'photo', 'description');
    foreach ($fields as $field) {
      if (isset($_POST[$field])) {
        $xml->{$field} = null;
        if ($field == 'photo') {
          foreach ($_POST[$field] as $k => $v) $xml->photos->photo[$k] = $v;
        }
        else {
          $xml->{$field}->addCData($_POST[$field]);
        }
      }
    }
    $succ = (bool) $xml->saveXML(GSDATAOTHERPATH . $this->id . '/categories/' . $_GET['categories'] . '.xml');
  
    if ($succ) {
      $this->deleteI18nSearchIndex();
      $msg = i18n_r($this->id . '/CAT_EDIT_SUCC');
      $isSuccess = true;
    }
    else {
      $msg = i18n_r($this->id . '/CAT_EDIT_FAIL');
      $isSuccess = false;
    }
  }
  
  $categories = new CatalogCategories(GSDATAOTHERPATH . $this->id . '/categories/*.xml', $catalogurl, $slugged);
  $categories = $categories->getCategories();
  $category   = $categories[$_GET['categories']];

?>
<h3><?php i18n($this->id . '/EDIT_CATEGORY'); ?></h3>
<form action="" method="post">
  <?php include('category_form.php'); ?>
  <p id="submit_line">
		<span><input class="submit" name="submitted" value="<?php i18n('BTN_SAVECHANGES'); ?>" type="submit"></span>
		 &nbsp;&nbsp;<?php i18n('OR'); ?>&nbsp;&nbsp; <a class="cancel" href="load.php?id=<?php echo $this->id; ?>&categories"><?php i18n('CANCEL'); ?></a>
	</p>
</form>
