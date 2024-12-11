Provides a TaxJar Order Total Module for Zen Cart.  The Order Total Module uses the TaxJar 
API to calculate the tax due. 

How it Works:

During checkout, the order subtotal and shipping total are sent to TaxJar separately via 
the TaxJar API.  TaxJar calculates the tax based on the shipping address and returns it 
to ZC for inclusinon in the order.  State, local and special assesments are included. 
States that charge (or don't charge) sales tax on shipping are accomodated.

Tested with zen cart versions 2.01 and 2.1.0 and PHP versions 8.2, 8.3. If you are using 
zc versions prior to 1.58, use the prior version of the plugin.  
    
You will likely want to disable the built-in zc tax module, ot_tax.

Visit the taxjar website to create your taxjar account and get an API key.  
https://www.taxjar.com/

Settings are in Admin/Modules/Order Total Modules/TaxJar Sales Tax. There you may 
enter your TaxJar API key, Order Prefix and list the states you have configured to 
collect in the TaxJar admin.  The Order Prefix is optional, but allows having more 
than one ZC store tied into your TaxJar account. This ensures unique order numbers within TaxJar.

If you are using Customer Tax Exempt, there is a setting to enable it.  A Customer 
may be exempted from collection by adding the state to the Customer's Tax Exempt field 
in the Admin Customers.  For multiple state exemptions, list the states like this:  
FL,NC,GA  if they are exempt from all states (blanket certificate) then you may enter ALL.


Installation:

*** BACK UP YOUR FILES! USE A TEST ZC SITE! ***

1. Extract the plugin .zip file.

2. Rename the YOUR_ADMIN folder with the name of your 
ZC admin folder.

3. Copy all three folders (includes, TaxJar and YOUR_ADMIN) into 
the root folder of your ZC store.  There are no core file overwrites.  
You don't need the two *.txt files.  The TaxJar folder is empty, but will be populated with the Taxjar API later in the install.

6. Use Composer to Install the Taxjar library:

	From a command line, navigate to your store's /public_html/taxjar folder. 
	
	    Execute this command:

	        composer require taxjar/taxjar-php
	
	    You may see a message like this:
	
	        No composer.json in current directory, do you want to use the one 
            at /home/v157lege/public_html? [Y,n]?
	    
	    Enter n and press enter.  That will cause composer to create a new 
        composer.json in the taxjar folder.

	The Taxjar/vendor folder will be created in your TaxJar folder.  T
    he vendor folder contains the files required for the Taxjar API.

5. Login to your Admin and visit the Modules/Order Total page. 
You should see the new TaxJar Sales Tax module.  Click the "Install Module" button.  You should see the configuration options appear. 

6. Template and admin file modifications:

*************** 1/2
To YOUR_TEMPLATE/templates/tpl_checkout_success_default.php, add this snippet at the end of the file.  The snippet creates the new order in TaxJar when the customer checks out:

<!--BOF Taxjar code 1/1 -->
<?php
    $b = taxjar_create_order($order,$_GET['order_id']);
?>
<!--EOF Taxjar code 1/1 -->


*************** 2/2

One change for the Admin. This allows removing or adding an order to 
TaxJar from your ZC Admin. This is for those instances when an order is 
canceled or refunded, or you want to remove test transactions, 
or a customer has sent in an exemption certificate after placing the order, etc.


In YOUR_ADMIN/orders.php find this line:

<td class="main"><?php echo $order->info['payment_method']; ?></td>
        </tr>

Then add this right above it:

<!-- BOF Taxjar mods 1/1 -->

        <?php 
        $tj = $_GET['tj'] ?? null;
        $tj_message = '';
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
				<a type="submit" class="btn btn-sm bg-success" href="<?php echo $_SERVER['PHP_SELF']."?cmd=orders&page=1&oID=".$oID."&action=edit&tj=1";?>">Add to Taxjar</a>

				<a type="submit" class="btn btn-sm bg-danger" href="<?php echo $_SERVER['PHP_SELF']."?cmd=orders&page=1&oID=".$oID."&action=edit&tj=2";?>">Delete from TaxJar</a>
            </td>
        </tr>
        <tr>
            <td colspan="2"><?php echo $tj_message; ?></td>
        </tr>
   
<!-- EOF Taxjar mods 1/1 -->

Visit your store's admin and go to Customers / Orders.  Select an order 
then click Details on the left.  You should see two new buttons on that page, 
"Add To Taxjar" and "Remove From TaxJar".  

That's all!  Please note that it takes a while for new transactions to appear 
in the TaxJar Admin so if you don't see them right away just be patient.

If you need help or have questions, please contact us through the 
Zen Cart Support Forum.

**End of Document**