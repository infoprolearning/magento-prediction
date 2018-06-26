<?php
class Compunnel_Prediction_Helper_Event extends Compunnel_Prediction_Helper_Abstract
{

    public function processNewEvent($params)
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $data = array();

            $data['entityId'] = $customer->getId();
            $data['targetEntityId'] = $params['product'];
            $data['entityType'] = "user";
            $data['targetEntityType'] = "item";

            $data['event'] = $params['event'];
            $data['source'] = $params['source'];
            $data['location'] = $params['location'];

            $event = $params['event'];
            switch ($event) {
                case 'view':
                    $data['event'] = "view";
                    break;

                default:
                    break;
            }

            $this->postEventData($data);
        }
    }

    public function postEventData($data, $storeId)
    {
        if (!$this->isEngineEnabled($storeId)) {
            return;
        }
        try {
            $additionalData = $this->getAdditionalData();

            $requestPacket = array();

            $requestPacket['event_type'] = $data['event'];

            $requestPacket['source'] = $data['source'];
            if ($requestPacket['source'] == '') {
                $requestPacket['source'] = 'normal';
            }
            unset($data['source']);

            $requestPacket['location'] = $data['location'];
            if ($requestPacket['location'] == '') {
                $requestPacket['location'] = 'unknown';
            }
            unset($data['location']);

            $requestPacket['requestdata'] = $data;
            $requestPacket['additional'] = $additionalData;

            $curlObject = new Varien_Http_Adapter_Curl();
            $config = array(
                'timeout' => 10,
                'header' => false
                );
            $curlObject->setConfig($config);
            $headers = array();
            $headers[] = "Content-Type: application/json";

            $curlObject->write(
                Zend_Http_Client::POST,
                $this->getEventUrl($storeId),
                '1.1',
                $headers,
                json_encode($requestPacket, JSON_UNESCAPED_SLASHES)
            );
            Mage::log(json_encode($requestPacket, JSON_UNESCAPED_SLASHES), null, 'prediction.log');
            Mage::log($this->getEventUrl($storeId), null, 'prediction.log');
            $result = $curlObject->read();
            $curlObject->close();
            Mage::log($result, null, 'prediction.log');
            return $result;
        }
        catch(Exception $e) {
            Mage::logException($e);
        }
    }

}
