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

    $table = $installer->getConnection()
        ->newTable($installer->getTable('prediction/blacklist_rule'))
        ->addColumn(
            'rule_id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            11,
            array(
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
            ),
            'Rule ID'
        )
        ->addColumn(
            'name',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            255,
            array(),
            'Name of rule'
        )
        ->addColumn(
            'from_date',
            Varien_Db_Ddl_Table::TYPE_DATE,
            null,
            array(),
            'From Date'
        )
        ->addColumn(
            'to_date',
            Varien_Db_Ddl_Table::TYPE_DATE,
            null,
            array(),
            'To Date'
        )
        ->addColumn(
            'customer_group_ids',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            '64k',
            array(),
            'Customer Group Ids'
        )
        ->addColumn(
            'is_active',
            Varien_Db_Ddl_Table::TYPE_SMALLINT,
            null,
            array(
                'nullable'  => false,
                'default'   => '0',
            ),
            'Is Active'
        )
        ->addColumn(
            'store_ids',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            4000,
            array(),
            'Store Ids'
        )
        ->setComment('Blacklist rules');

    $installer->getConnection()->createTable($table);

    $table = $installer->getConnection()
        ->newTable($installer->getTable('prediction/blacklist_rule_product'))
        ->addColumn(
            'rule_product_id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
            ),
            'Rule Product ID Identifier'
        )
        ->addColumn(
            'rule_id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'unsigned' => true,
                'nullable' => false,
                'default' => '0'
            ),
            'Rule ID'
        )
        ->addColumn(
            'from_time',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'unsigned' => true,
                'nullable' => false,
                'default' => '0'
            ),
            'Starting time of rule'
        )
        ->addColumn(
            'to_time',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'unsigned' => true,
                'nullable' => false,
                'default' => '0'
            ),
            'End time of rule'
        )
        ->addColumn(
            'product_id',
            Varien_Db_Ddl_Table::TYPE_INTEGER,
            null,
            array(
                'nullable' => false,
                'unsigned' => true,
                'default' => '0'
            ),
            'Affected Product ID'
        )
        ->addColumn(
            'store_id',
            Varien_Db_Ddl_Table::TYPE_SMALLINT,
            null,
            array(
                'unsigned' => true,
                'nullable' => false,
                'default' => '0'
            ),
            'Store ID'
        )
        ->addColumn(
            'customer_group_id',
            Varien_Db_Ddl_Table::TYPE_SMALLINT,
            null,
            array(
                'nullable' => false,
                'unsigned' => true,
                'default' => '0'
            ),
            'Customer group ID'
        )
        ->addColumn(
            'location',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            10,
            array(),
            'Location where this rule is effective'
        )
        ->setComment('Products affected by blacklist rules');

    $installer->getConnection()->createTable($table);

    $installer->endSetup();
