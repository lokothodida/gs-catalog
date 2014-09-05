<?php
  // Set the current ID
  $currentId = isset($_POST['currentId']) ? $_POST['currentId'] : $_GET['categories'];

  // save changes
  if (isset($_POST['submitted'])) {
    $xml = new SimpleXMLExtended('<category/>');
    $fields = array('title', 'category', 'photo', 'description');
    foreach ($fields as $field) {
      if (isset($_POST[$field])) {
        $xml->{$field} = null;
        if ($field == 'photo') {
          foreach ($_POST[$field] as $k => $v) $xml->photos->photo[$k] = $v;
        } else {
          $xml->{$field}->addCData($_POST[$field]);
        }
      }
    }

    // renaming the category slug
    $prevId = explode('-', $_GET['categories']);
    $id = reset($prevId);
    $prevId = $this->setup->toSlug(implode('-', array_slice($prevId, 1)));

    if ($currentId != $_POST['slug']) {
      $currentId = $id . '-' . $_POST['slug'];
      unlink($this->dataDir . '/categories/' . $_POST['currentId'] . '.xml');
    }

    $succ = (bool) $xml->saveXML($this->dataDir . '/categories/' . $currentId . '.xml');

    if ($succ) {
      $this->setup->deleteI18nSearchIndex();
      $msg = i18n_r($this->id . '/CAT_EDIT_SUCC');
      $isSuccess = true;
    } else {
      $msg = i18n_r($this->id . '/CAT_EDIT_FAIL');
      $isSuccess = false;
    }
  }

  // load categories
  $categoriesParams = array(
    'wildcard' => $this->dataDir . '/categories/*.xml',
    'settings' => $this->settings,
  );

  $categoryParams = array(
    'file' => $this->dataDir . '/categories/' . $currentId . '.xml',
    'settings' => $this->settings,
  );

  $categories = new CatalogCategories($categoriesParams);
  $categories = $categories->getCategories();
  $category   = new CatalogCategory($categoryParams);
?>
<h3><?php i18n($this->id . '/EDIT_CATEGORY'); ?></h3>
<form action="" method="post">
  <?php include('category_form.php'); ?>
  <p id="submit_line">
    <span><input class="submit" name="submitted" value="<?php i18n('BTN_SAVECHANGES'); ?>" type="submit"></span>
     &nbsp;&nbsp;<?php i18n('OR'); ?>&nbsp;&nbsp; <a class="cancel" href="load.php?id=<?php echo $this->id; ?>&categories"><?php i18n('CANCEL'); ?></a>
  </p>
</form>
