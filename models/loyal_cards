<?php

class loyal_cards extends zzzBase
{
    private $_Error = null;
    
    public function __construct()
    {
      parent::__construct();
      $this->init( 'loyalcards' );
    }

    public function loadByCardNr( $karta )
    {
      return $this->load( $karta );
    }

    public function loadByUser( $userid )
    {
      $db = oxDb::getDb();
      $karta = $db->getOne("SELECT userid FROM loyal_cards WHERE OXUSERID = '".$userid."'");
      return $this->load( $karta );
    }

    public function savePointsOnCard($points,$typtransakce=null,$active=null,$extid=null, $note = null)
    {
      $this->load($this->getId());
      $this->loyalcards__points = new oxField($this->loyalcards__points->value + $points);
      if($this->update()){return true;}
      else{return false;}
    }

    public function findCard( $user )
    {
      $db = oxDb::getDb();
      $result = $db->getone("SELECT * from loyalcards WHERE OXUSERID = '".$user->getId()."';");
      if ($result)
      {
          $this->loadByCardNr($result["OXID"]);
          if ($this->loyalvards__active == 1){$active = true;}else{$active = false;}
          $data = array("cardid" => $result["ID"], "active" => $aktivni);
      }
      return $data;
    }

    public function activateTheCard($cardNumber, $userId=null)
    {
        $oDb = oxDb::getDb();

        if ( !$cardNumber ){$this->_Error = 'Card number is required';return false;}
        if ( !$userId ){$this->_Error = 'User id is required';return false;}

        try
        {
            $oDb->startTransaction();
            $result = $oDb->getAll("SELECT active, userid FROM loyalcards WHERE id = '".$cardNumber."'");
            if (sizeof($result) == 0 ){$this->_Error = 'Card not found';return false;}
            $cActive = $result[0][0];
            if ($cActive == '1'){$this->_Error = "Card is already activated";return false;}

            $fQuery = "UPDATE loyalcards SET active = 1, userid = '" . $userId . "', activation_date = NOW() WHERE id = '".$cardNumber."'";
            $oDb->execute($fQuery);
            $oDb->commitTransaction();
            $transaction = oxNew('cardtransactions');
            $transaction->savePoints('', $userId,$cardNumber,0,'+', 1, null, 'CARD_ACTIVATION');

            // adding cardid in previous user`s transactions
            $cUpdate = "UPDATE cardtransactions SET cardid = '".$cardNuber."' WHERE userid = '".$userId."' AND (cardid = '' OR cardid IS NULL) ";

            $oDb->execute($cUpdate);
            return true;
       }
       catch (ADOBD_Exception $ex)
       {
           $oDb->rollbackTransaction();
           $this->_Error = "sql error";
           return false;
       }
       return false;
    }

    public function deactivateCard($data)
    {
        
        $oDb = $oDb = oxDb::getDb();
        try
        {
            $oDb->startTransaction();

            $dResult = $oDb->getAll("SELECT active, useud FROM loyalcards WHERE cardid = '".$data["cardId"]."'");
            if (sizeof($dResult) == 0 ){$this->_Error = 'Card not found';return false;}

            $active = $dResult[0][0];
            $userId = $dResult[0][1];

            if($active == '0'){$this->_Error = "Card is not active";return false;}

            $sQuery = "UPDATE loyalcards SET active = 0 WHERE cardid = '".$data["cardId"]."';";
            $oDb->execute($sQuery);
            $oDb->commitTransaction();
            $dTransaction = oxNew('cardtransactions');
            $dTransaction->savePoints('', $userId, $data["cardNumber"], null, '-', null, null, 'DEACTIVATION, NOTE:'.$data["reason"], true);
            return true;
        }
        catch (ADOBD_Exception $ex)
        {
            $oDb->rollbackTransaction();
            $this->_Error = 'sql error';
            return false;
        }
    }

    public function getError()
    {
        return $this->_Error;
    }

    public function getUserId()
    {
        return $this->loyalcards__userid->value;
    }

     public function update()
     {
         
         $oDb = $oDb = oxDb::getDb();
         try
         {
            $oDb->startTransaction();
            $update = "UPDATE loyalcards set active = '".$this->loyalcards__active->value
                    ."', points = ".$this->loyalcards__points->value.",activation_date = '"
                    . $this->loyalcards__activation_date->value . "',note = '"
                    . $this->loyalcards__note."' WHERE cardid = '"
                    . $this->loyalcards__cardid."'";

            $oDb->execute($update);
            $oDb->commitTransaction();
            return true;

         } catch (Exception $ex) {
            $oDb->rollbackTransaction();
            $this->_Error = 'sql error';
            return false;
         }
         return false;
     }
     
     public function getUser()
     {
         $oUser = oxNew('oxuser');
         $oUser->load($this->getUserId());
         return $oUser;
     }

}
