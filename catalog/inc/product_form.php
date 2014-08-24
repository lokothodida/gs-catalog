<?php
  $fields = new ProductFields(GSDATAOTHERPATH . $this->id . '/fields.xml');
  $fields = $fields->getFields();
?>

<style>
  .CodeMirror {
    height: 200px;
  }
  .CodeMirror-scroll {
    overflow-y: auto;
    overflow-x: auto;
    height: 200px;
  }
</style>

<p>
  <input type="text" class="text title" name="title" placeholder="<?php i18n($this->id . '/PRODUCT'); ?>" value="<?php echo $product->getField('title'); ?>">
</p>

<style>
  .customfields .text {
    width: 635px;
  }
</style>

<div id="metadata_window2" class="customfields">
  <p>
    <label for="categories"><?php i18n($this->id . '/CATEGORIES'); ?> : </label>
    <?php
      // set up categories dropdown
      $categoriesDropdown = '<select multiple class="text" name="categories[]">';
      foreach ($categories as $k => $data) {
        $categoriesDropdown .= '<option value="' . $k . '">' . $data->getTitle() . '</option>';
      }
      $categoriesDropdown .= '</select>';
      
      // set up already-selected values from dropdown
      if ($product->getField('categories')) {
        foreach ($product->getField('categories')->category as $cat) {
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
      echo '<p><label for="' . $field->name . '">' . $field->label . ' : </label>';
      // fill in value
      $value = $product->getField($field->name) ? $product->getField($field->name) : $field->default;
      if ($field->type == 'codeeditor') {
        echo '<textarea id="' . $field->name . '" name="' . $field->name . '" style="height: 200px !important;">' . $value . '</textarea>';
        $textarea = $field->name;
        $height = 200;
        include('codemirror.php');
      }
      elseif ($field->type == 'wysiwyg') {
        echo '<textarea id="' . $field->name . '" name="' . $field->name . '">' . $value . '</textarea>';
        $textarea = $field->name;
        $height = 200;
        include('ckeditor.php');
      }
      elseif ($field->type == 'checkbox') {
        echo '<input type="checkbox" id="' . $field->name . '" name="' . $field->name . '" value="y" ' . (($value == 'y') ? 'checked="checked"' : null) . '/>';
      }
      else {
        echo '<input type="text" class="text" name="' . $field->name . '" value="' . $value . '">';
      }
      echo '</p>';
    }
  ?>
</div>
