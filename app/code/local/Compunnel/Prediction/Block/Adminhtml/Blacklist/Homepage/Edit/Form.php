<?php
    class Compunnel_Prediction_Block_Adminhtml_Blacklist_Homepage_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
    {

        public function __construct()
        {
            parent::__construct();
            $this->setId('prediction_blacklist_homepage_form');
            $this->setTitle(Mage::helper('prediction')->__('Rule Information'));
        }

        protected function _prepareForm()
        {
            $model = Mage::registry('current_prediction_blacklist_homepage_rule');
            $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post'));

            $form->setHtmlIdPrefix('rule_');

            $fieldset = $form->addFieldset(
                'base_fieldset',
                array(
                    'legend '=> Mage::helper('prediction')->__('General Information'),
                    'class'     => 'fieldset-wide'
                )
            );

            if ($model->getId()) {
                $fieldset->addField(
                    'rule_id',
                    'hidden',
                    array(
                        'name' => 'rule_id',
                        )
                );
            }

            $fieldset->addField(
                'name',
                'text',
                array(
                    'name' => 'name',
                    'label' => Mage::helper('prediction')->__('Rule Name'),
                    'title' => Mage::helper('prediction')->__('Rule Name'),
                    'required' => true,
                    )
            );

            $fieldset->addField(
                'is_active',
                'select',
                array(
                    'label'     => Mage::helper('prediction')->__('Status'),
                    'title'     => Mage::helper('prediction')->__('Status'),
                    'name'      => 'is_active',
                    'required' => true,
                    'options'    => array(
                        '1' => Mage::helper('prediction')->__('Active'),
                        '0' => Mage::helper('prediction')->__('Inactive'),
                        ),
                    )
            );

            $fieldset->addField(
                'customer_group_ids',
                'multiselect',
                array(
                    'name'      => 'customer_group_ids[]',
                    'label'     => Mage::helper('prediction')->__('Customer Groups'),
                    'title'     => Mage::helper('prediction')->__('Customer Groups'),
                    'required'  => true,
                    'values'    => Mage::getResourceModel('customer/group_collection')->toOptionArray()
                    )
            );

            $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
            $fieldset->addField(
                'from_date',
                'date',
                array(
                    'name'   => 'from_date',
                    'label'  => Mage::helper('prediction')->__('From Date'),
                    'title'  => Mage::helper('prediction')->__('From Date'),
                    'image'  => $this->getSkinUrl('images/grid-cal.gif'),
                    'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
                    'format'       => $dateFormatIso
                    )
            );

            $fieldset->addField(
                'to_date',
                'date',
                array(
                    'name'   => 'to_date',
                    'label'  => Mage::helper('prediction')->__('To Date'),
                    'title'  => Mage::helper('prediction')->__('To Date'),
                    'image'  => $this->getSkinUrl('images/grid-cal.gif'),
                    'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
                    'format'       => $dateFormatIso
                    )
            );

            $fieldset->addField(
                'sort_order',
                'text',
                array(
                    'name' => 'sort_order',
                    'label' => Mage::helper('prediction')->__('Priority'),
                    )
            );

            $form->setValues($model->getData());
            $form->setUseContainer(true);
            $this->setForm($form);
            Mage::dispatchEvent('adminhtml_prediction_blacklist_homepage_edit_main_prepare_form', array('form' => $form));
            return parent::_prepareForm();
        }

    }
