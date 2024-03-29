Provides a TaxJar order total module for Zen Cart.  Tested with version 1.5.5f but should work with other versions.  You will need to disable the built-in zc tax module ot_tax.  Visit the taxjar website to create your taxjar account.  

Settings are in the ot_taxjar module.  Enter your taxjar API key, Order Prefix and list the states you have configured to collect in TaxJar.  The Order Prefix is optional, but allows having more than one ZC store tied into your TaxJar account.  This ensures unique order numbers within TaxJar.

If you are using Customer Tax Exempt, there is a setting to enable it.  A Customer may be exempted from collection by adding the state to the Customer Tax Exempt field in the Admin Customers.  For multiple state exemptions, list the states like this:  FL,NC,GA  if they are exempt from all states (blanket cert) then enter ALL.


Installation:

*** BACK UP YOUR FILES! ***

Copy all the files in the usual manner to your ZC store.  No core overwrites.

Install the TaxJar library using Composer.  Details here: https://github.com/taxjar/taxjar-php

Then two easy mods to two template files:

*************** 1/2
To YOUR_TEMPLATE/templates/tpl_checkout_success_default.php, add ths at the end of the file.  This snippet creates the new order in taxjar when the customer checks out.

<!--BOF Taxjar code 1/1 -->
<?php
    $b = taxjar_create_order($order,$_GET['order_id']);
?>
<!--EOF Taxjar code 1/1 -->


*************** 2/2

OPTIONAL

The following adds two buttons to the Admin Orders page:  One removes the order from TaxJar, one adds the order to TaxJar.  This is for those instances when an order is canceled or refunded, or a customer has sent in an exemption certificate after placing the order. If you choose not to install this piece, you can do it through the TaxJar interface. 

In YOUR_ADMIN/orders.php find this line:

<td class="main"><?php echo $order->info['payment_method']; ?></td>
        </tr>

Then add this right above it:

   <!-- BOF Taxjar mods 1/1 -->
        <?php 
        $tj = $_GET['tj'];
        
        if($tj==1){
            $r = create_order($order,$oID);
            $tj_message = $r;
        }
        
        if($tj==2){
            $r = delete_order($oID);
            $tj_message = $r;
        }
            $store = MODULE_ORDER_TOTAL_TAXJAR_ORDER_PREFIX;
        ?>

        <tr>
            <td colspan="2">
                <a type="submit" class="btn btn-sm bg-success" href="<?php echo $_SERVER['PHP_SELF']."?page=1&oID=".$oID."&action=edit&tj=1";?>">Add to Taxjar</a>

                <a type="submit" class="btn btn-sm bg-danger" href="<?php echo $_SERVER['PHP_SELF']."?page=1&oID=".$oID."&action=edit&tj=2";?>">Delete from TaxJar</a>
            </td>
        </tr>
        <tr>
            <td colspan="2"><?php echo $tj_message; ?></td>
        </tr>
   <!-- EOF Taxjar mods 1/1 -->
