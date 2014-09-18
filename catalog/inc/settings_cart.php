<style>
  .CodeMirror {
    height: 300px;
  }
  .CodeMirror-scroll {
    overflow-y: auto;
    overflow-x: auto;
    height: 300px;
  }
</style>
<?php
  // saved
  if (isset($_POST['submitted'])) {
    $xml = new SimpleXMLExtended('<cart/>');

    $xml->enabled = isset($_POST['enabled']) ? 'y' : 'n';

    $xml->columns->labels->name = null;
    $xml->columns->labels->name->addCData($_POST['labelname']);
    $xml->columns->labels->price = null;
    $xml->columns->labels->price->addCData($_POST['labelprice']);
    $xml->columns->labels->quantity = null;
    $xml->columns->labels->quantity->addCData($_POST['labelquantity']);
    $xml->columns->labels->total = null;
    $xml->columns->labels->total->addCData($_POST['labeltotal']);
    
    $xml->style = null;
    $xml->style->addCData($_POST['cartstyle']);
    
    $xml->currency = null;
    $xml->currency->addCData($_POST['currency']);
    $xml->language = null;
    $xml->language->addCData($_POST['language']);
    
    $xml->checkout->type = null;
    $xml->checkout->type->addCData($_POST['checkouttype']);
    $xml->checkout->email = null;
    $xml->checkout->email->addCData($_POST['checkoutemail']);
    
    $xml->shipping->flatrate = null;
    $xml->shipping->flatrate->addCData($_POST['shippingflatrate']);
    $xml->shipping->quantityrate = null;
    $xml->shipping->quantityrate->addCData($_POST['shippingquantityrate']);
    $xml->shipping->totalrate = null;
    $xml->shipping->totalrate->addCData($_POST['shippingtotalrate']);
    
    $xml->tax->rate = null;
    $xml->tax->rate->addCData($_POST['taxrate']);
    $xml->tax->shipping = null;
    $xml->tax->shipping->addCData($_POST['taxshipping']);
    
    $xml->css = null;
    $xml->css->addCData($_POST['css']);
    
    $xml->template = null;
    $xml->template->addCData($_POST['template']);
    
    $succ = (bool) $xml->saveXML($this->dataDir . '/cart.xml');
    
    // success
    if ($succ) {
      $msg = i18n_r($this->id . '/OPTIONS_UPD_SUCC');
      $isSuccess = true;
    }
    // error
    else {
      $msg = i18n_r($this->id . '/OPTIONS_UPD_FAIL');
      $isSuccess = false;
    }
  }
  
  $cart = new CatalogSettingsCart(array('file' => $this->dataDir . '/cart.xml'));
  
?>

<div class="leftsec">
  <p>
    <label for="enabled"><?php i18n($this->id . '/ENABLED'); ?>: </label>
    <input type="checkbox" name="enabled" value="y" <?php if ($cart->get('enabled')) echo 'checked="checked"'; ?>>
  </p>
  <p>
    <label for="labelname"><?php i18n($this->id . '/NAME'); ?> (<?php i18n($this->id . '/LABEL'); ?>) : </label>
    <input type="text" class="text" name="labelname" value="<?php echo $cart->get('LabelName'); ?>"/>
  </p>
  <p>
    <label for="labelprice"><?php i18n($this->id . '/PRICE'); ?> (<?php i18n($this->id . '/LABEL'); ?>) : </label>
    <input type="text" class="text" name="labelprice" value="<?php echo $cart->get('LabelPrice'); ?>"/>
  </p>
  <p>
    <label for="labelquantity"><?php i18n($this->id . '/QUANTITY'); ?> (<?php i18n($this->id . '/LABEL'); ?>) : </label>
    <input type="text" class="text" name="labelquantity" value="<?php echo $cart->get('LabelQuantity'); ?>"/>
  </p>
  <p>
    <label for="labeltotal"><?php i18n($this->id . '/TOTAL'); ?> (<?php i18n($this->id . '/LABEL'); ?>) : </label>
    <input type="text" class="text" name="labeltotal" value="<?php echo $cart->get('LabelTotal'); ?>"/>
  </p>
  <p>
    <label for="checkoutemail"><?php i18n($this->id . '/EMAIL'); ?> : </label>
    <input type="text" class="text" name="checkoutemail" value="<?php echo $cart->get('CheckoutEmail'); ?>"/>
  </p>
  <p>
    <label for="currency"><?php i18n($this->id . '/CURRENCY'); ?> : </label>
    <input type="text" class="text" name="currency" maxlength="3" value="<?php echo $cart->get('Currency'); ?>"/>
  </p>
  <p>
    <label for="language"><?php i18n($this->id . '/LANGUAGE'); ?> : </label>
    <input type="text" class="text" name="language" value="<?php echo $cart->get('Language'); ?>"/>
  </p>
</div>
<div class="rightsec">
  <p>
    <label for="taxrate"><?php i18n($this->id . '/TAX_RATE'); ?> : </label>
    <input type="text" class="text" name="taxrate" value="<?php echo $cart->get('TaxRate'); ?>"/>
  </p>
  <p>
    <label for="taxshipping"><?php i18n($this->id . '/TAX_SHIPPING'); ?> : </label>
    <input type="text" class="text" name="taxshipping" value="<?php echo $cart->get('TaxShipping'); ?>"/>
  </p>
  <p>
    <label for="cartystyle"><?php i18n($this->id . '/STYLE'); ?>: </label>
    <select class="text" name="cartstyle">
      <option value="div" <?php if ($cart->get('CartStyle') == 'div') echo 'selected'; ?>>
        div
      </option>
      <option value="table" <?php if ($cart->get('CartStyle') == 'table') echo 'selected'; ?>>
        table
      </option>
    </select>
  </p>
  <p>
    <label for="checkouttype"><?php i18n($this->id . '/TYPE'); ?>: </label>
    <select class="text" name="checkouttype">
      <option value="PayPal" <?php if ($cart->get('CheckoutType') == 'PayPal') echo 'selected'; ?>>
        <?php i18n($this->id . '/PAYPAL'); ?>
      </option>
      <option value="AmazonPayments" <?php if ($cart->get('CheckoutType') == 'AmazonPayments') echo 'selected'; ?>>
        <?php i18n($this->id . '/AMAZONPAYM'); ?>
      </option>
      <option value="GoogleCheckout" <?php if ($cart->get('CheckoutType') == 'GoogleCheckout') echo 'selected'; ?>>
        <?php i18n($this->id . '/GOOGLECHECK'); ?>
      </option>
    </select>
  </p>
  <p>
    <label for="shippingflatrate"><?php i18n($this->id . '/FLAT_RATE'); ?> : </label>
    <input type="text" class="text" name="shippingflatrate" value="<?php echo $cart->get('ShippingFlatRate'); ?>"/>
  </p>
  <p>
    <label for="shippingquantityrate"><?php i18n($this->id . '/QUANTITY_RATE'); ?> : </label>
    <input type="text" class="text" name="shippingquantityrate" value="<?php echo $cart->get('ShippingQuantityRate'); ?>"/>
  </p>
  <p>
    <label for="shippingtotalrate"><?php i18n($this->id . '/TOTAL_RATE'); ?> : </label>
    <input type="text" class="text" name="shippingtotalrate" value="<?php echo $cart->get('ShippingTotalRate'); ?>"/>
  </p>
</div>
<div class="clear"></div>

<p>
  <h4 style="font-weight: bold;"><?php i18n($this->id . '/CSS'); ?> : </h4>
  <textarea id="cartcss" name="css" style="height: 200px !important;"><?php echo $cart->get('Css'); ?></textarea>
  <?php $textarea = 'cartcss'; $mode = 'text/css'; include('codemirror.php'); ?>
</p>
<p>
  <h4 style="font-weight: bold;"><?php i18n($this->id . '/TEMPLATE'); ?> : </h4>
  <textarea id="carttemplate" name="template" style="height: 200px !important;"><?php echo $cart->get('CartTemplate'); ?></textarea>
  <?php $textarea = 'carttemplate'; include('codemirror.php'); ?>
</p>
