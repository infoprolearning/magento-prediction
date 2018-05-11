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
        ->newTable($installer->getTable('prediction/visitor'))
        ->addColumn(
            'visitor_id',
            Varien_Db_Ddl_Table::TYPE_BIGINT,
            null,
            array(
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
            ),
            'Visitor ID'
        )
        ->addColumn(
            'session_id',
            Varien_Db_Ddl_Table::TYPE_TEXT,
            64,
            array(
                'nullable'  => true,
                'default'   => null,
            ),
            'Session ID'
        )
        ->addColumn(
            'first_visit_at',
            Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
            null,
            array(),
            'First Visit Time'
        )
        ->addColumn(
            'last_visit_at',
            Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
            null,
            array(
                'nullable'  => false,
            ),
            'Last Visit Time'
        )
        ->addColumn(
            'last_url_id',
            Varien_Db_Ddl_Table::TYPE_BIGINT,
            null,
            array(
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
            ),
            'Last URL ID'
        )
        ->addColumn(
            'store_id',
            Varien_Db_Ddl_Table::TYPE_SMALLINT,
            null,
            array(
                'unsigned'  => true,
                'nullable'  => false,
            ),
            'Store ID'
        )
        ->setComment('Visitors Table for Recommendation Engine');

    $installer->getConnection()->createTable($table);
