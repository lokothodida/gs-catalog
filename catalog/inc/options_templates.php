<style>
  .CodeMirror {
    height: 200px;
  }
  .CodeMirror-scroll {
    overflow-y: auto;
    overflow-x: auto;
    height: 200px;
  }
</style>
<?php
  // saved
  if (isset($_POST['submitted'])) {
    $fields = array('main', 'header', 'category', 'product', 'featured', 'i18nsearch-product', 'footer');
    $xml = new SimpleXMLExtended('<templates/>');
    foreach ($fields as $k => $field) {
      $xml->template[$k] = null;
      $xml->template[$k]->addAttribute('name', $field);
      $xml->template[$k]->addCData($_POST[$field]);
    }
    $succ = (bool) $xml->saveXML(GSDATAOTHERPATH . $this->id . '/templates.xml');
    
    // success
    if ($succ) {
      $msg = i18n_r($this->id . '/OPTIONS_UPD_SUCC');
      $isSuccess = true;
    }
    // error
    else {
      $msg = i18n_r($this->id . '/OPTIONS_UPD_FAIL');
      $isSuccess = false;
    }
  }
  $general = new CatalogGeneralOptions(GSDATAOTHERPATH . $this->id . '/general.xml');
  $templates = new CatalogTemplates(GSDATAOTHERPATH . $this->id . '/templates.xml');
  $templates = $templates->getTemplates();

?>

<!-- main catalog page template -->
<p>
  <h4 style="font-weight: bold;"><?php i18n($this->id . '/MAIN'); ?> : </h4>
  <textarea id="main" name="main" style="height: 200px !important;"><?php echo $templates['main']; ?></textarea>
  <?php $textarea = 'main'; include('codemirror.php'); ?>
</p>
<!--header template-->
<p>
  <h4 style="font-weight: bold;"><?php i18n($this->id . '/HEADER'); ?> : </h4>
  <textarea id="t-header" name="header" style="height: 200px !important;"><?php echo $templates['header']; ?></textarea>
  <?php $textarea = 't-header'; include('codemirror.php'); ?>
</p>
<!--category-->
<p>
  <h4 style="font-weight: bold;"><?php i18n($this->id . '/CATEGORY'); ?> : </h4>
  <textarea id="category" name="category" style="height: 200px !important;"><?php echo $templates['category']; ?></textarea>
  <?php $textarea = 'category'; include('codemirror.php'); ?>
</p>
<!--product-->
<p>
  <h4 style="font-weight: bold;"><?php i18n($this->id . '/PRODUCT'); ?> : </h4>
  <textarea id="product" name="product" style="height: 200px !important;"><?php echo $templates['product']; ?></textarea>
  <?php $textarea = 'product'; include('codemirror.php'); ?>
</p>
<?php if ($general->getI18nSearch() == 'y') : ?>
  <!--search product-->
  <p>
    <h4 style="font-weight: bold;"><?php i18n($this->id . '/I18N_SEARCH_PROD'); ?> : </h4>
    <textarea id="featured" name="i18nsearch-product" style="height: 200px !important;"><?php echo $templates['i18nsearch-product']; ?></textarea>
    <?php $textarea = 'i18nsearch-product'; include('codemirror.php'); ?>
  </p>
<?php endif; ?>
<!--footer template-->
<p>
  <h4 style="font-weight: bold;"><?php i18n($this->id . '/FOOTER'); ?> : </h4>
  <textarea id="t-footer" name="footer" style="height: 200px !important;"><?php echo $templates['footer']; ?></textarea>
  <?php $textarea = 't-footer'; include('codemirror.php'); ?>
</p>
<!--featured products-->
<p>
  <h4 style="font-weight: bold;"><?php i18n($this->id . '/FEATURED'); ?> : </h4>
  <textarea id="featured" name="featured" style="height: 200px !important;"><?php echo $templates['featured']; ?></textarea>
  <?php $textarea = 'featured'; include('codemirror.php'); ?>
</p>
