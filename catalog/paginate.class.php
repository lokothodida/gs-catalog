<?php

/**
 * Title:         PHP ArrayPaginate
 * Version:       0.1
 * Description:   Paginate PHP arrays with markup for navigation
 * Author:        Lawrence Okoth-Odida
 * Documentation: https://github.com/lokothodida/php-arraypaginate/wiki/
 */
class ArrayPaginate {
  /** constants*/

  /** properties */
  private $options,
          $items,
          $results,
          $totalPages,
          $navigation;

  /** public methods */
  // Constructor
  public function __construct(array $items) {
    $this->items = $items;
  }

  // Paginate
  public function paginate(array $options) {
    $this->resetPagination();
    $this->setDefaultOptions($options);
    $this->selectCurrentPage();
    $this->buildNavigation();

    return array(
      'results'    => $this->results,
      'totalPages' => $this->totalPages,
      'navigation' => $this->navigation,
    );
  }

  /** private methods */
  // Reset the current pagination fields
  private function resetPagination() {
    $this->options    = array();
    $this->results    = array();
    $this->totalPages = null;
    $this->navigation = null;
  }

  // Put the default options in
  private function setDefaultOptions($options) {
    // items per page
    if (!isset($options['itemsPerPage'])) {
      $options['itemsPerPage'] = 5;
    }

    // current page
    if (!isset($options['currentPage'])) {
      $options['currentPage'] = 1;
    }

    // url
    if (!isset($options['url'])) {
      $options['url'] = '?p=%page%';
    }

    // labels
    if (!isset($options['labels']['first'])) {
      $options['labels']['first'] = '&lt;&lt;';
    }
    if (!isset($options['labels']['prev'])) {
      $options['labels']['prev'] = '&lt;';
    }
    if (!isset($options['labels']['next'])) {
      $options['labels']['next'] = '&gt;';
    }
    if (!isset($options['labels']['last'])) {
      $options['labels']['last'] = '&gt;&gt;';
    }

    // maximum number of navigation links
    if (!isset($options['maxNavLinks'])) {
      $options['maxNavLinks'] = 0;
    }

    // preserve keys
    if (!isset($options['preserveKeys'])) {
      $options['preserveKeys'] = false;
    }

    $this->options = $options;
  }

  // Set the current page and slice the array
  private function selectCurrentPage() {
    $currentPage   = $this->options['currentPage'];
    $itemsPerPage  = $this->options['itemsPerPage'];
    $start         = ($currentPage - 1) * $itemsPerPage;
    $preserveKeys  = $this->options['preserveKeys'];
    $this->results = array_slice($this->items, $start, $itemsPerPage, $preserveKeys);
    $this->totalPages = ceil(count($this->items) / $itemsPerPage);
  }

  // Build up the navigation html
  private function buildNavigation() {
    $html        = '';
    $currentPage = $this->options['currentPage'];
    $totalPages  = $this->totalPages;
    $labels      = $this->options['labels'];
    $maxNavLinks = $this->options['maxNavLinks'];

    // Fixing boundaries
    $start = 1;
    $end = $totalPages;

    if ($maxNavLinks && $totalPages > $maxNavLinks) {
      // we have a restriction on the number of pages to show
      $upperBound = $currentPage - 1 + ceil($maxNavLinks / 2);
      $lowerBound = $currentPage - floor($maxNavLinks / 2);

      // check if the bounds are too high/low
      if ($upperBound >= $totalPages) {
        // too high
        $start = $lowerBound - ($upperBound - $totalPages);
      } elseif ($lowerBound <= 1) {
        // too low
        $end = $maxNavLinks;
      } else {
        // bounds are fine
        $start = $lowerBound;
        $end   = $upperBound;
      }
    }

    // fix prev/next numbers
    $prev = ($currentPage > 1) ? $currentPage - 1 : 1;
    $next = ($currentPage < $totalPages) ? $currentPage + 1 : $totalPages;

    // first
    $html .= $this->createNavigationAnchor(1, $labels['first'], 'first');

    // prev
    $html .= $this->createNavigationAnchor($prev, $labels['prev'], 'prev');

    // show page numbers in the [$start...$end] range
    for ($i = $start; $i <= $end; $i++) {

      if ($i == $currentPage) {
        $html .= $this->createSpan($i, array());
      } else {
        $html .= $this->createNavigationAnchor($i);
      }
    }

    // next
    $html .= $this->createNavigationAnchor($next, $labels['next'], 'next');

    // last
    $html .= $this->createNavigationAnchor($totalPages, $labels['last'], 'last');

    $this->navigation = $html;
  }

  // Create a navigation link
  private function createNavigationAnchor($pageNumber, $label = false, $labelKey = false) {
    $html = '';

    // set the label to the page number if it isn't supplied
    if (!$label) {
      $label = $pageNumber;
    }

    // add the html element
    $properties = array(
      'class' => (!$labelKey) ? 'page-' . $pageNumber : $labelKey,
      'href' => str_replace('%page%', $pageNumber, $this->options['url']),
    );
    $html .= $this->createHtmlElement('a', $properties, $label);

    return $html;
  }

  // Create a span
  private function createSpan($content, $properties) {
    return $this->createHtmlElement('span', $properties, $content);
  }

  // Create an html element
  private function createHtmlElement($type, $properties, $content) {
    // opening tag
    $html = '<' . $type;

    // properties
    foreach ($properties as $key => $value) {
      $html .= ' ' . $key . '="' . $value . '"';
    }

    // content
    $html .= '>'. $content;

    // closing tag
    $html .= '</' . $type . '>';

    return $html;
  }
}

?>
