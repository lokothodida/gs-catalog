<h3><?php echo $title; ?></h3>

<form action="<?php echo $action; ?>" method="post">

<p>
  <input type="text" class="text title" name="title" placeholder="<?php i18n('catalog/PRODUCT'); ?>" value="<?php echo $product->get('title'); ?>">
</p>

<style>
  .customfields .text {
    width: 635px;
  }
</style>

<!--hidden fields-->
<input type="hidden" name="credate" value="<?php echo $product->get('credate'); ?>">
<input type="hidden" name="pubdate" value="<?php echo time(); ?>">

  <div id="metadata_window2" class="customfields">
    <p>
      <label for="categories"><?php i18n('catalog/CATEGORIES'); ?> : </label>
      <?php
        // set up categories dropdown
        $categoriesDropdown = '<select multiple class="text" name="categories[]">';
        foreach ($categories as $k => $data) {
          $categoriesDropdown .= '<option value="' . $data->get('slug') . '">' . $data->get('title') . '</option>';
        }
        $categoriesDropdown .= '</select>';
        
        // set up already-selected values from dropdown
        if ($product->get('categories')) {
          foreach ($product->get('categories') as $cat) {
            $tmp = 'value="' . $cat . '"';
            $categoriesDropdown = str_replace($tmp, $tmp . ' selected', $categoriesDropdown);
          }
        }
        
        // output dropdown menu
        echo $categoriesDropdown;
      ?>
    </p>
    <?php
      // display custom fields
      foreach ($fields as $k => $field) {
        echo '<p><label for="' . $field['name'] . '">' . $field['label'] . ' : </label>';
        // fill in value
        $value = $product->get($field['name']) ? $product->get($field['name']) : $field['default'];
        if ($field['type'] == 'textarea') {
          // textarea
          echo '<textarea id="' . $field['name'] . '" name="' . $field['name'] . '" style="height: 200px !important;">' . $value . '</textarea>';
          include('codemirror.php');
        } elseif ($field['type'] == 'codeeditor') {
          // codeeditor
          echo '<textarea id="' . $field['name'] . '" name="' . $field['name'] . '" style="height: 200px !important;">' . $value . '</textarea>';
          $codemirrorId = $field['name'];
          $height = 200;
          include('codemirror.php');
        } elseif ($field['type'] == 'wysiwyg') {
          // wysiwyg/html editor
          echo '<textarea id="' . $field['name'] . '" name="' . $field['name'] . '">' . $value . '</textarea>';
          $ckeditorId = $field['name'];
          $height = 200;
          include('ckeditor.php');
        } elseif ($field['type'] == 'image') {
          // image field (by Tzvook)
          echo '<input type="text" class="text" name="' . $field['name'] . '" id="' . $field['name'] . '" value="' . $value . '" onClick=\'window.open("../admin/filebrowser.php?CKEditorFuncNum=1&returnid=' . $field['name'] . '&type=images","mywindow","width=600,height=500")\'>';
        } elseif ($field['type'] == 'checkbox') {
          // checkbox
          echo '<input type="checkbox" id="' . $field['name'] . '" name="' . $field['name'] . '" value="y" ' . (($value == 'y') ? 'checked="checked"' : null) . '/>';
        } else {
          // text
          echo '<input type="text" class="text" name="' . $field['name'] . '" value="' . $value . '">';
        }
        echo '</p>';
      }
    ?>
  </div>




  <div id="submit_line">
    <span><input id="page_submit" class="submit" name="submitted" value="<?php i18n('BTN_SAVECHANGES'); ?>" type="submit"></span>
    /
    <a href="<?php echo $cancelUrl; ?>" class="cancel"><?php i18n('CANCEL'); ?></a>
  </div>

  <input type="hidden" name="<?php echo $postName; ?>"/>
</form>