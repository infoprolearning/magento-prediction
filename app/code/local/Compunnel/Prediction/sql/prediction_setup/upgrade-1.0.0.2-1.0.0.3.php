<?php
    $installer = $this;

    $installer->startSetup();

    $table = $installer->getConnection()
        ->newTable($installer->getTable('prediction/whitelist_rule'))
        ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 11, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            ), 'Rule ID')
        ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Name of rule')
        ->addColumn('from_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        ), 'From Date')
        ->addColumn('to_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        ), 'To Date')
        ->addColumn('customer_group_ids', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Customer Group Ids')
        ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'nullable'  => false,
            'default'   => '0',
        ), 'Is Active')
        ->addColumn('store_ids', Varien_Db_Ddl_Table::TYPE_TEXT, 4000, array(
        ), 'Store Ids')
        ->addIndex(
            $installer->getIdxName('prediction/whitelist_rule', array('from_date', 'to_date', 'is_active')),
            array('from_date', 'to_date', 'is_active')
        )
        ->setComment('Whitelist rules');

    $installer->getConnection()->createTable($table);

    $table = $installer->getConnection()
        ->newTable($installer->getTable('prediction/whitelist_rule_product'))
        ->addColumn('rule_product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            ), 'Rule Product ID Identifier')
        ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
            'default' => '0'
        ), 'Rule ID')
        ->addColumn('from_time', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
            'default' => '0'
        ), 'Starting time of rule')
        ->addColumn('to_time', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => false,
            'default' => '0'
        ), 'End time of rule')
        ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => false,
            'unsigned' => true,
            'default' => '0'
        ), 'Affected Product ID')
        ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned' => true,
            'nullable' => false,
            'default' => '0'
        ), 'Store ID')
        ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'nullable' => false,
            'unsigned' => true,
            'default' => '0'
        ), 'Customer group ID')
        ->addColumn('location', Varien_Db_Ddl_Table::TYPE_TEXT, 10, array(
        ), 'Location where this rule is effective')
        ->addIndex(
            $installer->getIdxName('prediction/whitelist_rule_product', array('rule_id', 'from_time', 'to_time', 'store_id', 'customer_group_id', 'product_id')),
            array('rule_id', 'from_time', 'to_time', 'store_id', 'customer_group_id', 'product_id')
        )
        ->addForeignKey(
            $installer->getFkName('prediction/whitelist_rule_product', 'product_id', 'catalog/product', 'entity_id'),
            'product_id',
            $installer->getTable('catalog/product'),
            'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE
        )
        ->addForeignKey(
            $installer->getFkName('prediction/whitelist_rule_product', 'customer_group_id', 'customer/customer_group', 'customer_group_id'),
            'customer_group_id',
            $installer->getTable('customer/customer_group'),
            'customer_group_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE
        )
        ->addForeignKey(
            $installer->getFkName('prediction/whitelist_rule_product', 'rule_id', 'prediction/whitelist_rule', 'rule_id'),
            'rule_id',
            $installer->getTable('prediction/whitelist_rule'),
            'rule_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE
        )
        ->addForeignKey(
            $installer->getFkName('prediction/whitelist_rule_product', 'store_id', 'core/store', 'store_id'),
            'store_id',
            $installer->getTable('core/store'),
            'store_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_CASCADE
        )
        ->setComment('Products affected by whitelist rules');

    $installer->getConnection()->createTable($table);

    $installer->endSetup();
