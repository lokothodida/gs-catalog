<h3 class="floated"><?php i18n('catalog/FIELDS'); ?></h3>
<div class="edit-nav">
  <p>
    <a href="<?php echo CATALOGADMINURL; ?>&settings=cart"><?php i18n('catalog/CART'); ?></a>
    <a href="<?php echo CATALOGADMINURL; ?>&settings=fields" class="current"><?php i18n('catalog/FIELDS'); ?></a>
    <a href="<?php echo CATALOGADMINURL; ?>&settings=theme"><?php i18n('catalog/THEME'); ?></a>
    <a href="<?php echo CATALOGADMINURL; ?>&settings"><?php i18n('catalog/SETTINGS'); ?></a>
  </p>
  <div class="clear"></div>
</div>

<style>
  .smalltext {
    width: 80px !important;
    padding: 2px !important;
  }
</style>
<?php
$fieldHtml = '
<tr>
<td><input type="text" class="text smalltext fieldname" name="name[]" value="%name%"/></td>
<td>
<select class="text smalltext fieldtype" name="type[]">
<option value="text">' . i18n_r('catalog/TEXT') . '</option>
<option value="textarea">' . i18n_r('catalog/TEXTAREA') . '</option>
<option value="wysiwyg">' . i18n_r('catalog/WYSIWYG') . '</option>
<option value="image">' . i18n_r('catalog/IMAGE') . '</option>
<option value="codeeditor">' . i18n_r('catalog/CODEEDITOR') . '</option>
<option value="checkbox">' . i18n_r('catalog/CHECKBOX') . '</option>
</select>
</td>
<td><input type="text" class="text smalltext fieldname" name="label[]" value="%label%"/></td>
<td><input type="text" class="text smalltext" name="default[]" value="%default%"/></td>
<td>
<select class="text smalltext fieldtype" name="index[]">
<option value="y">' . i18n_r('catalog/YES') . '</option>
<option value="n">' . i18n_r('catalog/NO') . '</option>
</select>
</td>
<td style="text-align: center;"><a href="#" onclick="deleteField(this); return false;" class="cancel">x</a></td>
</tr>
';
?>

<form method="post">
  <input type="hidden" name="editFields"/>
  <table class="highlight" id="customfields">
  <thead>
  <tr>
  <th><?php i18n('catalog/FIELD'); ?></th>
  <th><?php i18n('catalog/TYPE'); ?></th>
  <th><?php i18n('catalog/LABEL'); ?></th>
  <th><?php i18n('catalog/DEFAULT'); ?></th>
  <th><?php i18n('catalog/INDEX'); ?></th>
  <th style="text-align: center;"><?php i18n('catalog/OPTIONS'); ?></th>
  </tr>
  </thead>
  <tbody id="fields">
  <?php
  foreach ($fields as $field) {
    echo str_replace(
      array(
        '%name%',
        'value="' . $field['type'] . '"',
        'value="' . $field['index'] . '"',
        '%default%',
        '%label%'),
      array(
        $field['name'],
        'value="' . $field['type'] . '" selected="selected"',
        'value="' . $field['index'] . '" selected="selected"',
        $field['default'],
        $field['label']),
      $fieldHtml);
  }
  ?>
  </tbody>
  <tbody id="fieldsoptions">
  <tr>
  <td colspan="100%"><a href="#" onclick="addField(); return false;"><?php i18n('catalog/ADD_FIELD'); ?></a></td>
  </tr>
  </tbody>
  </table>

  <div id="submit_line">
    <span><input id="page_submit" class="submit" name="submitted" value="<?php i18n('BTN_SAVECHANGES'); ?>" type="submit"></span>
    /
    <a href="<?php echo $cancelUrl; ?>" class="cancel"><?php i18n('CANCEL'); ?></a>
  </div>
</form>

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