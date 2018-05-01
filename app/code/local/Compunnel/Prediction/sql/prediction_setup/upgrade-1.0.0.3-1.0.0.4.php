<?php
    $installer = $this;
    $connection = $installer->getConnection();

    $rulesTable                 = $installer->getTable('prediction/blacklist_rule');
    $storesTable                = $installer->getTable('core/store');
    $customerGroupsTable        = $installer->getTable('customer/customer_group');
    $rulesStoresTable           = $installer->getTable('prediction/blacklist_store');
    $rulesCustomerGroupsTable   = $installer->getTable('prediction/blacklist_customer_group');

    $installer->startSetup();

    if (!$connection->isTableExists($rulesStoresTable)) {
        $table = $connection->newTable($rulesStoresTable)
            ->addColumn(
                'rule_id',
                Varien_Db_Ddl_Table::TYPE_INTEGER,
                null,
                array(
                    'unsigned'  => true,
                    'nullable'  => false,
                    'primary'   => true
                    ),
                'Rule Id'
            )
            ->addColumn(
                'store_id',
                Varien_Db_Ddl_Table::TYPE_SMALLINT,
                null,
                array(
                    'unsigned'  => true,
                    'nullable'  => false,
                    'primary'   => true
                    ),
                'Store Id'
            )
            ->addIndex(
                $installer->getIdxName('prediction/blacklist_store', array('rule_id')),
                array('rule_id')
            )
            ->addIndex(
                $installer->getIdxName('prediction/blacklist_store', array('store_id')),
                array('store_id')
            )
            ->addForeignKey(
                $installer->getFkName('prediction/blacklist_store', 'rule_id', 'prediction/blacklist_rule', 'rule_id'),
                'rule_id',
                $rulesTable,
                'rule_id',
                Varien_Db_Ddl_Table::ACTION_CASCADE,
                Varien_Db_Ddl_Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName('prediction/blacklist_store', 'store_id', 'core/store', 'store_id'),
                'store_id',
                $storesTable,
                'store_id',
                Varien_Db_Ddl_Table::ACTION_CASCADE,
                Varien_Db_Ddl_Table::ACTION_CASCADE
            )
            ->setComment('Rules To Stores Relations');

        $connection->createTable($table);
    }

    if (!$connection->isTableExists($rulesCustomerGroupsTable)) {
        $table = $connection->newTable($rulesCustomerGroupsTable)
            ->addColumn(
                'rule_id',
                Varien_Db_Ddl_Table::TYPE_INTEGER,
                null,
                array(
                    'unsigned'  => true,
                    'nullable'  => false,
                    'primary'   => true
                    ),
                'Rule Id'
            )
            ->addColumn(
                'customer_group_id',
                Varien_Db_Ddl_Table::TYPE_SMALLINT,
                null,
                array(
                    'unsigned'  => true,
                    'nullable'  => false,
                    'primary'   => true
                    ),
                'Customer Group Id'
            )
            ->addIndex(
                $installer->getIdxName('prediction/blacklist_customer_group', array('rule_id')),
                array('rule_id')
            )
            ->addIndex(
                $installer->getIdxName('prediction/blacklist_customer_group', array('customer_group_id')),
                array('customer_group_id')
            )
            ->addForeignKey(
                $installer->getFkName('prediction/blacklist_customer_group', 'rule_id', 'prediction/blacklist_rule', 'rule_id'),
                'rule_id',
                $rulesTable,
                'rule_id',
                Varien_Db_Ddl_Table::ACTION_CASCADE,
                Varien_Db_Ddl_Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName('prediction/blacklist_customer_group', 'customer_group_id', 'customer/customer_group', 'customer_group_id'),
                'customer_group_id',
                $customerGroupsTable,
                'customer_group_id',
                Varien_Db_Ddl_Table::ACTION_CASCADE,
                Varien_Db_Ddl_Table::ACTION_CASCADE
            )
            ->setComment('Rules To Customer Groups Relations');

        $connection->createTable($table);
    }

    $connection->dropColumn($rulesTable, 'store_ids');
    $connection->dropColumn($rulesTable, 'customer_group_ids');

    $installer->endSetup();
