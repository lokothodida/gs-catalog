<h3><?php i18n('catalog/OVERVIEW'); ?></h3>

<ul>
  <li>
    <a href="<?php echo catalog_get_url(); ?>" target="_blank">
      <?php i18n('catalog/VIEW'); ?>
    </a>
  </li>
  <li>
    <?php echo str_replace('%s', '<strong>' . $categories . '</strong>', i18n_r('catalog/NUM_CATEGORIES')); ?>
  </li>
  <li>
    <?php echo str_replace('%s', '<strong>' . $products . '</strong>', i18n_r('catalog/NUM_PRODUCTS')); ?>
  </li>
</ul>