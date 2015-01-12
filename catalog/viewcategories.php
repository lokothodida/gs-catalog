<h3 class="floated"><?php i18n('catalog/CATEGORIES'); ?></h3>
<div class="edit-nav">
  <p>
    <a href="<?php echo CATALOGADMINURL; ?>&categories&index"><?php i18n('catalog/INDEX'); ?></a>
    <a href="<?php echo CATALOGADMINURL; ?>&categories&create"><?php i18n('catalog/CREATE'); ?></a>
  </p>
  <div class="clear"></div>
</div>

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
      var category = $(this).attr('href').replace('<?php echo CATALOGADMINURL; ?>&categories&delete=', '');
      var popup = confirm('<?php i18n('catalog/ADMIN_CATEGORY_DELETE_CONFIRM'); ?>'.replace('%s', '"' + category + '"'));

      if (!popup) {
        return false;
      }
    });
  });
</script>

<form action="<?php echo $action; ?>" method="post">
  <input type="hidden" name="orderCategories"/>
  <table id="editnav" class="edittable highlight">
    <thead>
      <tr>
        <th colspan="100%"><?php i18n('catalog/TITLE'); ?></th>
      </tr>
    </thead>
    <tbody class="ui-sortable">
      <?php self::displayCategoriesOnAdminPanel($categories); ?>
    </tbody>
  </table>

  <div id="submit_line">
    <span><input id="page_submit" class="submit" name="submitted" value="<?php i18n('catalog/BTN_SAVEORDER'); ?>" type="submit"></span>
  </div>
</form>

<p><em><b><span id="pg_counter"><?php echo $totalCategories; ?></span></b> total categories</em></p>

