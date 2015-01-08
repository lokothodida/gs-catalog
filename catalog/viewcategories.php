<h3><?php i18n('catalog/CATEGORIES'); ?></h3>

<script type="text/javascript" src="../plugins/catalog/js/jquery-ui.sort.min.js"></script>
<script>
  $(function() {
    // make categories sortable
    $('#editnav tbody').sortable({
      items: 'tr',
      handle: 'td'
    });

    // give delete links a popup message
    $('.delete a').click(function() {
      var popup = confirm('<?php i18n('CONFIRM_DELETE'); ?>');

      if (!popup) {
        return false;
      }
    });
  });
</script>

<form action="" method="post">
  <input type="hidden" name="orderCategories"/>
  <table id="editnav" class="edittable highlight">
    <thead>
      <tr>
        <th colspan="100%"><?php i18n('catalog/TITLE'); ?></th>
      </tr>
    </thead>
    <tbody class="ui-sortable">
      <?php foreach ($categories as $category): ?>
      <tr>
        <td style="padding-left:4px">
          <input type="hidden" name="order[]" value="<?php echo $category->get('slug'); ?>"/>
          <a href="<?php echo CATALOGADMINURL . '&categories&edit=' . $category->get('slug'); ?>">
            <?php echo $category->get('title'); ?>
          </a>
        </td>
        <td class="secondarylink">
          <a href="<?php echo catalog_get_category_url($category->get('slug')); ?>" target="_blank">
            #
          </a>
        </td>
        <td class="delete">
          <a href="<?php echo CATALOGADMINURL . '&categories&delete=' . $category->get('slug'); ?>">
            x
          </a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div id="submit_line">
    <span><input id="page_submit" class="submit" name="submitted" value="<?php i18n('catalog/BTN_SAVEORDER'); ?>" type="submit"></span>
  </div>
</form>

<p><em><b><span id="pg_counter"><?php echo count($categories); ?></span></b> total categories</em></p>

