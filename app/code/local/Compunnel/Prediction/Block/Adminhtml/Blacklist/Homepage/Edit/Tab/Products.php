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
class Compunnel_Prediction_Block_Adminhtml_Blacklist_Homepage_Edit_Tab_Products extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('related_product_grid');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        if ($this->_getRule()->getId()) {
            $this->setDefaultFilter(array('in_products' => 1));
        }
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalog/product_link')->useRelatedLinks()
            ->getProductCollection()
            ->setProduct($this->_getRule())
            ->addAttributeToSelect('*');

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _getRule()
    {
        return Mage::registry('current_prediction_blacklist_homepage_rule');
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
            }
            else {
                if ($ruleIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $productIds));
                }
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('in_products', array(
            'header_css_class'  => 'a-center',
            'type'              => 'checkbox',
            'name'              => 'in_products',
            'values'            => $this->_getSelectedProducts(),
            'align'             => 'center',
            'index'             => 'entity_id'
        ));

        $this->addColumn('entity_id', array(
            'header' => Mage::helper('prediction')->__('ID'),
            'sortable' => true,
            'width' => 60,
            'index' => 'entity_id'
            ));

        $this->addColumn('name', array(
            'header' => Mage::helper('prediction')->__('Name'),
            'index' => 'name'
            ));

        $this->addColumn('status', array(
            'header' => Mage::helper('prediction')->__('Status'),
            'width' => 90,
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
            ));

        $this->addColumn('visibility', array(
            'header' => Mage::helper('prediction')->__('Visibility'),
            'width' => 90,
            'index' => 'visibility',
            'type' => 'options',
            'options' => Mage::getSingleton('catalog/product_visibility')->getOptionArray(),
            ));

        $this->addColumn('sku', array(
            'header' => Mage::helper('prediction')->__('SKU'),
            'width' => 80,
            'index' => 'sku'
            ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getData('grid_url') ? $this->getData('grid_url') : $this->getUrl('*/*/relatedGrid', array('_current' => true));
    }

    protected function _getSelectedProducts()
    {
        $products = $this->getSelectedProducts();
        return $products;
    }

    public function getSelectedProducts()
    {
        $ruleId = $this->_getRule()->getRuleId();
        return Mage::getModel('prediction/blacklist_rule_product')->getProductsByRuleId($ruleId);
    }

}
