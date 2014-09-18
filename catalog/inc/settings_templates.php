</form><!--close off previous form-->
<style>
  .CodeMirror {
    height: 200px;
  }
  .CodeMirror-scroll {
    overflow-y: auto;
    overflow-x: auto;
    height: 200px;
  }
  .import {
    font: bold 13px Helvetica, Arial, sans-serif;
    text-decoration: none !important;
    padding: 5px;
    text-shadow: 0px 1px 0px rgba(255, 255, 255, 0.5);
    transition: all 0.218s ease 0s;
    color: #333 !important;
    background: #fff;
    border: 1px solid #ACACAC;
    border-radius: 2px;
    cursor: pointer;
    box-shadow: 0px 0px 3px rgba(0, 0, 0, 0.06);
  }
</style>
<?php
  // activate a theme
  if (isset($_GET['activate'])) {
    $file = $this->dataDir . '/templates.xml';
    $template = $this->dataDir . '/templates/' . $_GET['activate'] . '.xml';

    if (file_exists($file)) {
      $xml = new SimpleXMLExtended($file, 0, true);

      $xml->current = null;
      $xml->current->addCData($_GET['activate']);

      $xml->saveXML($file);
    } else {
      // theme/template does not exist
    }
  }

  // importing a template
  if (!empty($_FILES)) {
    $upload = $_FILES['importFile'];

    if ($upload['type'] == 'text/xml') {
      $filename = $upload['name'];
      $themename = basename($filename, '.xml');
      $destination = $this->dataDir . '/templates/' . $filename;

      if (!file_exists($destination)) {
        move_uploaded_file($upload['tmp_name'], $this->dataDir . '/themes/' . $filename);

        $msg = str_replace('%s', '<strong>' . $themename . '</strong>', i18n_r($this->id . '/OPTIONS_UPLOAD_THEME_SUCC'));
        $isSuccess = true;
      } else {
        $msg = i18n_r($this->id . '/OPTIONS_UPLOAD_THEME_FAIL_NAME');
        $isSuccess = false;
      }
    } else {
      $msg = i18n_r($this->id . '/OPTIONS_UPLOAD_THEME_FAIL_TYPE');
      $isSuccess = false;
    }
  }

  // exporting a template for download
  if (isset($_GET['export'])) {
    $template = $_GET['export'] . '.xml';
    $templatesDir = $this->dataDir . '/templates/' . $template;
    $tmpDir = $this->dataDir . '/tmp/' . $template;

    copy($templatesDir, $tmpDir);

    $exportFile = str_replace(GSROOTPATH, $GLOBALS['SITEURL'], $tmpDir);
  }

  // saving options
  if (isset($_POST['submitted'])) {
    $fields = array('main', 'header', 'category', 'product', 'featured', 'i18nsearch-product', 'footer');
    $xml = new SimpleXMLExtended('<theme/>');
    foreach ($fields as $k => $field) {
      $xml->template[$k] = null;
      $xml->template[$k]->addAttribute('name', $field);
      $xml->template[$k]->addCData($_POST[$field]);
    }
    $succ = (bool) $xml->saveXML($this->dataDir . '/themes/' . $_POST['current'] . '.xml');
    
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

  $general   = new CatalogSettingsGeneral($this->dataDir . '/general.xml');
  $theme = new CatalogSettingsTheme(array('file' => $this->dataDir . '/themes.xml', 'directory' => $this->dataDir . '/themes/'));
  //$themes    = $templates->getThemes();
  //$current   = $templates->getCurrentTheme();
  //$templates = $templates->getTemplates();
?>
<!--select theme/template-->
<?php if (isset($_GET['export']) && isset($template)) : ?>
  <script>
  $(document).ready(function() {
    window.open('<?php echo $exportFile; ?>');
  });
  </script>
<?php endif; ?>
<form action="" method="post" enctype="multipart/form-data">
  <p>
    <select id="theme_select" class="text" style="width:200px;" name="template">
      <?php foreach ($themes as $theme) : ?>
      <option <?php if ($theme == $current) echo 'selected="selected"'; ?>value="<?php echo $theme; ?>"><?php echo $theme; ?></option>
      <?php endforeach; ?>
    </select>&nbsp;&nbsp;&nbsp;
    <input class="submit activate" name="submitted" value="<?php i18n('ACTIVATE_THEME'); ?>" type="submit">
  </p>
  <p>
    <input class="import" name="importFile" value="<?php i18n($this->id . '/IMPORT'); ?>" type="file">
    <input class="submit" name="import" value="<?php i18n($this->id . '/IMPORT'); ?>" type="submit">
    <input class="submit export" name="export" value="<?php i18n($this->id . '/EXPORT'); ?>" type="submit">
  </p>
</form>

<script>
$(document).ready(function() {
  $('.activate').click(function() {
    window.location = '<?php echo $adminUrl; ?>&options=templates&activate=' + $('#theme_select').val();
    return false;
  });
  $('.export').click(function() {
    window.location = '<?php echo $adminUrl; ?>&options=templates&export=' + $('#theme_select').val();
    return false;
  });
});
</script>
<form action="" method="post">
<input type="hidden" name="current" value="<?php echo $current; ?>"/>

<!-- header -->
<p>
  <h4 style="font-weight: bold;"><?php i18n($this->id . '/HEADER'); ?> : </h4>
  <textarea id="t-header" name="header" style="height: 200px !important;"><?php echo $theme->get('header'); ?></textarea>
  <?php $textarea = 't-header'; include('codemirror.php'); ?>
</p>


<!-- index (header) -->
<p>
  <h4 style="font-weight: bold;"><?php i18n($this->id . '/INDEX_HEADER'); ?> : </h4>
  <textarea id="indexHeader" name="indexHeader" style="height: 200px !important;"><?php echo $theme->get('indexHeader'); ?></textarea>
  <?php $textarea = 'indexHeader'; include('codemirror.php'); ?>
</p>

<!-- index (categories loop) -->
<p>
  <h4 style="font-weight: bold;"><?php i18n($this->id . '/INDEX_CATEGORIES'); ?> : </h4>
  <textarea id="indexCategories" name="indexCategories" style="height: 200px !important;"><?php echo $theme->get('indexCategories'); ?></textarea>
  <?php $textarea = 'indexCategories'; include('codemirror.php'); ?>
</p>

<!-- index (footer) -->
<p>
  <h4 style="font-weight: bold;"><?php i18n($this->id . '/INDEX_FOOTER'); ?> : </h4>
  <textarea id="indexFooter" name="indexFooter" style="height: 200px !important;"><?php echo $theme->get('indexFooter'); ?></textarea>
  <?php $textarea = 'indexFooter'; include('codemirror.php'); ?>
</p>

<!-- category (showing its products) -->
<p>
  <h4 style="font-weight: bold;"><?php i18n($this->id . '/CATEGORY'); ?> : </h4>
  <textarea id="category" name="category" style="height: 200px !important;"><?php echo $theme->get('category'); ?></textarea>
  <?php $textarea = 'category'; include('codemirror.php'); ?>
</p>

<!--product-->
<p>
  <h4 style="font-weight: bold;"><?php i18n($this->id . '/PRODUCT'); ?> : </h4>
  <textarea id="product" name="product" style="height: 200px !important;"><?php echo $theme->get('product'); ?></textarea>
  <?php $textarea = 'product'; include('codemirror.php'); ?>
</p>

<!--search item (product)-->
<p>
  <h4 style="font-weight: bold;"><?php i18n($this->id . '/SEARCH_ITEM'); ?> : </h4>
  <textarea id="searchProduct" name="searchProduct" style="height: 200px !important;"><?php echo $theme->get('searchProduct'); ?></textarea>
  <?php $textarea = 'searchProduct'; include('codemirror.php'); ?>
</p>

<!--footer-->
<p>
  <h4 style="font-weight: bold;"><?php i18n($this->id . '/FOOTER'); ?> : </h4>
  <textarea id="footer" name="footer" style="height: 200px !important;"><?php echo $theme->get('footer'); ?></textarea>
  <?php $textarea = 'footer'; include('codemirror.php'); ?>
</p>
