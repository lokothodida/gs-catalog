<?php
  // create new product
  if (isset($_POST['submitted'])) {
    $xml = new SimpleXMLExtended('<product/>');
    $fields = new CatalogSettingsFields(array('file' => GSDATAOTHERPATH . $this->id . '/fields.xml'));
    $fields = $fields->getFields();

    // title
    $xml->title = null;
    $xml->title->addCData($_POST['title']);

    // categories
    $xml->categories = null;
    if (isset($_POST['categories']) && is_array($_POST['categories'])) {
      foreach ($_POST['categories'] as $k => $cat) {
        $xml->categories->category[$k] = $cat;
      }
    }

    // custom fields
    foreach ($fields as $field) {
      if (isset($_POST[$field->name])) {
        $xml->{$field->name} = null;
        $xml->{$field->name}->addCData($_POST[$field->name]);
      }
      // checkbox is set to 'n'
      elseif ($field->type == 'checkbox') {
        $xml->{$field->name} = null;
        $xml->{$field->name}->addCData('n');
      }
    }

    // dates
    $xml->credate = null;
    $xml->credate->addCData(time());
    $xml->pubdate = null;
    $xml->pubdate->addCData(time());

    // save the file if name doesn't cause a clash
    $identifiers = simplexml_load_file(GSDATAOTHERPATH . $this->id . '/identifiers.xml');
    $filename = GSDATAOTHERPATH . $this->id . '/products/' . $identifiers->products . '-' . $this->setup->toSlug($_POST['title']) . '.xml';
    if ($this->setup->toSlug($_POST['title']) && !file_exists($filename)) {
      $succ = (bool) $xml->saveXML($filename);
      $identifiers->products = ((int) $identifiers->products) + 1;
      $identifiers->saveXML(GSDATAOTHERPATH . $this->id . '/identifiers.xml');
    }
    else {
      $msg = i18n_r($this->id . '/PROD_NAMEEXISTS');
      $succ = false;
    }
    
    // success
    if ($succ) {
      $this->setup->deleteI18nSearchIndex();
      $msg = i18n_r($this->id . '/PROD_CRE_SUCC');
      $isSuccess = true;
    }
    // error
    else {
      if (!isset($msg)) $msg = i18n_r($this->id . '/PROD_CRE_FAIL');
      $isSuccess = false;
    }
  }
  if (isset($_GET['del'])) {
    $file = GSDATAOTHERPATH . $this->id . '/products/' . $_GET['del'] . '.xml';
    if (file_exists($file)) {
      $succ = (bool) unlink(GSDATAOTHERPATH . $this->id . '/products/' . $_GET['del'] . '.xml');
    }
    else {
      $succ = false;
    }
    
    // success
    if ($succ) {
      $this->setup->deleteI18nSearchIndex();
      $msg = i18n_r($this->id . '/PROD_DEL_SUCC');
      $isSuccess = true;
    }
    // error
    else {
      $msg = i18n_r($this->id . '/PROD_DEL_FAIL');
      $isSuccess = false;
    }
  }

  // load categories
  $categoriesParams = array(
    'wildcard' => $this->dataDir . '/categories/*.xml',
    'settings' => $this->settings,
  );

  $categories = new CatalogCategories($categoriesParams);
  $categories = $categories->getCategories();
  $tmpcats = array();

  // set the array keys to the ids of the categories
  foreach ($categories as $k => $category) {
    $tmpcats[$category->getField('id')] = $category;
  }

  $categories = $tmpcats;

  // load products
  $productsParams = array(
    'wildcard' => $this->dataDir . '/products/*.xml',
    'settings' => $this->settings,
    'i18n'     => $this->setup->i18nExists(),
  );

  $products = new CatalogProducts($productsParams);

  $filter = isset($_GET['filter']) && $_GET['filter'] != 'all' ? $_GET['filter'] : false;

  $getProductsParams = array('filter' => $filter);

  if ($this->setup->i18nExists()) {
    $getProductsParams['languages'] = true;
  }

  $prods = $products->getProducts($getProductsParams);
?>
<form>
  <h3 class="floated"><?php i18n($this->id . '/PRODUCTS'); ?></h3>
  <div class="edit-nav clearfix">
    <a href="load.php?id=<?php echo $this->id; ?>&products=create"><?php i18n($this->id . '/CREATE_PRODUCT'); ?></a>
  </div>

  <p>
    <label for="category"><?php i18n($this->id . '/CATEGORY'); ?> : </label>
    <select class="text" id="selectcategory" onchange="changeCategory(this)" name="category" style="width: 200px;">
      <option value="all" <?php if (isset($_GET['filter']) && $_GET['filter'] == 'all') echo 'selected="selected"'; ?>>
        <?php i18n($this->id . '/VIEW_ALL'); ?>
      </option>
      <option value="" <?php if (isset($_GET['filter']) && $_GET['filter'] == '') echo 'selected="selected"'; ?>>
        <?php i18n($this->id . '/NO_CATEGORIES'); ?>
      </option>
      <?php foreach ($categories as $category) : ?>
      <option value="<?php echo $category->getField('id'); ?>" <?php if (isset($_GET['filter']) && $_GET['filter'] == (string) $category->getField('id')) echo 'selected="selected"'; ?>>
        <?php echo $category->getField('title'); ?>
      </option>
      <?php endforeach; ?>
    </select>
  </p>

  <script>
    function changeCategory(obj) {
      window.location = "load.php?id=<?php echo $this->id; ?>&products&filter=" + obj.value;
    }
  </script>

  <table class="edittable highlight paginate">
    <thead>
      <tr>
        <?php if ($this->setup->i18nExists()) : ?>
          <th><?php i18n('LANGUAGE'); ?></th>
        <?php endif; ?>
        <th width="60%"><?php i18n($this->id . '/NAME'); ?></th>
        <th width="35%"><?php i18n($this->id . '/CATEGORIES'); ?></th>
        <th width="5%"></th>
      </tr>
    </thead>
    <tbody>
      <?php
        foreach ($prods as $product) :
          if ($this->setup->i18nExists()) {
            $languages = $product['language'];
            $product   = $product['language'][return_i18n_default_language()];
          }
      ?>
      <tr>
        <?php if ($this->setup->i18nExists()) : ?>
          <td>
            <select class="text selectLanguage" style="width: 50px;">
              <?php foreach ($languages as $lang => $pr) : ?>
                <option>
                  <?php echo $lang; ?>
                </option>
              <?php endforeach; ?>
            </select>
          </td>
        <?php endif; ?>
        <td><a class="editProduct" href="load.php?id=<?php echo $this->id; ?>&products=<?php echo $product->getField('id'); ?>"><?php echo $product->getField('title'); ?></a></td>
        <td>
          <?php
            // display the category links
            foreach ($product->getField('categories') as $category) {
              if (isset($categories[$category])) {
                $cat = $categories[$category];
                //$product->setUrlFromCategory($categories[$category]);
                echo '<li><a href="' . $cat->getField('url') . '" target="_blank">'. $cat->getField('title') . '</a></li>';
              }
            }
          ?>
        </td>
        <td style="text-align: right;"><a class="cancel deleteProduct" href="load.php?id=<?php echo $this->id; ?>&products&del=<?php echo $product->getField('id'); ?>" onclick="deleteProduct(); return false;">x</a></td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($prods)) : ?>
      <tr>
        <td colspan="100%"><?php i18n($this->id . '/NO_PRODUCTS'); ?></th>
      </tr>
      <?php endif; ?>
    </tbody>
  </table>
</form>

<script>
  function deleteProduct(obj) {
    if (confirm("<?php i18n($this->id . '/PROD_DEL_SURE'); ?>")) {
      window.location = obj.getAttribute('href');
    }
  }
  <?php if ($this->setup->i18nExists()) : ?>
  $(document).ready(function() {
    // select the correct language
    $('.selectLanguage').change(function() {
      var $this = $(this);
      var lang = $this.val();

      $this.closest('tr').find('.editProduct, .deleteProduct').each(function() {
        var currentLink = $(this).attr('href').split('&lang=')[0];
        var newLink = currentLink + '&lang=' + lang;
        $(this).attr('href', newLink);
      });
    });
  });
  <?php endif; ?>
</script>
