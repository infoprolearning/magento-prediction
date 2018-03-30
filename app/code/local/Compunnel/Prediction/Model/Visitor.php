<?php
    class Compunnel_Prediction_Model_Visitor extends Mage_Core_Model_Abstract
    {
        protected $_skipRequestLogging = false;
        protected $_logCondition;
        protected $_httpHelper;
        protected $_config;
        protected $_session;

        public function __construct(array $data = array())
        {
            $this->_httpHelper = !empty($data['http_helper']) ? $data['http_helper'] : Mage::helper('core/http');
            $this->_config = !empty($data['config']) ? $data['config'] : Mage::getConfig();
            $this->_logCondition = !empty($data['log_condition']) ? $data['log_condition'] : Mage::helper('log');
            $this->_session = !empty($data['session']) ? $data['session'] : Mage::getSingleton('core/session');
            parent::__construct($data);
        }

        protected function _construct()
        {
            $this->_init('log/visitor');
            $userAgent = $this->_httpHelper->getHttpUserAgent();
            $ignoreAgents = $this->_config->getNode('global/ignore_user_agents');
            if ($ignoreAgents) {
                $ignoreAgents = $ignoreAgents->asArray();
                if (in_array($userAgent, $ignoreAgents)) {
                    $this->_skipRequestLogging = true;
                }
            }
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
            }
            if (!$visitorId || $this->isVisitorSessionNew()) {
                Mage::dispatchEvent('visitor_init', array('visitor' => $this));
            }
            return $this;
        }

        public function saveByRequest($observer)
        {
            try {
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
            if (is_array($visitorData) && isset($visitorData['is_new_visitor']) && $visitorData['is_new_visitor'] == 1) {
                return true;
            }
            return false;
        }
    }
