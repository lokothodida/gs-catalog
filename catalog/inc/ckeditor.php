<?php
  $toolbar = !is_null($GLOBALS['EDTOOL']) ? ", toolbar: '" . trim($GLOBALS['EDTOOL'], ",'") . "'" : '';
  $options = !is_null($GLOBALS['EDOPTIONS']) ? ',' . trim($GLOBALS['EDOPTIONS'], ",") : '';
?>
	<?php if ($GLOBALS['HTMLEDITOR'] != '') { ?>
	<script type="text/javascript" src="template/js/ckeditor/ckeditor.js"></script>
	<script type="text/javascript">
	  var editor = CKEDITOR.replace('<?php echo isset($textarea) ? $textarea : 'post-content'; ?>', {
			  skin : 'getsimple',
			  forcePasteAsPlainText : true,
			  language : '<?php echo $GLOBALS['EDLANG']; ?>',
			  defaultLanguage : 'he',

			  entities : false,
			  uiColor : '#FFFFFF',
			  height: '<?php echo isset($height) ? $height : $GLOBALS['EDHEIGHT']; ?>',
			  baseHref : '<?php echo $GLOBALS['SITEURL']; ?>',
			  tabSpaces:10,
			  filebrowserBrowseUrl : 'filebrowser.php?type=all',
			  filebrowserImageBrowseUrl : 'filebrowser.php?type=images',
			  filebrowserWindowWidth : '730',
			  filebrowserWindowHeight : '500' 
			  <?php echo $toolbar; ?>
			  <?php echo $options; ?>
	  });
	</script>
	<?php
		# CKEditor setup functions
if (!class_exists('I18nNavigationFrontend') && file_exists($i18n_nav = GSPLUGINPATH . 'i18n_navigation/frontend.class.php')) {
  include($i18n_nav);
}
exec_action('html-editor-init');
	?>
	<?php } ?>
