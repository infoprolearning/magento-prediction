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

    $installer = $this;

    $installer->startSetup();

    $installer->getConnection()
        ->addIndex(
            $installer->getTable('prediction/blacklist_rule'),
            $installer->getIdxName(
                'prediction/blacklist_rule',
                array(
                    'from_date',
                    'to_date',
                    'is_active'
                )
            ),
            array(
                'from_date',
                'to_date',
                'is_active'
            )
        );

    $installer->getConnection()
        ->addIndex(
            $installer->getTable('prediction/blacklist_rule_product'),
            $installer->getIdxName(
                'prediction/blacklist_rule_product',
                array(
                    'rule_id',
                    'from_time',
                    'to_time',
                    'store_id',
                    'customer_group_id',
                    'product_id'
                )
            ),
            array(
                'rule_id',
                'from_time',
                'to_time',
                'store_id',
                'customer_group_id',
                'product_id'
            )
        );

    $installer->getConnection()
        ->addForeignKey(
            $installer->getFkName(
                'prediction/blacklist_rule_product',
                'product_id',
                'catalog/product',
                'entity_id'
            ),
            $installer->getTable('prediction/blacklist_rule_product'),
            'product_id',
            $installer->getTable('catalog/product'),
            'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE
        );

    $installer->getConnection()
        ->addForeignKey(
            $installer->getFkName(
                'prediction/blacklist_rule_product',
                'customer_group_id',
                'customer/customer_group',
                'customer_group_id'
            ),
            $installer->getTable('prediction/blacklist_rule_product'),
            'customer_group_id',
            $installer->getTable('customer/customer_group'),
            'customer_group_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE
        );

    $installer->getConnection()
        ->addForeignKey(
            $installer->getFkName(
                'prediction/blacklist_rule_product',
                'rule_id',
                'prediction/blacklist_rule',
                'rule_id'
            ),
            $installer->getTable('prediction/blacklist_rule_product'),
            'rule_id',
            $installer->getTable('prediction/blacklist_rule'),
            'rule_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE
        );

    $installer->getConnection()
        ->addForeignKey(
            $installer->getFkName(
                'prediction/blacklist_rule_product',
                'store_id',
                'core/store',
                'store_id'
            ),
            $installer->getTable('prediction/blacklist_rule_product'),
            'store_id',
            $installer->getTable('core/store'),
            'store_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE
        );

    $installer->endSetup();
