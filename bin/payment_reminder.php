<?php

    require_once dirname(__FILE__) . "/../bootstrap.php";
    $logfile = "emails_".date("Ym").".txt"; 
    $myConfig = oxRegistry::getConfig();
    
    $nDays = ((int)$myConfig->getConfigParam('eshop_days_for_reminder')+1)*86400;
    $deadline = (int)$myConfig->getConfigParam('days_for_cancel_order')*86400;
    
    // failsafe while while not all forms are filled 
    if($nDays == 0 || $deadline == 0 || !$myConfig->getConfigParam('eshop_days_for_reminder') || !$myConfig->getConfigParam('days_for_cancel_order'))
    {
        $failEmail = oxNew('email');
        $failEmail->sendEmail($myConfig->getConfigParam( 'adminEmail' ), '[ERROR] Script for payment reminders and order page!','Required fields for the script to run are not filled!');
        die;
        
    }
    $deadline = date("Y-m-d",time()-$deadline);
    $date_from = date( "Y-m-d" ,time()-$nDays).' 00:00:00';
    $date_to = date( "Y-m-d" ,time()-$nDays).' 23:59:59';
    
    $msg .= "Loading orders with online payment and wire transfer payment: " . date( "d.m.Y H:i:s" ) . "\n";
    
    $oDb = oxDb::getDb();
   
    $sQ = "SELECT o.ID FROM order o
        WHERE (o.PAID IN ('0000-00-00 00:00:00', '0000-00-00')OR o.PAID IS NULL) AND o.CANCEL = 0 
        AND o.PAYMENTTYPE IN('payadvance', 'payonline') AND o.ORDERDATE >= '".$date_from."' AND o.ORDERDATE <= '".$date_to."' AND o.FOLDER = 'ORDERFOLDER_WAITING_PAYMENT'";
    
    try 
    {
        $unpaid = $oDb->getAll($sQ);
        $msg .= "Total " . count($unpaid) ." orders.\n";
        
    } 
    catch (Exception $ex) 
    {
        $msg .= "Error.".$oDb->errorMsg()."\n";
    }
    if(count($unpaid))
    {
        $eError = 0;
        $j = 0;
        $oEmail = oxNew( "email" );
        foreach($unpaid as $value )
        {
           
            $oOrder = oxNew('order');
            $oOrder->load($value[0]);
            if($oEmail->sendReminder( $oOrder )){$j++;}
            else{$eError++;$oErrors[] = 'Error sending email for order:'.$oOrder->order__ordernumber->value.'('.$oEmail->getErrorInfo().')';}
        }
        $msg .= 'Sent '.$j.' emails.'.($eError>0?'Number of sending errors:'.$eError.'('.implode(",",$oErrors).')':"").'\n';
    }
    else{$msg.='There are no orders.\n';}
        
    // Loading unpaid orders Day+8
    $msg .= 'Loading orders that have not been paid for 8 days...\n';
    
    $sQuery = "SELECT o.ID ID FROM order o
        WHERE (o.PAID IN ('0000-00-00 00:00:00', '0000-00-00')OR o.PAID IS NULL) AND o.CANCEL = 0 
        AND o.PAYMENTTYPE IN('payadvance', 'payonline') AND o.ORDERDATE <= '".$deadline."' AND o.FOLDER = 'ORDERFOLDER_WAITING_PAYMENT'";
    
    try 
    {

        $cancelOrders = $oDb->getAll($sQuery);
        $msg .= "Total " . count($cancelOrders) ." orders.\n";
        
    } 
    catch (Exception $ex) 
    {
        $msg .= "Error.".$oDb->errorMsg()."\n";
    }
   
    if(count($cancelOrders))
    {   
        $i=0;
            foreach( $cancelOrders as $value )
            {
                $stornoOrder = oxNew('order');
                $stornoOrder->load($value[0]);
                
                if(!$resStorno = $stornoOrder->cancelOrder())
                {
                    $stornoErrors[] = $stornoOrder->order__ordernr->value.',\n';
                }
                else
                {
                    $ids[] = $value[0];
                    $i++; 
                }
                
            }
            $j=0;
            $eError = 0;
            $sEmail = oxNew('email');
            foreach($ids as $oOrder)
            {
              $sOrder = oxNew('order');
              $sOrder->load($oOrder);
              if($sEmail->sendReminder( $sOrder, true)){$j++;}
              else{$eError++;$eErrors[]='Error sending email to order:'.$sOrder->order__ordernr->value.'('.$sEmail->getErrorInfo().')';}
            }
            $msg .= "Total cancelled " . count($i) ." orders.".($stornoErrors?'Errors when cancelling:('.$stornoErrors.')':'')."Sent ".$j."emails.".($eError>0?'Number of errors while sending:'.$eError.'('.implode(",",$eErrors).')':"")."\n";
    }
      else{$msg.='No orders found.\n';}
    
    echo($msg);exit;
   
    
    if ( mb_substr( $msg, 0, 6, "UTF-8" ) == "<br />" )
        $zprava = "<br />@M@ " . mb_substr( $msg, 6, mb_strlen( $msg, "UTF-8" ), "UTF-8" );
    else $msg = "@M@ " . $msg;
      
    if ( file_put_contents( dirname(__FILE__) . '/../log/' . $logfile, $msg, FILE_APPEND ) === false )
    {
      echo "[ERRRO WHEN FILE SAVING!]";
    }
    exit;
