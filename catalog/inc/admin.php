<?php
  // reset options
  if ($_GET['home'] == 'resetoptions') {
    $succ = $this->makeDefaultFiles(true);
    if (!in_array(false, $succ)) {
      $msg = i18n_r($this->id . '/RESET_OP_SUCC');
      $isSuccess = true;
    }
    else {
      $msg = i18n_r($this->id . '/RESET_OP_FAIL');
      $isSuccess = false;
    }
  }
  // clear the catalog
  elseif ($_GET['home'] == 'clearcatalog') {
    $succ = $this->clearCatalog();
    if (!in_array(false, $succ)) {
      $msg = i18n_r($this->id . '/CLEAR_CAT_SUCC');
      $isSuccess = true;
    }
    else {
      $msg = i18n_r($this->id . '/CLEAR_CAT_FAIL');
      $isSuccess = false;
    }
  }
?>

<h3><?php i18n($this->id . '/HOME'); ?></h3>
<ul>
  <li>
    <a href="<?php echo $GLOBALS['SITEURL'] . $general->slug; ?>/" target="_blank"><?php i18n($this->id . '/CATALOG'); ?></a>
  </li>
  <li>
    <a href="load.php?id=<?php echo $this->id; ?>&home=resetoptions" onclick="resetOptions(); return false;"><?php i18n($this->id . '/RESET_OPTIONS'); ?></a>
  </li>
  <li>
    <a href="load.php?id=<?php echo $this->id; ?>&home=clearcatalog" onclick="clearCatalog(); return false;"><?php i18n($this->id . '/CLEAR_CATALOG'); ?></a>
  </li>
</ul>

<script>
  function resetOptions(obj) {
    if (confirm("<?php i18n($this->id . '/RESET_OP_SURE'); ?>")) {
      window.location = obj.getAttribute('href');
    }
  }
  function clearCatalog(obj) {
    if (confirm("<?php i18n($this->id . '/CLEAR_CAT_SURE'); ?>")) {
      window.location = obj.getAttribute('href');
    }
  }
</script>
