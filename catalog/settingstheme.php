<h3 class="floated"><?php i18n('catalog/THEME'); ?></h3>
<div class="edit-nav">
  <p>
    <a href="<?php echo CATALOGADMINURL; ?>&settings=cart"><?php i18n('catalog/CART'); ?></a>
    <a href="<?php echo CATALOGADMINURL; ?>&settings=fields"><?php i18n('catalog/FIELDS'); ?></a>
    <a href="<?php echo CATALOGADMINURL; ?>&settings=theme" class="current"><?php i18n('catalog/THEME'); ?></a>
    <a href="<?php echo CATALOGADMINURL; ?>&settings"><?php i18n('catalog/SETTINGS'); ?></a>
  </p>
  <div class="clear"></div>
</div>

<script type="text/javascript" src="../plugins/catalog/js/jquery.litetabs.min.js"></script>
<script>
$(document).ready(function() {
  $('.activate').click(function() {
    window.location = '<?php echo CATALOGADMINURL; ?>&settings=theme&activate=' + $('#theme_select').val();
    return false;
  });
  $('.export').click(function() {
    window.location = '<?php echo CATALOGADMINURL; ?>&settings=theme&export=' + $('#theme_select').val();
    return false;
  });
  $('#tab-container').liteTabs({ width: '100%'});
  $('#change-theme').change(function() {
    var theme = $(this).val();
    window.location = '<?php echo CATALOGADMINURL; ?>&settings=theme&change=' + theme;
  });
});
</script>

<style>
ul.etabs {
  margin: 0 !important;
  padding: 4px !important;
  overflow: hidden;
}
.tab {
  display: block;
  float: left;
}
.tab a {
  font-size: 10px;
  text-transform: uppercase;
  display: block;
  padding: 3px 10px;
  float: right;
  margin: 0px 0px 5px 5px;
  border-radius: 3px;
  background-repeat: no-repeat;
  background-position: 94% center;


  line-height: 14px !important;
  background-color: #182227;
  color: #CCC !important;
  font-weight: bold;
  text-decoration: none !important;
  text-shadow: 1px 1px 0px rgba(0, 0, 0, 0.2);
  transition: all 0.1s ease-in-out 0s;
}
.tab a.selected, .tab a:hover {
  background-color: #CF3805;
  color: #FFF !important;
  font-weight: bold;
  text-decoration: none;
  line-height: 14px !important;
  text-shadow: 1px 1px 0px rgba(0, 0, 0, 0.2);
}

.liteTabs > div { width: 100%; }
.liteTabs > div.selected { position: static !important }

</style>

<p>
  <h3 style="font-size: 16px;"><?php i18n('catalog/CHANGE_THEME'); ?></h3>
  <select id="change-theme">
    <?php foreach ($themes as $theme) : ?>
    <option <?php if ($settings['current'] == $theme) echo 'selected'; ?>><?php echo $theme; ?></option>
    <?php endforeach; ?>
  </select>
</p>

<form action="" method="post">
  <h3 style="font-size: 16px;"><?php i18n('catalog/EDIT_CURRENT_THEME'); ?></h3>
  <input type="hidden" name="editTheme"/>
  <input type="hidden" name="current" value="<?php echo $settings['current']; ?>"/>

  <div id="tab-container" class="tab-container">
    <ul class='etabs'>
      <li class='tab'><a href="#tabs1-header"><?php i18n('catalog/HEADER'); ?></a></li>
      <li class='tab'><a href="#tabs1-indexheader">(<?php i18n('catalog/INDEX'); ?>) <?php i18n('catalog/HEADER'); ?></a></li>
      <li class='tab'><a href="#tabs1-indexfooter">(<?php i18n('catalog/INDEX'); ?>) <?php i18n('catalog/FOOTER'); ?></a></li>
      <li class='tab'><a href="#tabs1-indexcategories">(<?php i18n('catalog/INDEX'); ?>) <?php i18n('catalog/CATEGORIES'); ?></a></li>
      <li class='tab'><a href="#tabs1-category"><?php i18n('catalog/CATEGORY'); ?></a></li>
      <li class='tab'><a href="#tabs1-product"><?php i18n('catalog/PRODUCT'); ?></a></li>
      <li class='tab'><a href="#tabs1-searchresult"><?php i18n('catalog/SEARCH'); ?></a></li>
      <li class='tab'><a href="#tabs1-footer"><?php i18n('catalog/FOOTER'); ?></a></li>
      <li class='tab'><a href="#tabs1-css"><?php i18n('catalog/CSS'); ?></a></li>
    </ul>

    <!-- header -->
    <div name="#tabs1-header">
      <textarea id="t-header" name="header" style="height: 200px !important;"><?php echo $settings['header']; ?></textarea>
      <?php $codemirrorId = 't-header'; include('codemirror.php'); ?>
    </div>

    <!-- index (header) -->
    <div name="#tabs1-indexheader">
      <textarea id="indexHeader" name="indexheader" style="height: 200px !important;"><?php echo $settings['indexheader']; ?></textarea>
      <?php $codemirrorId = 'indexHeader'; include('codemirror.php'); ?>
    </div>

    <!-- index (categories loop) -->
    <div name="#tabs1-indexcategories">
      <textarea id="indexCategories" name="indexcategories" style="height: 200px !important;"><?php echo $settings['indexcategories']; ?></textarea>
      <?php $codemirrorId = 'indexCategories'; include('codemirror.php'); ?>
    </div>

    <!-- index (footer) -->
    <div name="#tabs1-indexfooter">
      <textarea id="indexFooter" name="indexfooter" style="height: 200px !important;"><?php echo $settings['indexfooter']; ?></textarea>
      <?php $codemirrorId = 'indexFooter'; include('codemirror.php'); ?>
    </div>

    <!-- category (showing its products) -->
    <div name="#tabs1-category">
      <textarea id="category" name="category" style="height: 200px !important;"><?php echo $settings['category']; ?></textarea>
      <?php $codemirrorId = 'category'; include('codemirror.php'); ?>
    </div>

    <!--product-->
    <div name="#tabs1-product">
      <textarea id="product" name="product" style="height: 200px !important;"><?php echo $settings['product']; ?></textarea>
      <?php $codemirrorId = 'product'; include('codemirror.php'); ?>
    </div>

    <!--search item (product)-->
    <div name="#tabs1-searchresult">
      <textarea id="searchProduct" name="searchresult" style="height: 200px !important;"><?php echo $settings['searchresult']; ?></textarea>
      <?php $codemirrorId = 'searchProduct'; include('codemirror.php'); ?>
    </div>

    <!--footer-->
    <div name="#tabs1-footer">
      <textarea id="footer" name="footer" style="height: 200px !important;"><?php echo $settings['footer']; ?></textarea>
      <?php $codemirrorId = 'footer'; include('codemirror.php'); ?>
    </div>

    <!--css-->
    <div name="#tabs1-css">
      <textarea id="css" name="css" style="height: 200px !important;"><?php echo $settings['css']; ?></textarea>
      <?php $codemirrorId = 'css'; include('codemirror.php'); ?>
    </div>

  </div>

  <div id="submit_line">
    <span><input id="page_submit" class="submit" name="submitted" value="<?php i18n('BTN_SAVECHANGES'); ?>" type="submit"></span>
    /
    <a href="<?php echo $cancelUrl; ?>" class="cancel"><?php i18n('CANCEL'); ?></a>
  </div>
</form>