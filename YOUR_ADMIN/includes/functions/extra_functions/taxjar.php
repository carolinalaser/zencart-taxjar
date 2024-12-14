<?php

function create_order($orderid)
{

    $store = MODULE_ORDER_TOTAL_TAXJAR_ORDER_PREFIX;

    $order = new order($orderid);

    //connect to taxjar
    $client = connect_taxjar();

    try {
        //see if this order already exists
        $tj_order = $client->showOrder($store . $orderid);
        $msg = 'Order ' . $orderid . ' Exists.';
    } catch (Exception $e) {

        //does not exist, create order

        $date = $order->info['date_purchased'];
        $country = $order->delivery['country']['iso_code_2'];
        $zip = $order->delivery['postcode'];
        $state = convert_state($order->delivery['state'], 'abbrev');
        $city = $order->delivery['city'];
        $street = $order->delivery['street_address'];

        $amount = get_ot_value($order->totals, "ot_total") - get_ot_value($order->totals, "ot_taxjar");

        $shipping = get_ot_value($order->totals, "ot_shipping");
        $tax = get_ot_value($order->totals, "ot_taxjar");


        $tj_order = $client->createOrder([
            'transaction_id' => $store . $orderid,
            'transaction_date' => $date,
            'to_country' => $country,
            'to_zip' => $zip,
            'to_state' => $state,
            'to_city' => $city,
            'to_street' => $street,
            'amount' => $amount,
            'shipping' => $shipping,
            'sales_tax' => $tax
        ]);

        $msg = 'Order ' . $orderid . ' created in TaxJar.';
    }

    //echo '<pre>';
    //echo print_r($tj_order);
    //echo '</pre>';

    return $msg;
}

function taxjar_status($oID)
{
    $pre = MODULE_ORDER_TOTAL_TAXJAR_ORDER_PREFIX;

    //connect to taxjar
    $client = connect_taxjar();

    try {
        //get this orders status in tj
        $tj_order = $client->showOrder($pre . $oID);
        
        global $currencies;

        $msg = $oID . ' recorded ' . $currencies->format($tj_order->sales_tax) . ' to ' . $tj_order->to_state;
    } catch (Exception $e) {
        $msg = $oID . ' no tax recorded';
    }
    return $msg;
}

function delete_order($oID)
{

    $store = MODULE_ORDER_TOTAL_TAXJAR_ORDER_PREFIX;

    $client = connect_taxjar();

    $client->deleteOrder($store . $oID);

    $msg = 'Order ' . $oID . ' removed from TaxJar.';
    return $msg;
}

function connect_taxjar()
{

    require $_SERVER['DOCUMENT_ROOT'] . '/includes/modules/order_total/taxjar/vendor/autoload.php';
    
    $client = TaxJar\Client::withApiKey(MODULE_ORDER_TOTAL_TAXJAR_API_KEY);

    return $client;
}

function get_ot_value($array, $key)
{

    foreach ($array as $a) {

        if ($a['class'] == $key) {

            return $a['value'];
        }
    }

    return 0.0;
}

function convert_state($name, $to)
{

    if (strlen($name) == 2) return $name;

    $states = array(
        array('name' => 'Alabama', 'abbrev' => 'AL'),
        array('name' => 'Alaska', 'abbrev' => 'AK'),
        array('name' => 'Arizona', 'abbrev' => 'AZ'),
        array('name' => 'Arkansas', 'abbrev' => 'AR'),
        array('name' => 'California', 'abbrev' => 'CA'),
        array('name' => 'Colorado', 'abbrev' => 'CO'),
        array('name' => 'Connecticut', 'abbrev' => 'CT'),
        array('name' => 'Delaware', 'abbrev' => 'DE'),
        array('name' => 'Florida', 'abbrev' => 'FL'),
        array('name' => 'Georgia', 'abbrev' => 'GA'),
        array('name' => 'Hawaii', 'abbrev' => 'HI'),
        array('name' => 'Idaho', 'abbrev' => 'ID'),
        array('name' => 'Illinois', 'abbrev' => 'IL'),
        array('name' => 'Indiana', 'abbrev' => 'IN'),
        array('name' => 'Iowa', 'abbrev' => 'IA'),
        array('name' => 'Kansas', 'abbrev' => 'KS'),
        array('name' => 'Kentucky', 'abbrev' => 'KY'),
        array('name' => 'Louisiana', 'abbrev' => 'LA'),
        array('name' => 'Maine', 'abbrev' => 'ME'),
        array('name' => 'Maryland', 'abbrev' => 'MD'),
        array('name' => 'Massachusetts', 'abbrev' => 'MA'),
        array('name' => 'Michigan', 'abbrev' => 'MI'),
        array('name' => 'Minnesota', 'abbrev' => 'MN'),
        array('name' => 'Mississippi', 'abbrev' => 'MS'),
        array('name' => 'Missouri', 'abbrev' => 'MO'),
        array('name' => 'Montana', 'abbrev' => 'MT'),
        array('name' => 'Nebraska', 'abbrev' => 'NE'),
        array('name' => 'Nevada', 'abbrev' => 'NV'),
        array('name' => 'New Hampshire', 'abbrev' => 'NH'),
        array('name' => 'New Jersey', 'abbrev' => 'NJ'),
        array('name' => 'New Mexico', 'abbrev' => 'NM'),
        array('name' => 'New York', 'abbrev' => 'NY'),
        array('name' => 'North Carolina', 'abbrev' => 'NC'),
        array('name' => 'North Dakota', 'abbrev' => 'ND'),
        array('name' => 'Ohio', 'abbrev' => 'OH'),
        array('name' => 'Oklahoma', 'abbrev' => 'OK'),
        array('name' => 'Oregon', 'abbrev' => 'OR'),
        array('name' => 'Pennsylvania', 'abbrev' => 'PA'),
        array('name' => 'Rhode Island', 'abbrev' => 'RI'),
        array('name' => 'South Carolina', 'abbrev' => 'SC'),
        array('name' => 'South Dakota', 'abbrev' => 'SD'),
        array('name' => 'Tennessee', 'abbrev' => 'TN'),
        array('name' => 'Texas', 'abbrev' => 'TX'),
        array('name' => 'Utah', 'abbrev' => 'UT'),
        array('name' => 'Vermont', 'abbrev' => 'VT'),
        array('name' => 'Virginia', 'abbrev' => 'VA'),
        array('name' => 'Washington', 'abbrev' => 'WA'),
        array('name' => 'West Virginia', 'abbrev' => 'WV'),
        array('name' => 'Wisconsin', 'abbrev' => 'WI'),
        array('name' => 'Wyoming', 'abbrev' => 'WY')
    );

    $return = false;
    foreach ($states as $state) {
        if ($to == 'name') {
            if (strtolower($state['abbrev']) == strtolower($name)) {
                $return = $state['name'];
                break;
            }
        } else if ($to == 'abbrev') {
            if (strtolower($state['name']) == strtolower($name)) {
                $return = strtoupper($state['abbrev']);
                break;
            }
        }
    }
    return $return;
}

function order_taxable($order)
{

    //taxjar enabled in admin?
    $tj_enabled = MODULE_ORDER_TOTAL_TAXJAR_STATUS;

    if ($tj_enabled == 'False') {
        return false;
    }

    $delivery_state = $order->delivery['state'];

    //echo 'state '.$delivery_state;

    $delivery_state = convert_state($delivery_state, 'abbrev');
    $email = $order->customer['email_address'];

    //echo $email;
    //echo '<pre>';
    //echo print_r($order);
    //echo '</pre>';

    //is there a nexus at the delivery address?
    $tj_nexus = check_nexus($delivery_state);

    if (!$tj_nexus) {
        return false;
    }

    //check for tax exempt customer if enabled
    if (MODULE_ORDER_TOTAL_TAXJAR_USE_CUST_TAX_EXEMPT == 'True') {

        $customer_exempt = customer_exempt($email, $delivery_state);

        if ($customer_exempt) {
            return false;
        } else {
            return true;
        }
    } else {
        return true;
    }
}

function check_nexus($delivery_state)
{

    $nexus_regions = explode(",", MODULE_ORDER_TOTAL_TAXJAR_NEXUS_STATES);

    foreach ($nexus_regions as $r) {

        if ($r == $delivery_state) {

            return true;
        }
    }
    return false;
}

function customer_exempt($email, $delivery_state)
{
    //uses email b/c id is not always available in $order
    global $db;

    if (!$email) {

        return false;
    }

    $sql = 'select customers_tax_exempt from ' . TABLE_CUSTOMERS . ' where customers_email_address = "' . $email . '"';

    $cust = $db->Execute($sql);

    $rec = $cust->RecordCount();

    if ($rec > 0) {

        $states = $cust->fields['customers_tax_exempt'];

        //see if delivery state is anywhere in the tax_exempt string
        if (strpos($states, $delivery_state) !== false) {
            return true;
        }
    }

    return false;
}
