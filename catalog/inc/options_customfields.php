<?php
  // saved
  if (isset($_POST['submitted'])) {
    $xml = new SimpleXMLExtended('<fields/>');
    foreach ($_POST['name'] as $k => $field) {
      $xml->field[$k] = null;
      $xml->field[$k]->addAttribute('name', $_POST['name'][$k]);
      $xml->field[$k]->addAttribute('type', $_POST['type'][$k]);
      $xml->field[$k]->addAttribute('index', $_POST['index'][$k]);
      $xml->field[$k]->addAttribute('label', $_POST['label'][$k]);
      $xml->field[$k]->addCData($_POST['default'][$k]);
    }
    $succ = (bool) $xml->saveXML(GSDATAOTHERPATH . $this->id . '/fields.xml');
    
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

?>

<style>
  .smalltext {
    width: 80px !important;
    padding: 2px !important;
  }
</style>

<?php

$fields = new ProductFields(GSDATAOTHERPATH . $this->id . '/fields.xml');
$fieldHtml = '

<tr>
  <td><input type="text" class="text smalltext fieldname" name="name[]" value="%name%"/></td>
  <td>
    <select class="text smalltext fieldtype" name="type[]">
      <option value="text">' . i18n_r($this->id . '/TEXT') . '</option>
      <option value="textarea">' . i18n_r($this->id . '/TEXTAREA') . '</option>
      <option value="wysiwyg">' . i18n_r($this->id . '/WYSIWYG') . '</option>
      <option value="image">' . i18n_r($this->id . '/IMAGE') . '</option>
      <option value="codeeditor">' . i18n_r($this->id . '/CODEEDITOR') . '</option>
      <option value="checkbox">' . i18n_r($this->id . '/CHECKBOX') . '</option>
    </select>
  </td>
  <td><input type="text" class="text smalltext fieldname" name="label[]" value="%label%"/></td>
  <td><input type="text" class="text smalltext" name="default[]" value="%default%"/></td>
  <td>
    <select class="text smalltext fieldtype" name="index[]">
      <option value="y">' . i18n_r($this->id . '/YES') . '</option>
      <option value="n">' . i18n_r($this->id . '/NO') . '</option>
    </select>
  </td>
  <td style="text-align: center;"><a href="#" onclick="deleteField(this); return false;" class="cancel">x</a></td>
</tr>

';

?>

<table class="highlight" id="customfields">
  <thead>
    <tr>
      <th><?php i18n($this->id . '/FIELD'); ?></th>
      <th><?php i18n($this->id . '/TYPE'); ?></th>
      <th><?php i18n($this->id . '/LABEL'); ?></th>
      <th><?php i18n($this->id . '/DEFAULT'); ?></th>
      <th><?php i18n($this->id . '/INDEX'); ?></th>
      <th style="text-align: center;"><?php i18n($this->id . '/OPTIONS'); ?></th>
    </tr>
  </thead>
  <tbody id="fields">
    <?php
      foreach ($fields->getFields() as $field) {
        echo str_replace(
              array(
                '%name%', 
                'value="' . $field->type . '"',
                'value="' . $field->index . '"', 
                '%default%', 
                '%label%'), 
              array(
                $field->name, 
                'value="' . $field->type . '" selected="selected"',
                'value="' . $field->index . '" selected="selected"', 
                $field->default, 
                $field->label), 
              $fieldHtml);
      }
    ?>
  </tbody>
  <tbody id="fieldsoptions">
    <tr>
      <td colspan="100%"><a href="#" onclick="addField(); return false;"><?php i18n($this->id . '/ADD_FIELD'); ?></a></td>
    </tr>
  </tbody>
</table>

<script type="text/javascript">
  // sortable fields
  $('#customfields tbody').sortable();
  
  // function to add a field
  function addField() {
    var tbody = document.getElementById('fields');
    tbody.innerHTML = tbody.innerHTML + <?php echo json_encode(str_replace(array('%name%', '%default%', '%label%'), '', $fieldHtml)); ?>;
  }
  function deleteField(linknode) {
    var tr = linknode.parentNode.parentNode;
    tr.parentNode.removeChild(tr);
  }
  function validateOptions() {
    var names = document.getElementsByClassname("fieldname");
    for (name in names) {
      if (names[name] == "") return false;
    }
    
    return true;
  }
</script>
