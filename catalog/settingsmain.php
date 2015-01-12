<h3 class="floated"><?php i18n('catalog/SETTINGS'); ?></h3>
<div class="edit-nav">
  <p>
    <a href="<?php echo CATALOGADMINURL; ?>&settings=cart"><?php i18n('catalog/CART'); ?></a>
    <a href="<?php echo CATALOGADMINURL; ?>&settings=fields"><?php i18n('catalog/FIELDS'); ?></a>
    <a href="<?php echo CATALOGADMINURL; ?>&settings=theme"><?php i18n('catalog/THEME'); ?></a>
    <a href="<?php echo CATALOGADMINURL; ?>&settings" class="current"><?php i18n('catalog/SETTINGS'); ?></a>
  </p>
  <div class="clear"></div>
</div>

<script type="text/javascript" src="../plugins/catalog/js/jquery.litetabs.min.js"></script>
<script>
$(document).ready(function() {
  $('#tab-container').liteTabs({ width: '100%'});
});
</script>

<style>
ul.etabs { margin: 0 !important; padding: 0 !important; overflow: hidden; }
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

<form method="post">
  <input type="hidden" name="editMain"/>
  <p>
  <input type="text" class="text title" name="title" placeholder="<?php i18n('catalog/TITLE'); ?>" value="<?php echo $settings['title']; ?>"/>
  </p>
  <h3><?php i18n('catalog/GENERAL'); ?></h3>
  <div class="leftsec">
  <p>
  <label for="slug"><?php i18n('catalog/SLUG'); ?>: </label>
  <input class="text" id="slug" name="slug" value="<?php echo $settings['slug']; ?>" type="text">
  </p>

  <?php
  $template = $settings['template'];
  $theme_templates = '';
  global $TEMPLATE;
  // MAKE SELECT BOX OF AVAILABLE TEMPLATES
  if ($template == '') { $template = 'template.php'; }

  $themes_path = GSTHEMESPATH . $TEMPLATE;
  $themes_handle = opendir($themes_path) or die("Unable to open ". GSTHEMESPATH);		
  while ($file = readdir($themes_handle))	{		
    if( isFile($file, $themes_path, 'php') ) {		
      if ($file != 'functions.php' && substr(strtolower($file),-8) !='.inc.php' && substr($file,0,1)!=='.') {		
        $templates[] = $file;		
      }		
    }		
  }		
      
  sort($templates);

  foreach ($templates as $file){
    if ($template == $file)	{ 
      $sel="selected"; 
    } else{ 
      $sel=""; 
    }
    
    if ($file == 'template.php'){ 
      $templatename=i18n_r('DEFAULT_TEMPLATE'); 
    } else { 
      $templatename=$file;
    }
    
    $theme_templates .= '<option '.$sel.' value="'.$file.'" >'.$templatename.'</option>';
  }
  ?>
  <p class="inline">
    <label for="template"><?php i18n('catalog/TEMPLATE'); ?>: </label>
    <select class="text" name="template">
      <?php echo $theme_templates; ?>
    </select>
  </p>
  
  <p class="inline">
  <input type="checkbox" name="wysiwyg" value="y" <?php if ($settings['wysiwyg'] == 'y') echo 'checked="checked"'; ?>>
  <label for="wysiwyg"><?php i18n('ENABLE_HTML_ED'); ?></label>
  <p>
    <label for="wysiwyg"><?php i18n('catalog/WYSIWYG_TOOLBAR'); ?>: </label>
    <select class="text" name="wysiwygtoolbar">
      <?php foreach (array('basic', 'advanced') as $wysiwygtoolbar) : ?>
      <option value="<?php echo $wysiwygtoolbar; ?>" <?php if ($settings['wysiwygtoolbar'] === $wysiwygtoolbar) echo 'selected'; ?>>
      <?php i18n('catalog/' . strtoupper($wysiwygtoolbar)); ?>
      </option>
      <?php endforeach; ?>
    </select>
  </p>
  </div>
  <div class="rightsec">
  <p class="inline">
  <input type="checkbox" name="slugged" value="y" <?php if ($settings['slugged'] == 'y') echo 'checked="checked"'; ?>>
  <label for="i18nsearch"><?php i18n('catalog/SLUGGED'); ?></label>
  </p>
  <p class="inline">
  <input type="checkbox" name="internalsearch" value="y" <?php if ($settings['internalsearch'] == 'y') echo 'checked="checked"'; ?>>
  <label for="internalsearch"><?php i18n('catalog/INTERNAL_SEARCH'); ?></label>
  </p>
  <p class="inline">
  <input type="checkbox" name="i18nsearch" value="y" <?php if ($settings['i18nsearch'] == 'y') echo 'checked="checked"'; ?>>
  <label for="i18nsearch"><?php i18n('catalog/I18N_SEARCH'); ?></label>
  </p>
  </div>
  <div class="clear"></div>
  <div class="leftsec">
  <h3><?php i18n('catalog/PRODUCTS'); ?></h3>
  <p>
  <label for="productsperpage"><?php i18n('catalog/PRODUCTS_PER_PAGE'); ?>: </label>
  <input class="text" id="productsperpage" name="productsperpage" value="<?php echo $settings['productsperpage']; ?>" type="number">
  </p>
  <p>
  <label for="pagination"><?php i18n('catalog/PAGINATION'); ?>: </label>
  <select class="text" name="pagination">
  <option value="top" <?php if ($settings['pagination'] == 'top') echo 'selected'; ?>>
  <?php i18n('catalog/TOP'); ?>
  </option>
  <option value="bottom" <?php if ($settings['pagination'] == 'bottom') echo 'selected'; ?>>
  <?php i18n('catalog/BOTTOM'); ?>
  </option>
  <option value="both" <?php if ($settings['pagination'] == 'both') echo 'selected'; ?>>
  <?php i18n('catalog/BOTH'); ?>
  </option>
  </select>
  </p>
  <?php if (CatalogBackEnd::i18nExists()) : ?>
  <p>
  <label for="languages"><?php i18n('catalog/LANGUAGES'); ?>: </label>
  <?php
  $languages = implode("\n", $settings['languages']);
  ?>
  <textarea class="text" name="languages" style="width: 94%; height: 80px;"><?php echo $languages; ?></textarea>
  </p>
  <?php endif; ?>
  </div>
  <div class="rightsec">
  <h3><?php i18n('catalog/CATEGORIES'); ?></h3>
  <p>
  <label for="categoryview"><?php i18n('catalog/CATEGORY_VIEW'); ?>: </label>
  <select class="text" name="categoryview">
  <option value="all" <?php if ($settings['categoryview'] == 'all') echo 'selected'; ?>>
  <?php i18n('catalog/VIEW_ALL'); ?>
  </option>
  <option value="nested" <?php if ($settings['categoryview'] == 'nested') echo 'selected'; ?>>
  <?php i18n('catalog/HIERARCHICAL'); ?>
  </option>
  <option value="parents" <?php if ($settings['categoryview'] == 'parents') echo 'selected'; ?>>
  <?php i18n('catalog/PARENTS_ONLY'); ?>
  </option>
  </select>
  </p>
  </div>
  <div class="clear"></div>
  <h3><?php i18n('catalog/ERRORS'); ?></h3>
  
  <div id="tab-container" class="tab-container">
    <ul class='etabs'>
      <li class='tab'><a href="#tabs1-pageerror">404</a></li>
      <li class='tab'><a href="#tabs1-categoryerror"><?php i18n('catalog/CATEGORIES'); ?></a></li>
      <li class='tab'><a href="#tabs1-producterror"><?php i18n('catalog/PRODUCTS'); ?></a></li>
      <li class='tab'><a href="#tabs1-noresults"><?php i18n('catalog/NO_RESULTS'); ?></a></li>
    </ul>

      <div name="#tabs1-pageerror">
        <textarea id="pageerror" name="pageerror" style="height: 200px;"><?php echo $settings['pageerror']; ?></textarea>
        <?php $ckeditorId = 'pageerror'; $height = 200; include('ckeditor.php'); ?>
      </div>

      <div name="#tabs1-categoryerror">
        <textarea id="categoryerror" name="categoryerror" style="height: 200px;"><?php echo $settings['categoryerror']; ?></textarea>
        <?php $ckeditorId = 'categoryerror'; $height = 200; include('ckeditor.php'); ?>
      </div>

      <div name="#tabs1-producterror">
        <textarea id="producterror" name="producterror" style="height: 200px;"><?php echo $settings['producterror']; ?></textarea>
        <?php $ckeditorId = 'producterror'; $height = 200; include('ckeditor.php'); ?>
      </div>

      <div name="#tabs1-noresults">
        <textarea id="noresults" name="noresults" style="height: 200px;"><?php echo $settings['noresults']; ?></textarea>
        <?php $ckeditorId = 'noresults'; $height = 200; include('ckeditor.php'); ?>
      </div>

  </div>

  <div id="submit_line">
    <span><input id="page_submit" class="submit" name="submitted" value="<?php i18n('BTN_SAVECHANGES'); ?>" type="submit"></span>
    /
    <a href="<?php echo $cancelUrl; ?>" class="cancel"><?php i18n('CANCEL'); ?></a>
  </div>
</form>