<?php
    class Compunnel_Prediction_IndexController extends Mage_Core_Controller_Front_Action {

        public function indexAction() {
            $location = $this->getRequest()->getParam('location', false);

            if (!$location) {
                return null;
            }

            $layout = $this->getLayout();
            $update = $layout->getUpdate();
            $update->load('prediction_index_block_' . $location);
            $layout->generateXml();
            $layout->generateBlocks();
            $output = $layout->getOutput();
            $this->getResponse()->setBody($output);
            return $output;
        }

    }
