<h3 class="floated"><?php i18n('catalog/PRODUCTS'); ?></h3>
<div class="edit-nav">
  <p>
    <a href="<?php echo CATALOGADMINURL; ?>&products&create"><?php i18n('catalog/CREATE'); ?></a>
    Filter: 
    <input autocomplete="off" id="filter" value="" class="_text ac_input" style="width:80px" title="Enter a part of a title or : and a tag/keyword" type="text">
  </p>
  <div class="clear"></div>
</div>

<script type="text/javascript" src="../plugins/catalog/js/jquery.fastlivefilter.js"></script>
<script>
  $(document).ready(function() {
    $('#filter').fastLiveFilter('.products tbody', {
      timeout: 200,
      callback: function(total) { $('#pg_counter').html(total); }
    });
  });
</script>

<table class="edittable highlight products">
  <thead>
    <tr>
      <th width="60%"><?php i18n('catalog/TITLE'); ?></th>
      <th><?php i18n('catalog/CATEGORIES'); ?></th>
      <th colspan="2" width="10%"></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($products as $product): ?>
    <tr>
      <td style="padding-left:4px">
        <a href="<?php echo CATALOGADMINURL . '&products&edit=' . $product->get('slug'); ?>">
          <?php echo $product->get('title'); ?>
        </a>
      </td>
      <td style="padding-left:4px">
        <?php foreach ($product->get('categories') as $category) : ?>
        <a href="<?php echo catalog_get_category_url($category); ?>" target="_blank">
          <?php echo $categories[$category]->get('title'); ?>
        </a><br/>
        <?php endforeach; ?>
      </td>
      <td class="secondarylink">
        <a href="<?php echo catalog_get_product_url($product->get('slug')); ?>" target="_blank">
          #
        </a>
      </td>
      <td class="delete">
        <a href="<?php echo CATALOGADMINURL . '&products&delete=' . $product->get('slug'); ?>">
          x
        </a>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<p><em><b><span id="pg_counter"><?php echo count($products); ?></span></b> total products</em></p>