<?php

class CatalogItemsQuery extends SimpleQuery {
  protected function getField($item, $field) {
    $value = $item->getField($field);

    if ($value !== null) {
      return $value;
    } else {
      return self::NOEXIST;
    }
  }
}

?>
