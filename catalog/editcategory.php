<h3><?php echo $title; ?></h3>
<form action="<?php echo $action; ?>" method="post">
  <p id="edit_window">
    <label for="post-title" style="display:none;"><?php i18n('catalog/TITLE'); ?></label>
    <input class="text title" id="post-title" name="title" value="<?php echo $category->get('title'); ?>" placeholder="<?php i18n('catalog/TITLE'); ?>" type="text">
  </p>

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
      <input class="text" name="image" value="<?php echo $cat->get('image'); ?>" placeholder="http://"/>
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
    /
    <a href="<?php echo $cancelUrl; ?>" class="cancel"><?php i18n('CANCEL'); ?></a>
  </div>

  <input type="hidden" name="<?php echo $postName; ?>"/>
</form>