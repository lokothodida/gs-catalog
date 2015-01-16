<h3 class="floated"><?php echo $title; ?></h3>

<script>
  $(function() {
    // give delete links a popup message
    $('.deletebtn').click(function() {
      var category = $(this).attr('href').replace('<?php echo CATALOGADMINURL; ?>&categories&delete=', '');
      var popup = confirm('<?php i18n('catalog/ADMIN_CATEGORY_DELETE_CONFIRM'); ?>'.replace('%s', '"' + category + '"'));

      if (!popup) {
        return false;
      }
    });
  });
</script>

<form action="<?php echo $action; ?>" method="post">

<div class="edit-nav">
  <p>
    <?php if ($mode == 'create') : ?>
      <label><?php i18n('catalog/LANGUAGE'); ?>: </label>
      <select name="language">
        <option value=""></option>
        <?php foreach ($languages as $language) : ?>
        <option value="<?php echo $language; ?>"><?php echo $language; ?></option>
        <?php endforeach; ?>
      </select>
    <?php endif; ?>
  </p>
  <div class="clear"></div>
</div>

  <p id="edit_window">
    <label for="post-title" style="display:none;"><?php i18n('catalog/TITLE'); ?></label>
    <input class="text title" id="post-title" name="title" value="<?php echo $category->get('title'); ?>" placeholder="<?php i18n('catalog/TITLE'); ?>" type="text">
  </p>

  <!--hidden fields-->
  <input type="hidden" name="order" value="<?php echo $category->get('order'); ?>"/>
  
  <div class="leftsec">
    <p>
      <label><?php i18n('catalog/PARENT'); ?>: </label>
      <select name="parent" class="text">
        <option value=""></option>
        <?php foreach ($categories as $cat) : ?>
        <option value="<?php echo $cat->get('slug'); ?>"
          <?php
            if ($cat->get('slug') == $category->get('parent')) {
              echo 'selected';
            } elseif ($cat->get('slug') == $category->get('slug')) {
              echo 'disabled';
            }
          ?>
        ><?php echo $cat->get('title'); ?></option>
        <?php endforeach; ?>
      </select>
    </p>
  </div>

  <div class="rightsec">
    <p>
      <label><?php i18n('catalog/IMAGE'); ?>: </label>
            <input class="text" name="image" value="<?php echo $cat->get('image'); ?>" placeholder="http://" id="image" onClick='window.open("../admin/filebrowser.php?CKEditorFuncNum=1&returnid=image&type=images","mywindow","width=600,height=500")'/>
    </p>
  </div>

  <div class="clear"></div>

  <p>
    <label for="post-content" style="display:none;">Page Body</label>
    <textarea id="post-content" name="description"><?php echo $category->get('description'); ?></textarea>
    <?php include('ckeditor.php'); ?>
  </p>

  <div id="submit_line">
    <span><input id="page_submit" class="submit" name="submitted" value="<?php i18n('BTN_SAVECHANGES'); ?>" type="submit"></span>
    <?php if ($mode == 'edit') { ?>
    /
    <a href="<?php echo $deleteUrl; ?>" class="cancel deletebtn"><?php i18n('catalog/DELETE'); ?></a>
    <?php } ?>
    /
    <a href="<?php echo $cancelUrl; ?>" class="cancel"><?php i18n('CANCEL'); ?></a>
  </div>

  <input type="hidden" name="<?php echo $postName; ?>"/>
</form>
