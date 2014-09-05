<style>
  div.photo {
    margin: 0 5px 5px 0;
  }
</style>

<?php

$categoriesDropdown = '<select class="text" name="category"><option value=""></option>';
foreach ($categories as $k => $data) {
  $categoriesDropdown .= '<option value="' . $k . '">' . $data->getTitle() . '</option>';
}
$categoriesDropdown .= '</select>';

$photoHtml = '
  <div class="photo">
      <input type="url" class="text" style="width: 90%;" name="photo[]" value="%photo%" placeholder="http://"/>
      <a href="#" class="cancel" onclick="deletePhoto(this); return false;">x</a>
  </div>
';

?>

<p>
  <input type="text" class="text title" name="title" placeholder="<?php i18n($this->id . '/CATEGORY'); ?>" value="<?php echo $category->getTitle(); ?>">
</p>

<div class="leftsec">
  <p>
    <label for="categories"><?php i18n($this->id . '/PARENT'); ?>: </label>
    <?php 
      echo str_replace(
            array('value="' . $category->getCategory() . '"', 'value="' . $category->getId() . '"'), 
            array('value="' . $category->getCategory() . '" selected', 'value="' . $category->getId() . '" ' . ($category->getId() ? 'disabled' : null)), 
            $categoriesDropdown);
    ?>
  </p>
</div>
<div class="rightsec">
  <p style="margin: 0 0 0 0;"><label for="photos"><?php i18n($this->id . '/PHOTOS'); ?>: </label></p>
  <div id="photos">
    <?php
      if ($category->getPhotos()) {
        foreach ($category->getPhotos() as $photo) {
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
  
  


<textarea id="description" name="description"><?php echo $category->getDescription(); ?></textarea>

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
