<?php
/**
 * TaxJar - an order_total module for Zen Cart
 * URL: 
 * Version:
 * By: Chuck Phillips
 * @copyright Portions Copyright 2004-2006 Zen Cart Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */

class ot_taxjar {
   var $title, $output, $code, $description, $sort_order, $explanation, $_check;

   function __construct() {
      $this->code = 'ot_taxjar';
      $this->title = MODULE_ORDER_TOTAL_TAXJAR_TITLE;
      $this->description = MODULE_ORDER_TOTAL_TAXJAR_DESCRIPTION;

	  $this->sort_order = defined('MODULE_ORDER_TOTAL_TAXJAR_SORT_ORDER') ? MODULE_ORDER_TOTAL_TAXJAR_SORT_ORDER : null;
        if (null === $this->sort_order) return false;
        $this->output = [];
   }

   function process() {
       
        global $order, $currencies, $customer;
        
        //echo '<pre>';
        //echo print_r($order);
        //echo '</pre>';
        
        $taxable = order_taxable($order);
        
        if ($taxable) {

            //connect to taxjar
            $client = connect_taxjar();

            //get taxable amount
            $tax = $this->get_taxjar_tax($client,$order);
            
            $tj_tax = $tax->amount_to_collect;
            
            $state = $tax->jurisdictions->state;
            $city = ucwords(strtolower($tax->jurisdictions->city));
            $county = ucwords(strtolower($tax->jurisdictions->county));
            $rate = $tax->rate * 100;
            
            $jurisdiction = $rate."% "." ".$state." Sales Tax:";
            
            $order->info['total'] += $tj_tax;
            $this->output[] = array('title' => $jurisdiction,
                                    'text' => $currencies->format($tj_tax, true, $order->info['currency'], $order->info['currency_value']),
                                    'value' => $tj_tax);
        }
   }
    
    function get_taxjar_tax($client,$order){
    
        //global $order_total_modules;
        //$order_total_modules->output();
        //echo '<pre>';
        //echo print_r($order);
        //echo '</pre>';
        
        $subtotal = $order->info['total']-$order->info['shipping_cost'];
        
        $shipping_cost = $order->info['shipping_cost'];
              
        $zip = $order->delivery['postcode'];
        $street = $order->delivery['street_address'];
        $city = $order->delivery['city'];
        $state = convert_state($order->delivery['state'],'abbrev');
        $country = $order->delivery['country']['iso_code_2'];
            
        try{
                
            //correct From addresses to use store varibles
            $order_taxes = $client->taxForOrder([
              'to_country' => $country,
              'to_zip' => $zip,
              'to_state' => $state,
              'to_city' => $city,
              'to_street' => $street,
              'amount' => $subtotal,
              'shipping' => $shipping_cost,
    
            ]);
            }catch(Exception $e){
                
                //address error
                return false;
        }
        
            return $order_taxes;
        
    }


   function check() {
      global $db;
      if (!isset($this->_check)) {
         $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_TAXJAR_STATUS'");
         $this->_check = $check_query->RecordCount();
      }

      return $this->_check;
   }

   function keys() {
      return array('MODULE_ORDER_TOTAL_TAXJAR_STATUS', 'MODULE_ORDER_TOTAL_TAXJAR_SORT_ORDER', 'MODULE_ORDER_TOTAL_TAXJAR_API_KEY', 'MODULE_ORDER_TOTAL_TAXJAR_ORDER_PREFIX', 'MODULE_ORDER_TOTAL_TAXJAR_NEXUS_STATES', 'MODULE_ORDER_TOTAL_TAXJAR_USE_CUST_TAX_EXEMPT');
   }

   function install() {
      global $db;

        $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Enable TaxJar', 'MODULE_ORDER_TOTAL_TAXJAR_STATUS', 'True', 'Do you want to enable TaxJar processing?', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
        
        $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) VALUES ('Sort Order', 'MODULE_ORDER_TOTAL_TAXJAR_SORT_ORDER', '400', 'Sort order of display.', '6', '20', now())");
        
        $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('TaxJar API Key', 'MODULE_ORDER_TOTAL_TAXJAR_API_KEY', 'your-key-here', 'Your TaxJar API key.  Get yours from http://www.taxjar.com', '6', '10', now())");

        $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Order Prefix', 'MODULE_ORDER_TOTAL_TAXJAR_ORDER_PREFIX', '', 'Order prefix for TaxJar.  Useful if order numbers from multiple platforms might overlap.', '6', '10', now())");

        $db->Execute("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Nexus States', 'MODULE_ORDER_TOTAL_TAXJAR_NEXUS_STATES', '', 'State where you want ot collect sales tax. These must be configured in TaxJar also. Separate states with a comma. ex: GA,CA,NY,FL.', '6', '10', now())");

        $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) VALUES ('Use Customer Tax Exempt Plugin?', 'MODULE_ORDER_TOTAL_TAXJAR_USE_CUST_TAX_EXEMPT', 'False', 'Exempt customers using Customers Tax Exempt plugin.  Install CTE before enabling.', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");


   }

   function remove() {
      global $db;
      $db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
   }


}

?>
