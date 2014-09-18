<style>
  div.photo {
    margin: 0 5px 5px 0;
  }
</style>

<?php

$categoriesDropdown = '<select class="text" name="category"><option value=""></option>';
foreach ($categories as $k => $data) {
  $categoriesDropdown .= '<option value="' . $data->getField('id') . '">' . $data->getField('title') . '</option>';
}
$categoriesDropdown .= '</select>';

$photoHtml = '
  <div class="photo">
      <input type="url" class="text" style="width: 90%;" name="photo[]" value="%photo%" placeholder="http://"/>
      <a href="#" class="cancel" onclick="deletePhoto(this); return false;">x</a>
  </div>
';

$id   = $category->getField('id', true);
$slug = $id['slug'];
$id   = $id['numeric'];

?>

<!--hidden fields-->
<input type="hidden" name="currentId" value="<?php echo $category->getField('id'); ?>">

<p>
  <input type="text" class="text title" name="title" placeholder="<?php i18n($this->id . '/CATEGORY'); ?>" value="<?php echo $category->getField('title'); ?>">
</p>

<div class="leftsec">
  <!--slug-->
  <p>
    <label for="slug"><?php i18n($this->id . '/SLUG'); ?>: </label>
    <input type="text" class="text" style="width: 90%;" name="slug" value="<?php echo $slug; ?>"/>
  </p>
  <!--category parents-->
  <p>
    <label for="categories"><?php i18n($this->id . '/PARENT'); ?>: </label>
    <?php 
      echo str_replace(
            array('value="' . $category->getField('category') . '"', 'value="' . $category->getField('id') . '"'), 
            array('value="' . $category->getField('category') . '" selected', 'value="' . $category->getField('id') . '" ' . ($category->getField('id') ? 'disabled' : null)), 
            $categoriesDropdown);
    ?>
  </p>
</div>
<div class="rightsec">
  <p style="margin: 0 0 0 0;"><label for="photos"><?php i18n($this->id . '/PHOTOS'); ?>: </label></p>
  <div id="photos">
    <?php
      if ($category->getField('photos')) {
        foreach ($category->getField('photos') as $photo) {
          echo str_replace('%photo%', $photo, $photoHtml);
        }
      }
    ?>
  </div>
  
  <div class="addphoto">
    <a href="#" onclick="addPhoto(); return false;" class="cancel">+</a>
  </div>
</div>
<div class="clear"></div>
  
  


<textarea id="description" name="description"><?php echo $category->getField('description'); ?></textarea>

<?php
  $textarea = 'description';
  include('ckeditor.php');
?>

<script type="text/javascript">
  // function to add a field
  function addPhoto() {
    var div = document.getElementById('photos');
    div.innerHTML = div.innerHTML + <?php echo json_encode(str_replace(array('%photo%'), '', $photoHtml)); ?>;
  }
  function deletePhoto(linknode) {
    var div = linknode.parentNode;
    div.parentNode.removeChild(div);
  }
</script>
