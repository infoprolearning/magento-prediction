<?php
    class Compunnel_Prediction_Block_Recommendations_Product extends Compunnel_Prediction_Block_Recommendations_Abstract {

        protected function _construct() {
            parent::_construct();
            $this->setTitle($this->__('Titles you might be interested in'));
        }

    }
