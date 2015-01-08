<?php

global $SITEURL;

$ckeditorId = isset($ckeditorId) ? $ckeditorId : 'post-content';
$height = isset($height) ? $height : '500px';

?>

<script type="text/javascript" src="template/js/ckeditor/ckeditor.js"></script>
<script type="text/javascript">

  var editor = CKEDITOR.replace('<?php echo $ckeditorId; ?>', {
    skin : 'getsimple',
    forcePasteAsPlainText : true,
    language : 'en',
    defaultLanguage : 'en',
    entities : false,
    uiColor : '#FFFFFF',
    height: '<?php echo $height; ?>',
    baseHref : '<?php echo $SITEURL; ?>',
    tabSpaces:10,
    filebrowserBrowseUrl : 'filebrowser.php?type=all',
    filebrowserImageBrowseUrl : 'filebrowser.php?type=images',
    filebrowserWindowWidth : '730',
    filebrowserWindowHeight : '500'
    ,toolbar: 'basic'										
  });

  CKEDITOR.instances["<?php echo $ckeditorId; ?>"].on("instanceReady", InstanceReadyEvent);

  function InstanceReadyEvent() {
    this.document.on("keyup", function () {
      $('#editform #<?php echo $ckeditorId; ?>').trigger('change');
    });
  }

</script>