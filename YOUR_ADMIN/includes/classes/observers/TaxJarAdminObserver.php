<?php

class TaxJarAdminObserver extends base
{
    public function __construct()
    {
        global $zco_notifier;
        $zco_notifier->attach($this, array('NOTIFY_ADMIN_ORDERS_MENU_BUTTONS'));
        $zco_notifier->attach($this, array('NOTIFY_ORDER_AFTER_SEND_ORDER_EMAIL'));
    }

    public function update($class, $eventID, $paramsArray, &$contents)
    {
        // Create buttons for Add / Remove from Tax jar
        // also process call backs
        if ($eventID === 'NOTIFY_ADMIN_ORDERS_MENU_BUTTONS') {

            $orderID = $paramsArray->orders_id;
            $action = $_GET['action'] ?? null;

            if ($action == 'tjadd') {
                $r = create_order($orderID);

            }elseif($action == 'tjremove') {
                $r = delete_order($orderID);

            }else{
                $r = taxjar_status($orderID);

            }
            
            $customButtonHtml = '';
            $customButtonHtml =  '<div class="card"><div class=card-header><strong>Taxjar: ' . $r . '</strong></div>';
            $customButtonHtml .= '<a type="button" class="btn btn-success m-2" href="/LPadmin16/index.php?cmd=orders&action=tjadd&oID=' . $orderID . '">Add</a>';
            $customButtonHtml .= '<a type="button" class="btn btn-danger m-2" href="/LPadmin16/index.php?cmd=orders&action=tjremove&oID=' . $orderID . '">Remove</a>';
            $customButtonHtml .= '</div>';

            $contents[] = [
                'align' => 'text-center',
                'text' => $customButtonHtml,
            ];

        }

        // new checkout, add to taxjar
        if ($eventID === 'NOTIFY_ORDER_AFTER_SEND_ORDER_EMAIL') {

            $orderID = $paramsArray->orders_id;
            $r = create_order($orderID);

        }
    }
}
