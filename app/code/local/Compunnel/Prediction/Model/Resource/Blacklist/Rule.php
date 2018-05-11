<?php
class Compunnel_Prediction_Model_Resource_Blacklist_Rule extends Compunnel_Prediction_Model_Resource_Rule
{

    protected $_associatedEntitiesMap = array(
        'store' => array(
            'associations_table' => 'prediction/blacklist_store',
            'rule_id_field'      => 'rule_id',
            'entity_id_field'    => 'store_id'
            ),
        'customer_group' => array(
            'associations_table' => 'prediction/blacklist_customer_group',
            'rule_id_field'      => 'rule_id',
            'entity_id_field'    => 'customer_group_id'
            )
        );

    protected $_entityMap = array(
        'table_alias' => 'prediction/blacklist_rule',
        'primary_key' => 'rule_id'
        );

}
