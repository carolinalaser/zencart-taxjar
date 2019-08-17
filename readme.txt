Copy all the files in the usual manner.  No core overwrites.

Install the TaxJar library using Composer.  Details here: https://github.com/taxjar/taxjar-php

Then two easy mods to two template files:

*************** 1/2
To YOUR_TEMPLATE/templates/tpl_checkout_success_default.php, add ths at the end of the file.

<!--BOF Taxjar code 1/1 -->
<?php
    $b = taxjar_create_order($order,$_GET['order_id']);
?>
<!--EOF Taxjar code 1/1 -->


*************** 2/2

and YOUR_ADMIN/orders.php, add this:

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

above this line:

<td class="main"><?php echo $order->info['payment_method']; ?></td>
        </tr>
        
***  US Addresses only.  Has not been tested with addresss outside the US.  Internation addesse wont be charged tax. ***
