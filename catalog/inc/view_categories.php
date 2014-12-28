<?php

  // create new category
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

    // save the file if name doesn't cause a clash
    $identifiers = simplexml_load_file($this->dataDir . '/identifiers.xml');

    $slug = $this->setup->toSlug($_POST['slug']);
    $title = $this->setup->toSlug($_POST['title']);
    $name = empty($slug) ? $title : $slug;
    $filename = $this->dataDir . '/categories/' . $identifiers->categories . '-' . $name . '.xml';

    if ($name && !file_exists($filename)) {
      $succ = (bool) $xml->saveXML($filename);
      $identifiers->categories = ((int) $identifiers->categories) + 1;
      $identifiers->saveXML($this->dataDir . '/identifiers.xml');
    } else {
      $msg = i18n_r($this->id . '/CAT_NAMEEXISTS');
      $succ = false;
    }
    
    // success
    if ($succ) {
      $this->setup->deleteI18nSearchIndex();
      $msg = i18n_r($this->id . '/CAT_CREATE_SUCC');
      $isSuccess = true;
    }
    // fail
    else {
      if (!isset($msg)) $msg = i18n_r($this->id . '/CAT_CREATE_FAIL');
      $isSuccess = false;
    }
  }
  // save new order
  if (isset($_POST['saveorder']) && isset($_POST['name'])) {
    $succ = array();
    foreach ($_POST['name'] as $order => $category) {
      $file = $this->dataDir . '/categories/' . $category . '.xml';
      if ($file) {
        $xml = new SimpleXMLExtended($file, 0, true);
        $xml->order = $order;
        $succ[] = (bool) $xml->saveXML($file);
      }
      else {
        $succ[] = false;
      }
    }
    // success
    if (!in_array(false, $succ)) {
      $this->setup->deleteI18nSearchIndex();
      $msg = i18n_r($this->id . '/CAT_ORDER_SUCC');
      $isSuccess = true;
    }
    // fail
    else {
      $msg = i18n_r($this->id . '/CAT_ORDER_FAIL');
      $isSuccess = false;
    }
  }
  // delete category
  if (isset($_GET['delete'])) {
    $file = $this->dataDir . '/categories/' . $_GET['delete'] . '.xml';
    if (file_exists($file)) {
      $succ = (bool) unlink($file);
    }
    else $succ = false;
    
    if ($succ) {
      $this->setup->deleteI18nSearchIndex();
      $msg = i18n_r($this->id . '/CAT_DEL_SUCC');
      $isSuccess = true;
    }
    else {
      $msg = i18n_r($this->id . '/CAT_DEL_FAIL');
      $isSuccess = false;
    }
  }

  // load categories
  $categoriesParams = array(
    'wildcard' => $this->dataDir . '/categories/*.xml',
    'settings' => $this->settings,
  );

  $getCategoriesParams = array(
    'sort' => 'parents',
  );

  $categories = new CatalogCategories($categoriesParams);
  $cats = $categories->getCategories($getCategoriesParams);
  $trail = new CatalogCategoriesTrail($cats);
  $trail = $trail->getTrail();
  var_dump($trail);
?>

<h3 class="floated"><?php i18n($this->id . '/CATEGORIES'); ?></h3>
<div class="edit-nav clearfix">
  <a href="<?php echo $this->adminUrl; ?>&categories=create"><?php i18n($this->id . '/CREATE_CATEGORY'); ?></a>
</div>

<form action="" method="post">
  <table class="highlight edittable" id="viewcategories">
    <thead>
      <tr>
        <th width="5%"><?php i18n($this->id . '/ID'); ?></th>
        <th width="20%"><?php i18n($this->id . '/SLUG'); ?></th>
        <th width="70%"><?php i18n($this->id . '/NAME'); ?></th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($cats as $category) :
        $id   = $category->getField('id', true);
        $slug = $id['slug'];
        $id   = $id['numeric'];
      ?>
      <tr>
        <td>
          <?php echo $id; ?>
          <input type="hidden" name="name[]" value="<?php echo $category->getField('id'); ?>">
        </td>
        <td>
          <?php echo $slug; ?>
        </td>
        <td>
          <span style="padding-left: <?php echo count($trail[$category->getField('id')]) * 5; ?>px;">
            <a href="<?php echo $this->adminUrl; ?>&categories=<?php echo $category->getField('id'); ?>"><?php echo $category->getField('title'); ?></a>
          </span>
        </td>
        <td style="text-align: right;">
          <a href="<?php echo $category->getField('url'); ?>" target="_blank">#</a>
          <a href="<?php echo $this->adminUrl; ?>&categories&delete=<?php echo $category->getField('id'); ?>" class="cancel" onclick="deleteCategory(); return false;">x</a>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($cats)) : ?>
      <tr>
        <td colspan="100%"><?php i18n($this->id . '/NO_CATEGORIES'); ?></th>
      </tr>
      <?php endif; ?>
    </tbody>
  </table>
  
  <p id="submit_line">
    <span><input class="submit" name="saveorder" value="<?php i18n('BTN_SAVECHANGES'); ?>" type="submit"></span>
  </p>
</form>

<script>
  $('#viewcategories tbody').sortable();
  function deleteCategory(obj) {
    //var msg = confirm("<?php i18n($this->id . '/DELCAT_AREYOUSURE'); ?>");
    if (confirm("<?php i18n($this->id . '/DELCAT_YOUSURE'); ?>")) {
      window.location = obj.getAttribute('href');
    }
  }
</script>
