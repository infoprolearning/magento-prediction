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

class Compunnel_Prediction_Model_Observer
{

    public function recordAddToCart($observer)
    {
        $product  = $observer->getEvent()->getProduct();
        $request  = $observer->getEvent()->getRequest();
        $response = $observer->getEvent()->getResponse();

        $params = $request->getParams();
        if (isset($params['source']) && $params['source'] == 'recommendation')
        {
            $eventData = array();
            $eventData['event']    = 'addtocart';
            $eventData['source']   = $params['source'];
            $eventData['location'] = $params['location'];
            $eventData['product'] = $product->getId();
            Mage::helper('prediction/event')->processNewEvent($eventData);
        }
    }

}
