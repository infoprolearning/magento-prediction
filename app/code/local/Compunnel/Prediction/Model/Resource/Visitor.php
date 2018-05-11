<?php
class Compunnel_Prediction_Model_Resource_Visitor extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct() {
        $this->_init('prediction/visitor', 'visitor_id');
    }

    protected function _prepareDataForSave(Mage_Core_Model_Abstract $visitor)
    {
        return array(
            'session_id'        => $visitor->getSessionId(),
            'first_visit_at'    => $visitor->getFirstVisitAt(),
            'last_visit_at'     => $visitor->getLastVisitAt(),
            'last_url_id'       => $visitor->getLastUrlId() ? $visitor->getLastUrlId() : 0,
            'store_id'          => Mage::app()->getStore()->getId(),
            );
    }

}
