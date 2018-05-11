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
class Compunnel_Prediction_Model_Visitor extends Mage_Core_Model_Abstract
{
    protected $_skipRequestLogging = false;
    protected $_httpHelper;
    protected $_session;

    protected function _construct()
    {
        $this->_init('prediction/visitor');
        $this->_httpHelper = Mage::helper('core/http');
        $this->_session = Mage::getSingleton('core/session', array('name' => 'prediction'))->start();
    }

    protected function _getSession()
    {
        return $this->_session;
    }

    public function initServerData()
    {
        $this->addData(array(
            'server_addr'           => $this->_httpHelper->getServerAddr(true),
            'remote_addr'           => $this->_httpHelper->getRemoteAddr(true),
            'http_secure'           => Mage::app()->getStore()->isCurrentlySecure(),
            'http_host'             => $this->_httpHelper->getHttpHost(true),
            'http_user_agent'       => $this->_httpHelper->getHttpUserAgent(true),
            'http_accept_language'  => $this->_httpHelper->getHttpAcceptLanguage(true),
            'http_accept_charset'   => $this->_httpHelper->getHttpAcceptCharset(true),
            'request_uri'           => $this->_httpHelper->getRequestUri(true),
            'session_id'            => $this->_session->getSessionId(),
            'http_referer'          => $this->_httpHelper->getHttpReferer(true),
            ));
        return $this;
    }

    public function initByRequest($observer)
    {
        $this->setData($this->_session->getVisitorData());

        $visitorId = $this->getId();
        if (!$visitorId) {
            $this->initServerData();
            $this->setIsNewVisitor(true);
            $this->setFirstVisitAt(now());
            $this->save();
        }
        return $this;
    }

    public function saveByRequest($observer)
    {
        try {
            $this->setLastVisitAt(now());
            $this->save();
            $this->_session->setVisitorData($this->getData());
        }
        catch (Exception $e) {
            Mage::logException($e);
        }
        return $this;
    }

    public function isVisitorSessionNew()
    {
        $visitorData = $this->_session->getVisitorData();
        $visitorSessionId = null;
        if (is_array($visitorData) && isset($visitorData['session_id'])) {
            $visitorSessionId = $visitorData['session_id'];
        }

        if ($this->_session->getSessionId() == $visitorSessionId) {
            $firstVisitTime = $visitorData['first_visit_at'];
            $visitorLifeTime = abs(strtotime(now()) - strtotime($firstVisitTime));

            return $visitorLifeTime <= Mage::getStoreConfig('prediction/general/new_user_lifetime');
        }
        return true;
    }
}
