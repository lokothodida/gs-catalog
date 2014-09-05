<?php
  // saved
  if (isset($_POST['submitted'])) {
    $fields = array(
      'title',
      'slug',
      'productsperpage',
      'pagination',
      'view',
      'categoryview',
      'pageerror',
      'internalsearch',
      'i18nsearch',
      'slugged',
      // editor(s)
      'wysiwyg',
      'wysiwygtoolbar',
      );
    $xml = new SimpleXMLExtended('<options/>');
    foreach ($fields as $k => $field) {
      $xml->{$field} = null;
      if (isset($_POST[$field])) {
        $xml->{$field}->addCData($_POST[$field]);
      }
      else {
        $xml->{$field}->addCData(null);
      }
    }
    
    $succ = (bool) $xml->saveXML(GSDATAOTHERPATH . $this->id . '/general.xml');
    
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
?>

<style>
  form h3 {
    font-size: 15px;
    line-height: 20px;
  }
</style>

<p>
  <input type="text" class="text title" name="title" placeholder="<?php i18n($this->id . '/CATALOG_TITLE'); ?>" value="<?php echo $general->getTitle(); ?>"/>
</p>

<h3><?php i18n($this->id . '/GENERAL'); ?></h3>
<div class="leftsec">
  <p>
    <label for="slug"><?php i18n($this->id . '/SLUG'); ?>: </label>
    <input class="text" id="slug" name="slug" value="<?php echo $general->getSlug(); ?>" type="text">
  </p>
  <p class="inline">
    <input type="checkbox" name="wysiwyg" value="y" <?php if ($general->getWysiwyg() == 'y') echo 'checked="checked"'; ?>>
    <label for="wysiwyg"><?php i18n('ENABLE_HTML_ED'); ?></label>
  <p>
    <label for="wysiwyg"><?php i18n($this->id . '/WYSIWYG_TOOLBAR'); ?>: </label>
    <select class="text" name="wysiwygtoolbar">
      <?php foreach (array('basic', 'advanced') as $wysiwygtoolbar) : ?>
      <option value="<?php echo $wysiwygtoolbar; ?>" <?php if ($general->getWysiwygToolbar() === $wysiwygtoolbar) echo 'selected'; ?>>
        <?php i18n($this->id . '/' . strtoupper($wysiwygtoolbar)); ?>
      </option>
      <?php endforeach; ?>
    </select>
  </p>
</div>

<div class="rightsec">
  <p class="inline">
    <input type="checkbox" name="slugged" value="y" <?php if ($general->getSlugged() == 'y') echo 'checked="checked"'; ?>>
    <label for="i18nsearch"><?php i18n($this->id . '/SLUGGED'); ?></label>
  </p>
  <p class="inline">
    <input type="checkbox" name="internalsearch" value="y" <?php if ($general->getInternalSearch() == 'y') echo 'checked="checked"'; ?>>
    <label for="internalsearch"><?php i18n($this->id . '/INTERNAL_SEARCH'); ?></label>
  </p>
  <p class="inline">
    <input type="checkbox" name="i18nsearch" value="y" <?php if ($general->getI18nSearch() == 'y') echo 'checked="checked"'; ?>>
    <label for="i18nsearch"><?php i18n($this->id . '/I18N_SEARCH'); ?></label>
  </p>
</div>

<div class="clear"></div>

<div class="leftsec">
  <h3><?php i18n($this->id . '/PRODUCTS'); ?></h3>
  <p>
    <label for="productsperpage"><?php i18n($this->id . '/PROD_PERPAGE'); ?>: </label>
    <input class="text" id="productsperpage" name="productsperpage" value="<?php echo $general->getProductsPerPage(); ?>" type="number">
  </p>
  <p>
    <label for="pagination"><?php i18n($this->id . '/PAGINATION'); ?>: </label>
    <select class="text" name="pagination">
      <option value="top" <?php if ($general->getPagination() == 'top') echo 'selected'; ?>>
        <?php i18n($this->id . '/TOP'); ?>
      </option>
      <option value="bottom" <?php if ($general->getPagination() == 'bottom') echo 'selected'; ?>>
        <?php i18n($this->id . '/BOTTOM'); ?>
      </option>
      <option value="both" <?php if ($general->getPagination() == 'both') echo 'selected'; ?>>
        <?php i18n($this->id . '/BOTH'); ?>
      </option>
    </select>
  </p>
</div>

<div class="rightsec">
  <h3><?php i18n($this->id . '/CATEGORIES'); ?></h3>
  <p>
    <label for="categoryview"><?php i18n($this->id . '/CATEGORY_VIEW'); ?>: </label>
    <select class="text" name="categoryview">
      <option value="all" <?php if ($general->getCategoryView() == 'all') echo 'selected'; ?>>
        <?php i18n($this->id . '/VIEW_ALL'); ?>
      </option>
      <option value="hierarchical" <?php if ($general->getCategoryView() == 'hierarchical') echo 'selected'; ?>>
        <?php i18n($this->id . '/HIERARCHICAL'); ?>
      </option>
      <option value="parents" <?php if ($general->getCategoryView() == 'parents') echo 'selected'; ?>>
        <?php i18n($this->id . '/PARENTS_ONLY'); ?>
      </option>
    </select>
  </p>
</div>

<div class="clear"></div>

<label for="productsperpage"><?php i18n($this->id . '/ERROR_MESSAGE'); ?>: </label>
<textarea id="pageerror" name="pageerror" style="height: 200px;"><?php echo $general->getPageError(); ?></textarea>

<?php
  $textarea = 'pageerror';
  include('ckeditor.php');
?>
