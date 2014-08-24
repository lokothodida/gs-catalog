<?php
  $classcurrent = array(
    'cfields'   => $_GET['options'] == 'cfields' ? 'class="current"' : null,
    'templates' => $_GET['options'] == 'templates' ? 'class="current"' : null,
    'cart'      => $_GET['options'] == 'cart' ? 'class="current"' : null,
    'general'   => $_GET['options'] == 'general' || empty($_GET['options']) ? 'class="current"' : null,
  );
?>

<h3 class="floated"><?php i18n($this->id . '/OPTIONS'); ?></h3>
<div class="edit-nav clearfix">
  <a href="load.php?id=<?php echo $this->id; ?>&options=cart" <?php echo $classcurrent['cart']; ?>><?php i18n($this->id . '/CART'); ?></a>
	<a href="load.php?id=<?php echo $this->id; ?>&options=cfields" <?php echo $classcurrent['cfields']; ?>><?php i18n($this->id . '/CUSTOM_FIELDS'); ?></a>
	<a href="load.php?id=<?php echo $this->id; ?>&options=templates" <?php echo $classcurrent['templates']; ?>><?php i18n($this->id . '/TEMPLATES'); ?></a>
	<a href="load.php?id=<?php echo $this->id; ?>&options=general" <?php echo $classcurrent['general']; ?>><?php i18n($this->id . '/GENERAL'); ?></a>
</div>

<form action="" method="post" onsubmit="return validateOptions();">
  <?php
    if ($_GET['options'] == 'cfields') {
      include('options_customfields.php');
    }
    elseif ($_GET['options'] == 'templates') {
      include('options_templates.php');
    }
    elseif ($_GET['options'] == 'cart') {
      include('options_cart.php');
    }
    else {
      include('options_general.php');
    }

  ?>
  <p id="submit_line">
		<span><input class="submit" name="submitted" value="<?php i18n('BTN_SAVECHANGES'); ?>" type="submit"></span>
	</p>
</form>
