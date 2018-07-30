<?php
/**
 * PHP version 5
 *
 * @category  Compunnel
 * @package   Compunnel_Prediction
 * @author    Prateek Agrawal <prateek.agarwal@compunnel.com>
 * @copyright 2018 Compunnel (https://www.compunnel.com)
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License
 * @link      https://bitbucket.org/prateekatcompunnel/apac-prediction
*/

/**
 * Prediction data helper
 *
 * @category Compunnel
 * @package  Compunnel_Prediction
 * @author   Prateek Agrawal <prateek.agarwal@compunnel.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License
 * @link     https://bitbucket.org/prateekatcompunnel/apac-prediction
 */

class Compunnel_Prediction_Helper_Data extends Compunnel_Prediction_Helper_Abstract
{

    public function makeRecommendationCall($data, $storeId = '')
    {
        $recommendatationCallResponse = '';
        if (!$this->isEngineEnabled($storeId)) {
            return $recommendatationCallResponses;
        }
        try {
            $additionalData = $this->getAdditionalData();

            $requestPacket = array();
            $requestPacket['requestdata'] = $data;
            $requestPacket['additional'] = $additionalData;

            $encRequestData = Mage::helper('prediction/crypto')->encrypt(json_encode($requestPacket, JSON_UNESCAPED_SLASHES));
            $encRequestPacket = array(
                'data' => $encRequestData
            );

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
                $this->getApiUrl($storeId),
                '1.1',
                $headers,
                json_encode($encRequestPacket, JSON_UNESCAPED_SLASHES)
            );
            $result = $curlObject->read();
            $curlObject->close();
            if($this->isJson($result)) {
                $responseDataDecoded = json_decode($result, true);
                if (isset($responseDataDecoded['body'])) {
                    $decResponseData = Mage::helper('prediction/crypto')->decrypt($responseDataDecoded['body']);
                    return $decResponseData;
                }
            }
        }
        catch(Exception $e) {
            Mage::logException($e);
        }
        return $recommendatationCallResponses;
    }

    public function isVisitorNew()
    {
        return Mage::getSingleton('prediction/visitor')->isVisitorSessionNew();
    }

    public function getNoOfRecommendations($location, $storeId = '')
    {
        return $this->getRecommendationQty($location, $storeId);
    }
}
