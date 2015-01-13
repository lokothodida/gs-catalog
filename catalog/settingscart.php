<h3 class="floated"><?php i18n('catalog/CART'); ?></h3>
<div class="edit-nav">
  <p>
    <a href="<?php echo CATALOGADMINURL; ?>&settings=cart" class="current"><?php i18n('catalog/CART'); ?></a>
    <a href="<?php echo CATALOGADMINURL; ?>&settings=fields"><?php i18n('catalog/FIELDS'); ?></a>
    <a href="<?php echo CATALOGADMINURL; ?>&settings=theme"><?php i18n('catalog/THEME'); ?></a>
    <a href="<?php echo CATALOGADMINURL; ?>&settings"><?php i18n('catalog/SETTINGS'); ?></a>
  </p>
  <div class="clear"></div>
</div>

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

<form method="post">
  <div class="leftsec">
    <p>
      <label for="enabled"><?php i18n('catalog/ENABLED'); ?>: </label>
      <input type="checkbox" name="enabled" value="y" <?php if ($cart['enabled'] == 'y') echo 'checked="checked"'; ?>>
    </p>
    <p>
      <label for="labelname"><?php i18n('catalog/NAME'); ?> (<?php i18n('catalog/LABEL'); ?>) : </label>
      <input type="text" class="text" name="labelname" value="<?php echo $cart['labelname']; ?>"/>
    </p>
    <p>
      <label for="labelprice"><?php i18n('catalog/PRICE'); ?> (<?php i18n('catalog/LABEL'); ?>) : </label>
      <input type="text" class="text" name="labelprice" value="<?php echo $cart['labelprice']; ?>"/>
    </p>
    <p>
      <label for="labelquantity"><?php i18n('catalog/QUANTITY'); ?> (<?php i18n('catalog/LABEL'); ?>) : </label>
      <input type="text" class="text" name="labelquantity" value="<?php echo $cart['labelquantity']; ?>"/>
    </p>
    <p>
      <label for="labeltotal"><?php i18n('catalog/TOTAL'); ?> (<?php i18n('catalog/LABEL'); ?>) : </label>
      <input type="text" class="text" name="labeltotal" value="<?php echo $cart['labeltotal']; ?>"/>
    </p>
    <p>
      <label for="checkoutemail"><?php i18n('catalog/EMAIL'); ?> : </label>
      <input type="text" class="text" name="checkoutemail" value="<?php echo $cart['checkoutemail']; ?>"/>
    </p>
    <p>
      <label for="currency"><?php i18n('catalog/CURRENCY'); ?> : </label>
      <input type="text" class="text" name="currency" maxlength="3" value="<?php echo $cart['currency']; ?>"/>
    </p>
    <p>
      <label for="language"><?php i18n('catalog/LANGUAGE'); ?> : </label>
      <input type="text" class="text" name="language" value="<?php echo $cart['language']; ?>"/>
    </p>
  </div>
  <div class="rightsec">
    <p>
      <label for="taxrate"><?php i18n('catalog/TAX_RATE'); ?> : </label>
      <input type="text" class="text" name="taxrate" value="<?php echo $cart['taxrate']; ?>"/>
    </p>
    <p>
      <label for="taxshipping"><?php i18n('catalog/TAX_SHIPPING'); ?> : </label>
      <input type="text" class="text" name="taxshipping" value="<?php echo $cart['taxshipping']; ?>"/>
    </p>
    <p>
      <label for="cartystyle"><?php i18n('catalog/STYLE'); ?>: </label>
      <select class="text" name="cartstyle">
        <option value="div" <?php if ($cart['cartstyle'] == 'div') echo 'selected'; ?>>
          div
        </option>
        <option value="table" <?php if ($cart['cartstyle'] == 'table') echo 'selected'; ?>>
          table
        </option>
      </select>
    </p>
    <p>
      <label for="checkouttype"><?php i18n('catalog/TYPE'); ?>: </label>
      <select class="text" name="checkouttype">
        <option value="PayPal" <?php if ($cart['checkouttype'] == 'PayPal') echo 'selected'; ?>>
          <?php i18n('catalog/PAYPAL'); ?>
        </option>
        <option value="AmazonPayments" <?php if ($cart['checkouttype'] == 'AmazonPayments') echo 'selected'; ?>>
          <?php i18n('catalog/AMAZONPAYM'); ?>
        </option>
        <option value="GoogleCheckout" <?php if ($cart['checkouttype'] == 'GoogleCheckout') echo 'selected'; ?>>
          <?php i18n('catalog/GOOGLECHECK'); ?>
        </option>
      </select>
    </p>
    <p>
      <label for="shippingflatrate"><?php i18n('catalog/FLAT_RATE'); ?> : </label>
      <input type="text" class="text" name="shippingflatrate" value="<?php echo $cart['shippingflatrate']; ?>"/>
    </p>
    <p>
      <label for="shippingquantityrate"><?php i18n('catalog/QUANTITY_RATE'); ?> : </label>
      <input type="text" class="text" name="shippingquantityrate" value="<?php echo $cart['shippingquantityrate']; ?>"/>
    </p>
    <p>
      <label for="shippingtotalrate"><?php i18n('catalog/TOTAL_RATE'); ?> : </label>
      <input type="text" class="text" name="shippingtotalrate" value="<?php echo $cart['shippingtotalrate']; ?>"/>
    </p>
  </div>
  <div class="clear"></div>

  <p>
    <h4 style="font-weight: bold;"><?php i18n('catalog/CSS'); ?> : </h4>
    <textarea id="cartcss" name="css" style="height: 200px !important;"><?php echo $cart['css']; ?></textarea>
    <?php $codemirrorId = 'cartcss'; $mode = 'text/css'; include('codemirror.php'); ?>
  </p>
  <p>
    <h4 style="font-weight: bold;"><?php i18n('catalog/TEMPLATE'); ?> : </h4>
    <textarea id="carttemplate" name="template" style="height: 200px !important;"><?php echo $cart['template']; ?></textarea>
    <?php $codemirrorId = 'carttemplate'; include('codemirror.php'); ?>
  </p>

  <input type="hidden" name="editCart"/>
  <div id="submit_line">
    <span><input id="page_submit" class="submit" name="submitted" value="<?php i18n('BTN_SAVECHANGES'); ?>" type="submit"></span>
    /
    <a href="<?php echo $cancelUrl; ?>" class="cancel"><?php i18n('CANCEL'); ?></a>
  </div>
</form>