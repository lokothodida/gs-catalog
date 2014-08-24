<?php

class CatalogCart {
  private $cart;

  public function __construct($file) {
    if (file_exists($file)) {
      $xml = simplexml_load_file($file);
      
      $this->cart['enabled'] = (string) $xml->enabled;
      
      $this->cart['labelname'] = (string) $xml->columns->labels->name;
      $this->cart['labelprice'] = (string) $xml->columns->labels->price;
      $this->cart['labelquantity'] = (string) $xml->columns->labels->quantity;
      $this->cart['labeltotal'] = (string) $xml->columns->labels->total;
      
      $this->cart['cartstyle'] = (string) $xml->style;
      
      $this->cart['currency'] = (string) $xml->currency;
      $this->cart['language'] = (string) $xml->language;
      
      $this->cart['checkouttype'] = (string) $xml->checkout->type;
      $this->cart['checkoutemail'] = (string) $xml->checkout->email;
      
      $this->cart['shippingflatrate'] = (string) $xml->shipping->flatrate;
      $this->cart['shippingquantityrate'] = (string) $xml->shipping->quantityrate;
      $this->cart['shippingtotalrate'] = (string) $xml->shipping->totalrate;
      
      $this->cart['taxrate'] = (string) $xml->tax->rate;
      $this->cart['taxshipping'] = (string) $xml->tax->shipping;
      
      $this->cart['css'] = (string) $xml->css;
      $this->cart['carttemplate'] = (string) $xml->template;
    }
  }
  
  public function __call($name, $args) {
    if (substr($name, 0, 3) == 'get') {
      $field = strtolower(substr($name, 3));
      if (isset($this->cart[$field])) {
        return $this->cart[$field];
      }
      else return null;
    }
  }
  
  public function getCart() {
    return $this->cart;
  }
}

?>
