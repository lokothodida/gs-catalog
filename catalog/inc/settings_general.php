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
      'internalsearch',
      'i18nsearch',
      'slugged',
      // editor(s)
      'wysiwyg',
      'wysiwygtoolbar',
      'languages',
      // errors
      'pageerror',
      'categoryerror',
      'producterror',
      'noresults',
      );
    $xml = new SimpleXMLExtended('<options/>');
    foreach ($fields as $k => $field) {
      $xml->{$field} = null;
      if (isset($_POST[$field])) {
        if ($field == 'languages') {
          // language formatting
          $languages = explode("\n", $_POST[$field]);
          $languages = array_map('trim', $languages);

          if ($this->setup->i18nExists() && !in_array($defaultLang = trim(return_i18n_default_language()), $languages)) {
            $langauges = array_unshift($languages, $defaultLang);
          }

          $languages = implode("\n", $languages);

          $xml->{$field}->addCData($languages);
        } else {
          $xml->{$field}->addCData($_POST[$field]);
        }
      } else {
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
  
  $general = $generalSettings = new CatalogSettingsGeneral(GSDATAOTHERPATH . $this->id . '/general.xml');
?>

<style>
  form h3 {
    font-size: 15px;
    line-height: 20px;
  }
</style>

<p>
  <input type="text" class="text title" name="title" placeholder="<?php i18n($this->id . '/CATALOG_TITLE'); ?>" value="<?php echo $general->get('title'); ?>"/>
</p>

<h3><?php i18n($this->id . '/GENERAL'); ?></h3>
<div class="leftsec">
  <p>
    <label for="slug"><?php i18n($this->id . '/SLUG'); ?>: </label>
    <input class="text" id="slug" name="slug" value="<?php echo $general->get('slug'); ?>" type="text">
  </p>
  <p class="inline">
    <input type="checkbox" name="wysiwyg" value="y" <?php if ($general->get('wysiwyg') == 'y') echo 'checked="checked"'; ?>>
    <label for="wysiwyg"><?php i18n('ENABLE_HTML_ED'); ?></label>
  <p>
    <label for="wysiwyg"><?php i18n($this->id . '/WYSIWYG_TOOLBAR'); ?>: </label>
    <select class="text" name="wysiwygtoolbar">
      <?php foreach (array('basic', 'advanced') as $wysiwygtoolbar) : ?>
      <option value="<?php echo $wysiwygtoolbar; ?>" <?php if ($general->get('wysiwygtoolbar') === $wysiwygtoolbar) echo 'selected'; ?>>
        <?php i18n($this->id . '/' . strtoupper($wysiwygtoolbar)); ?>
      </option>
      <?php endforeach; ?>
    </select>
  </p>
</div>

<div class="rightsec">
  <p class="inline">
    <input type="checkbox" name="slugged" value="y" <?php if ($general->get('slugged')) echo 'checked="checked"'; ?>>
    <label for="i18nsearch"><?php i18n($this->id . '/SLUGGED'); ?></label>
  </p>
  <p class="inline">
    <input type="checkbox" name="internalsearch" value="y" <?php if ($general->get('internalsearch')) echo 'checked="checked"'; ?>>
    <label for="internalsearch"><?php i18n($this->id . '/INTERNAL_SEARCH'); ?></label>
  </p>
  <p class="inline">
    <input type="checkbox" name="i18nsearch" value="y" <?php if ($general->get('i18nsearch')) echo 'checked="checked"'; ?>>
    <label for="i18nsearch"><?php i18n($this->id . '/I18N_SEARCH'); ?></label>
  </p>
</div>

<div class="clear"></div>

<div class="leftsec">
  <h3><?php i18n($this->id . '/PRODUCTS'); ?></h3>
  <p>
    <label for="productsperpage"><?php i18n($this->id . '/PROD_PERPAGE'); ?>: </label>
    <input class="text" id="productsperpage" name="productsperpage" value="<?php echo $general->get('productsperpage'); ?>" type="number">
  </p>
  <p>
    <label for="pagination"><?php i18n($this->id . '/PAGINATION'); ?>: </label>
    <select class="text" name="pagination">
      <option value="top" <?php if ($general->get('pagination') == 'top') echo 'selected'; ?>>
        <?php i18n($this->id . '/TOP'); ?>
      </option>
      <option value="bottom" <?php if ($general->get('pagination') == 'bottom') echo 'selected'; ?>>
        <?php i18n($this->id . '/BOTTOM'); ?>
      </option>
      <option value="both" <?php if ($general->get('pagination') == 'both') echo 'selected'; ?>>
        <?php i18n($this->id . '/BOTH'); ?>
      </option>
    </select>
  </p>
  <?php if ($this->setup->i18nExists()) : ?>
  <p>
    <label for="languages"><?php i18n($this->id . '/LANGUAGES'); ?>: </label>
    <?php
      $languages = implode("\n", $general->get('languages'));
    ?>
    <textarea class="text" name="languages" style="width: 94%; height: 80px;"><?php echo $languages; ?></textarea>
  </p>
  <?php endif; ?>
</div>

<div class="rightsec">
  <h3><?php i18n($this->id . '/CATEGORIES'); ?></h3>
  <p>
    <label for="categoryview"><?php i18n($this->id . '/CATEGORY_VIEW'); ?>: </label>
    <select class="text" name="categoryview">
      <option value="all" <?php if ($general->get('categoryview') == 'all') echo 'selected'; ?>>
        <?php i18n($this->id . '/VIEW_ALL'); ?>
      </option>
      <option value="hierarchical" <?php if ($general->get('categoryview') == 'hierarchical') echo 'selected'; ?>>
        <?php i18n($this->id . '/HIERARCHICAL'); ?>
      </option>
      <option value="parents" <?php if ($general->get('categoryview') == 'parents') echo 'selected'; ?>>
        <?php i18n($this->id . '/PARENTS_ONLY'); ?>
      </option>
    </select>
  </p>
</div>

<div class="clear"></div>

<h3><?php i18n($this->id . '/ERRORS'); ?></h3>
<p>
  <label for="pageerror">404: </label>
  <textarea id="pageerror" name="pageerror" style="height: 200px;"><?php echo $general->get('pageerror'); ?></textarea>
  <?php $textarea = 'pageerror'; $height = 200; include('ckeditor.php'); ?>
</p>

<p>
  <label for="categoryerror"><?php i18n($this->id . '/CATEGORIES'); ?>: </label>
  <textarea id="categoryerror" name="categoryerror" style="height: 200px;"><?php echo $general->get('categoryerror'); ?></textarea>
  <?php $textarea = 'categoryerror'; $height = 200; include('ckeditor.php'); ?>
</p>

<p>
  <label for="producterror"><?php i18n($this->id . '/PRODUCTS'); ?>: </label>
  <textarea id="producterror" name="producterror" style="height: 200px;"><?php echo $general->get('producterror'); ?></textarea>
  <?php $textarea = 'producterror'; $height = 200; include('ckeditor.php'); ?>
</p>

<p>
  <label for="noresults"><?php i18n($this->id . '/NO_RESULTS'); ?>: </label>
  <textarea id="noresults" name="noresults" style="height: 200px;"><?php echo $general->get('noresults'); ?></textarea>
  <?php $textarea = 'noresults'; $height = 200; include('ckeditor.php'); ?>
</p>
