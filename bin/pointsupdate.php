<?php

    require_once dirname(__FILE__) . "/../bootstrap.php";
    $logfile = "loyaltypoints_update_".date("Ym").".txt";
    $msg .= "Start checking...: " . date( "d.m.Y H:i:s" ) . "\n";
    
    $oDb = oxDb::getDb();
    try 
    {
            $oDb->startTransaction();
            $qQ = "SELECT tr.ID ID, tr.USERID USERID,tr.CARDID CARDID, tr.POINTS POINTS FROM cardtransactions tr 
            LEFT JOIN loyalcards k ON k.ID = tr.CARDID AND k.USERID  = tr.USERID 
            WHERE 
            tr.CARDID != '' AND 
            tr.TRANSACTION_TYPE ='+' AND 
            tr.ACTIVE = 0 AND 
            DATE_ADD(tr.INSERT_DATE, INTERVAL 1 DAY) < NOW() AND 
            tr.DEACTIVATION_DATE IS NULL AND 
            k.ACTIVE = 1 AND 
            k.ACTIVATION_DATE IS NOT NULL";
        
        
        $selected = $oDb->getAll($qQ);
        foreach($selected as $value)
        {
            $idS[] = $value[0];
            $users[] = $value[1];
            $cards[] = array("id" => $value[2], "points" => $value[3]);
        }

        $activate = "UPDATE cardstransactions SET ACTIVE = 1, ACTIVATION_DATE = NOW() WHERE ID IN('".implode("','",$idS)."');";
        
        $oDb->execute($activate);

        //add points to cards
        foreach($cards as $card)
        {
            $sQuery = "UPDATE loyaltycards SET POINTS = POINTS + ".$card["points"]." WHERE ID = '".$card["id"]."'";
            $oDb->execute($sQuery);
        }
        $oDb->commitTransaction();
        $msg .= "Total " . count($selected) ." transaction was activated.\n";
    } 
    catch (Exception $ex) 
    {
        $oDb->rollbackTransaction();
        $msg .= "Error:".$oDb->errorMsg()."\n";
    }
      
    if ( mb_substr( $msg, 0, 6, "UTF-8" ) == "<br />" )
        $message = "<br />@M@ " . mb_substr( $msg, 6, mb_strlen( $msg, "UTF-8" ), "UTF-8" );
    else $msg = "@M@ " . $msg;
      
    if ( file_put_contents( dirname(__FILE__) . '/../log/' . $logfile, $msg, FILE_APPEND ) === false )
    {
      echo "[Error when during file saving!]";
    }
    exit;
