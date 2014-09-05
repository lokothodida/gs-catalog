<?php
if ($general->getWysiwyg() == 'y') {
  $toolbar = ', toolbar: "' . $general->getWysiwygToolbar() . '"';
  $options = !is_null($GLOBALS['EDOPTIONS']) ? ',' . trim($GLOBALS['EDOPTIONS'], ",") : '';
?>
  <script type="text/javascript" src="template/js/ckeditor/ckeditor.js"></script>
  <script type="text/javascript">
    var editor = CKEDITOR.replace('<?php echo isset($textarea) ? $textarea : 'post-content'; ?>', {
      skin : 'getsimple',
      forcePasteAsPlainText : true,
      language : '<?php echo $GLOBALS['EDLANG']; ?>',
      defaultLanguage : 'en',

      entities : false,
      uiColor : '#FFFFFF',
      height: '<?php echo isset($height) ? $height : $GLOBALS['EDHEIGHT']; ?>',
      baseHref : '<?php echo $GLOBALS['SITEURL']; ?>',
      tabSpaces: 10,
      filebrowserBrowseUrl : 'filebrowser.php?type=all',
      filebrowserImageBrowseUrl : 'filebrowser.php?type=images',
      filebrowserWindowWidth : '730',
      filebrowserWindowHeight : '500'
      <?php echo $toolbar; ?>
      <?php echo $options; ?>
    });
  </script>
<?php } ?>
