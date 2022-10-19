<?php

/**
*administration of loyal cards
 */

class loyalCards_Main extends oxAdminDetails
{

  public function render()
  {
    parent::render();
    
    $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
    
    $oCard  = oxNew( "loyalcards" );
    if ( $soxId != "-1" && isset( $soxId ) )
    {
      $oCard->load( $soxId );
      $this->_aViewData["edit"] = $oCard;
    }
     return "loyalcards_main.tpl";
    
  }

  public function save()
  {
      parent::save();
      $soxId = $this->getEditObjectId();
      $aParams = oxConfig::getParameter( "editval" );
      $oCard = oxNew( "loyalcards" );
      if ( $soxId != "-1" )
      {
        $oCard->load( $soxId );
            
            // Editing card points
            if($oCard->loyalcards__points->value != $aParams["loyalcards__points"])
            {
                $sHistory = oxNew('zzzhistory');
                $sHistory->add( 75, $soxId, oxSession::getVar( "auth" ), array( "ip" => $_SERVER["REMOTE_ADDR"], "aMessage" => "Changing the status of points: ". $oCard->loyalcards__points->value . "=>" . $aParams["loyalcards__points"]) );
                $oCard->loyalcards__points->value = $aParams["loyalcards__points"];
                $oCard->update();
            }
            
            // Changing remark (saving sms/email code when verify account
            if($oCard->loyalcards__note->value != $aParams["loyalcards__note")
            {
                $gHistory = oxNew('zzzhistory');
                $gHistory->add( 76, $soxId, oxSession::getVar( "auth" ), array( "ip" => $_SERVER["REMOTE_ADDR"], "aMessage" => "Changing remark: ". $oCard->loyalcards__note->value . "=>" . $aParams["loyalcards__note"]) );
                $oCard->loyalcards__note->value = $aParams["loyalcards__note"];
                $oCard->update();
            }
            
            // activation/deactivation
            if($oCard->loyalcards__active->value != $aParams["loyalcards__active"])
            {
                if($aParams[loyalcards__active"] == '1')
                {
                    if (!$oCard->activateCard($oCard->getId(), $oCard->loyalcards__userid->value))
                    {
                        $this->_aViewData["error"] = $oCard->getError();
                    }
                    else{$operation = 73;}
                }
                else
                {
                    if (!$oCard->deactivateCard(array("cardNumber" => $oCard->loyalcards__cardid->value, "reason" => "Deactivaton by admin")))
                    {
                         $this->_aViewData["error"] = $oCard->getError();
                    }
                    else{$operation = 74;}
                }
                $oHistory = oxNew('zzzhistory');
                $oHistory->add( $operation, $soxId, oxSession::getVar( "auth" ), array( "ip" => $_SERVER["REMOTE_ADDR"]));
            }
  }
 }
 
}
