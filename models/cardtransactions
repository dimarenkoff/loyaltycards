<?php

    class cardTransactions extends zzzBase
    {
      public function __construct()
      {
        parent::__construct();
        $this->init( 'cardtransactions' );
      }

       /**
       * $orderid(string),
       * $useid(string),
       * cardid(string),
       * $points(int),
       * $transactionType(string) / +/-,
       * $active(bool),
       * $extid(int) / extrernal id from cash register,
       * $note(string) / any remark,
       * $deactivation(bool),
       * $totalValue(double) / total value order
       * returns bool
       */
      public function savePoints($orderid, $userid, $cardid, $points, $transactionType, $active=null, $extid=null, $note = null, $deactivation = null, $totalValue=null)
      {
        if($transactionType == null && $points != 0){if ($points > 0){$transactionType = '+';}else{$transactionType = '-';} }
        
        $this->cardtransactions__orderid =  new oxField($orderid);
        $this->cardtransactions__userid =  new oxField($userid);
        $this->cardtransactions__cardid =  new oxField($cardid);
        $this->cardtransactions__points =  new oxField($points);
        $this->cardtransactions__transaction_type =  new oxField($transactionType);
        $this->cardtransactions__active =  new oxField($active);
        $this->cardtransactions__note =  new oxField($note);
        $sDate = date( 'Y-m-d H:i:s', oxRegistry::get("oxUtilsDate")->getTime() );
        $this->cardtransactions__insert_date =  new oxField( $sDate, oxField::T_RAW );
        if($extid){$this->cardtransactions__oxextid = new oxField( $extid );}
        if($active){$this->cardtransactions__activation_date = new oxField( $sDate, oxField::T_RAW );}
        if($deactivation){$this->cardtransactions__deactivation_date = new oxField( $sDate, oxField::T_RAW );}
        if($totalValue){$this->cardtransactions__total_value = new oxField($totalValue);}
        if($this->save()){return true;}
        else{return false;}
      }

      public function getUser()
      {
          $oUser = oxNew('oxuser');
          $oUser->load($this->cardtransactions__userid->value);
          return $oUser;
      }
    }
