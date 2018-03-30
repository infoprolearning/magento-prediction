<?php
    class Compunnel_Prediction_Helper_Data extends Compunnel_Prediction_Helper_Abstract
    {
        const RECOMMENDATION_PORT = '8000';

        public function makeRecommendationCall($data, $storeId = '')
        {
            if (!$this->isModuleEnabled($storeId)) {
                return;
            }
            try {
                $curlObject = new Varien_Http_Adapter_Curl();
                $config = array(
                    'timeout' => 10,
                    'header' => false
                    );
                $curlObject->setConfig($config);
                $headers = array();
                $headers[] = "Content-Type: application/json";

                $curlObject->write(Zend_Http_Client::POST, $this->getRecommendationUrl($storeId), '1.1', $headers, json_encode($data));
                $result = $curlObject->read();
                $curlObject->close();
                return $result;
            }
            catch(Exception $e) {
                Mage::logException($e);
            }
        }

        public function isVisitorNew()
        {
            return Mage::getSingleton('prediction/visitor')->isVisitorSessionNew();
        }

        protected function getRecommendationUrl($storeId = '')
        {
            return $this->getEngineUrl($storeId) . ':' . self::RECOMMENDATION_PORT . '/queries.json';
        }

        public function getNoOfRecommendations($location, $storeId = '') {
            return $this->getRecommendationQty($location, $storeId);
        }
    }
