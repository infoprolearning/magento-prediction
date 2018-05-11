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
 * Prediction index controller
 *
 * @category Compunnel
 * @package  Compunnel_Prediction
 * @author   Prateek Agrawal <prateek.agarwal@compunnel.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License
 * @link     https://bitbucket.org/prateekatcompunnel/apac-prediction
 */

class Compunnel_Prediction_IndexController extends Mage_Core_Controller_Front_Action
{

    /**
     * Responds to ajax calls from store front-end
     *
     * @return string
     */
    public function indexAction()
    {
        $location = $this->getRequest()->getParam('location', false);

        if (!$location) {
            return null;
        }

        if ($this->getRequest()->getParam('product', false)) {
            $this->initCurrentProduct();
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

    /**
     * Initialize current product
     *
     * @return object
     */
    protected function initCurrentProduct()
    {
        $categoryId = (int) $this->getRequest()->getParam('category', false);
        $productId  = (int) $this->getRequest()->getParam('product');

        $params = new Varien_Object();
        $params->setCategoryId($categoryId);

        return Mage::helper('catalog/product')->initProduct(
            $productId,
            $this,
            $params
        );
    }
}
