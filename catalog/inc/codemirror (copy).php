<script type="text/javascript" src="template/js/codemirror/lib/codemirror-compressed.js"></script>
<script>
  window.onload = addEventListener('load', function() {
    var foldFunc = CodeMirror.newFoldFunction(CodeMirror.braceRangeFinder);
    
    function keyEvent(cm, e) {
      if (e.keyCode == 81 && e.ctrlKey) {
        if (e.type == "keydown") {
          e.stop();
          setTimeout(function() {foldFunc(cm, cm.getCursor().line);}, 50);
        }
        return true;
      }
    }
    
    function toggleFullscreenEditing() {
      var editorDiv = $('.CodeMirror-scroll');
      if (!editorDiv.hasClass('fullscreen')) {
        toggleFullscreenEditing.beforeFullscreen = { height: editorDiv.height(), width: editorDiv.width() }
        editorDiv.addClass('fullscreen');
        editorDiv.height('100%');
        editorDiv.width('100%');
        editor.refresh();
      }
      else {
        editorDiv.removeClass('fullscreen');
        editorDiv.height(toggleFullscreenEditing.beforeFullscreen.height);
        editorDiv.width(toggleFullscreenEditing.beforeFullscreen.width);
        editor.refresh();
      }
    }
    
    var editor = CodeMirror.fromTextArea(document.getElementById("<?php echo isset($textarea) ? $textarea : 'codetext'; ?>"), {
      lineNumbers: true,
      matchBrackets: true,
      indentUnit: 4,
      indentWithTabs: true,
      enterMode: "keep",
      mode:"<?php echo isset($mode) ? $mode : 'application/x-httpd-php'; ?>",
      tabMode: "shift",
      theme:'default',
  	  onGutterClick: foldFunc,
  	  extraKeys: {
  	    "Ctrl-Q": function(cm){foldFunc(cm, cm.getCursor().line);},
  			"F11": toggleFullscreenEditing,
  			"Esc": toggleFullscreenEditing },
      onCursorActivity: function() {
       	editor.setLineClass(hlLine, null);
       	hlLine = editor.setLineClass(editor.getCursor().line, "activeline");
      }
    });
    var hlLine = editor.setLineClass(0, "activeline");
  }, false);   
</script>
