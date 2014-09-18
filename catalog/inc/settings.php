<?php
  $classcurrent = array(
    'cfields'   => $_GET['settings'] == 'cfields' ? 'class="current"' : null,
    'templates' => $_GET['settings'] == 'templates' ? 'class="current"' : null,
    'cart'      => $_GET['settings'] == 'cart' ? 'class="current"' : null,
    'general'   => $_GET['settings'] == 'general' || empty($_GET['settings']) ? 'class="current"' : null,
  );
?>

<h3 class="floated"><?php i18n($this->id . '/OPTIONS'); ?></h3>
<div class="edit-nav clearfix">
  <a href="load.php?id=<?php echo $this->id; ?>&settings=cart" <?php echo $classcurrent['cart']; ?>><?php i18n($this->id . '/CART'); ?></a>
  <a href="load.php?id=<?php echo $this->id; ?>&settings=cfields" <?php echo $classcurrent['cfields']; ?>><?php i18n($this->id . '/CUSTOM_FIELDS'); ?></a>
  <a href="load.php?id=<?php echo $this->id; ?>&settings=templates" <?php echo $classcurrent['templates']; ?>><?php i18n($this->id . '/TEMPLATES'); ?></a>
  <a href="load.php?id=<?php echo $this->id; ?>&settings=general" <?php echo $classcurrent['general']; ?>><?php i18n($this->id . '/GENERAL'); ?></a>
</div>

<form action="" method="post" onsubmit="return validateOptions();">
  <?php
    if ($_GET['settings'] == 'cfields') {
      include('settings_customfields.php');
    }
    elseif ($_GET['settings'] == 'templates') {
      include('settings_templates.php');
    }
    elseif ($_GET['settings'] == 'cart') {
      include('settings_cart.php');
    }
    else {
      include('settings_general.php');
    }

  ?>
  <p id="submit_line">
    <span><input class="submit" name="submitted" value="<?php i18n('BTN_SAVECHANGES'); ?>" type="submit"></span>
  </p>
</form>
