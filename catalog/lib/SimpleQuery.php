<?php

/**
 * Title:         PHP SimpleQuery
 * Version:       0.1.1
 * Description:   Perform queries on PHP arrays
 * Author:        Lawrence Okoth-Odida
 * Documentation: https://github.com/lokothodida/php-simplequery/wiki/
 */
class SimpleQuery {
  /** constants*/
  const NOEXIST = 'NO_EXIST';

  /** properties */
  protected $items,
            $query,
            $sort,
            $limit,
            $results = array();

  /** public methods */
  // Constructor
  public function __construct(array $items) {
    $this->items = $items;
  }

  // Run the query
  public function query(array $query, array $sort = array(), $limit = 0) {
    // reset the results
    $this->results = array();

    // ensure that the default elements of the query are set
    $this->query = $this->setUpQueryDefaults($query);
    $this->sort  = $this->setUpSortingDefaults($sort);
    $this->limit = (int) $limit;

    // filter out items according to the query
    foreach ($this->items as $item) {
      $this->filterItem($item);
    }

    // sort the results
    $this->sortResults();

    // limit the results
    $this->limitResults();

    return $this->results;
  }

  /** protected methods */
  // Get a field from an item
  protected function getField($item, $field) {
    return isset($item[$field]) ? $item[$field] : self::NOEXIST;
  }

  /** private methods */
  // Set up defaults for query
  private function setUpQueryDefaults($query) {
    foreach ($query as $field => $settings) {
      if (!is_array($settings)) {
        // format into an array if it isn't one
        $query[$field] = array('$eq' => $settings);
      } else {
        // if an array is provided ...
        // ...
      }

      // Fix the settings to be arrays
      $query = $this->fixSettingsToArrays(array('$has'), $query, $field);

      // case sensitivity
      if (!isset($query[$field]['$cs'])) {
        $query[$field]['$cs'] = true;
      }
    }

    return $query;
  }

  // Fix settings that are strings into arrays (correct formatting)
  private function fixSettingsToArrays($settings, $query, $field) {
    // Include negations
    foreach ($settings as $setting) {
      $settings[] = '!' . $setting;
    }

    // Now fix the formatting
    foreach ($settings as $setting) {
      if (isset($query[$field][$setting]) && !is_array($query[$field][$setting])) {
        $query[$field][$setting] = array($query[$field][$setting]);
      }
    }

    return $query;
  }

  // Set up defaults for sorting
  private function setUpSortingDefaults($sort) {
    // ...
    return $sort;
  }

  // Filter out an item
  private function filterItem($item) {
    // Keep track of the success status (before the loop, it is true (invariant))
    $success = true;

    // Check each field
    foreach ($this->query as $field => $settings) {
      // Get the value from the field
      $value = $this->getField($item, $field);

      // Check the case sensitivity
      $caseSensitive = $settings['$cs'];

      // Validate against each setting, accumulating the successes
      foreach ($settings as $setting => $properties) {
        $success = $success && $this->validate($value, $caseSensitive, $setting, $properties);
      }
    }

    // Add the item if it passed all of the validation tests
    if ($success) {
      $this->results[] = $item;
    }
  }

  // Validate
  private function validate($value, $cs, $setting, $properties) {
    $success = false;

    // Case-sensitive switch
    if (!$cs) {
      if (is_array($value)) {
        $value = array_map('strtolower', $value);
      } else {
        $value = strtolower($value);
      }
    }

    // Check if we have a negation in place
    $negation = substr($setting, 0, 1) == '!';

    if ($negation) {
      // Shave off the negation sign for now
      $setting = substr($setting, 1);
    }

    switch ($setting) {
      // greater than
      case '$gt':
        $success = $value > $properties;
        break;
      // greater than or equal to
      case '$gt=':
        $success = $value >= $properties;
        break;
      // less than
      case '$lt':
        $success = $value < $properties;
        break;
      // less than or equal to
      case '$lt=':
        $success = $value <= $properties;
        break;
      // strict equality
      case '$eq=':
        $success = $value === $properties;
        break;
      // has
      case '$has':
        $success = true;

        // Check if each $property occurs in $value (be it an array or a string)
        $isValueAnArray = is_array($value);
        foreach ($properties as $property) {
          $pos     = $isValueAnArray ? in_array($property, $value) : strpos($value, $property);
          $success = $success && $pos !== false;
        }

        break;
      // in
      case '$in':
        var_dump($value, $properties); echo '<br><br>';
        $success = in_array($value, $properties);
        break;
      // check if value exists
      case '$set':
        // ensure case is correct
        $selfNoExist = $cs ? self::NOEXIST : strtolower(self::NOEXIST);
        $noExist = $value == $selfNoExist;

        $success = $properties ? !$noExist : $noExist;
        break;
      // case sensitivity (not an actual operator)
      case '$cs':
        $success = true;
        break;
      // custom user function
      case '$custom':
        $success = call_user_func_array($properties, array($value));
        break;
      // normal equality
      default:
        $success = $value == $properties;
        break;
    }

    // Now negate the result if we originally had a negation
    if ($negation) {
      $success = !$success;
    }

    return $success;
  }

  // Sort the results
  private function sortResults() {
    // Use PHP's built-in sorting algorithm
    uasort($this->results, array($this, 'sortResultsImplementation'));
  }

  // Sorting implementation
  private function sortResultsImplementation($itemA, $itemB) {
    // Keep track of a score
    $score = 0;

    // Eun through each sorting field, aggregating the score
    foreach ($this->sort as $field => $order) {
      // Check field values
      $itemAValue = $this->getField($itemA, $field);
      $itemBValue = $this->getField($itemB, $field);

      if (in_array($order, array('asc,', 'desc'))) {
        // Check regular ascending/descending order
        $score += $this->sortResultsComparison($itemAValue, $itemBValue, $order);
      } else {
        // Use a custom user-defined callback
        $score += call_user_func_array($order, array($itemAValue, $itemBValue));
      }
    }

    return $score;
  }

  // Comparison between two items based on a field
  private function sortResultsComparison($itemAValue, $itemBValue, $order) {
    // If the values are arrays, check the array lengths instead
    $itemAValue = is_array($itemAValue) ? count($itemAValue) : $itemAValue;
    $itemBValue = is_array($itemBValue) ? count($itemBValue) : $itemBValue;

    // compare numbers if the values are numeric; strings otherwise
    $comparison = (is_numeric($itemAValue) && is_numeric($itemBValue)) ? cmp($itemAValue, $itemBValue) : strcmp($itemAValue, $itemBValue);
    return ($order == 'asc' ? $comparison : -$comparison);
  }

  // Limit the results
  private function limitResults() {
    if ($this->limit) {
      // set the correct starting position
      $start = ($this->limit < 0) ? $this->limit : 0;

      // slice the results
      $this->results = array_slice($this->results, $start, abs($this->limit));
    }
  }
}

?>
