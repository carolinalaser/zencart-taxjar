Provides a TaxJar Order Total Module for Zen Cart.  The Order Total Module uses the TaxJar 
API to calculate the tax due. 

How it Works:

During checkout, the order subtotal and shipping total are sent to TaxJar separately via 
the TaxJar API. TaxJar calculates the tax based on the shipping address and returns it 
to ZC for inclusinon in the order. State, local and special assesments are included. 
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
Very helpful if you have a dev site and don't want to "step" on live orders with your test orders.

If you are using Customer Tax Exempt, there is a setting to enable it.  A Customer 
may be exempted from collection by adding the state to the Customer's Tax Exempt field 
in the Admin Customers.  For multiple state exemptions, list the states like this:  
FL,NC,GA  if they are exempt from all states (blanket certificate) then you may enter ALL.


Installation:

*** BACK UP YOUR FILES! USE A TEST ZC SITE! ***

There are no file overwrites or merges required.

1. Extract the plugin .zip file.

2. Rename the YOUR_ADMIN folder with the name of your 
ZC admin folder.

3. Copy both folders (includes and YOUR_ADMIN) into 
the root folder of your ZC store.  There are no file overwrites.

4. Login to your Admin and visit the Modules/Order Total page. 
You should see the new TaxJar Sales Tax module.  Click the "Install Module" button.  You should see the configuration options appear. Fill in all fields.

5. Template and admin file modifications:

If you need help or have questions, please contact us through the 
Zen Cart Support Forum.

Ugrading:

*** BACK UP YOUR FILES! USE A TEST ZC SITE! ***

1. Same procedure as the install, but you will want to remove the change to YOUR_TEMPLATE/templates/tpl_checkout_success_default.php.  This is no
longer necessary since it is being handled by an observer in the newest version.

2. If you installed the admin tools in your YOUR_ADMIN/orders.php, you will want to remove those mods as well.  This is also handled by an 
observer in the newest version.

3. You can remove the old taxjar api files, located at STORE_ROOT/taxjar.  The new version uses the api located in the modules/order_total folder, and 
is supplied in the plugin files.



**End of Document**