<?php
    class Compunnel_Prediction_Block_Adminhtml_Blacklist_Homepage_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
    {

        public function canShowTab()
        {
            return true;
        }

        public function isHidden()
        {
            return false;
        }

        public function getTabLabel()
        {
            return Mage::helper('prediction')->__('Rule Information');
        }

        public function getTabTitle()
        {
            return Mage::helper('prediction')->__('Rule Information');
        }

        protected function _prepareForm()
        {
            $model = Mage::registry('current_prediction_blacklist_homepage_rule');
            if ($this->_isAllowedAction('save')) {
                $isElementDisabled = false;
            }
            else {
                $isElementDisabled = true;
            }
            $form = new Varien_Data_Form();
            $form->setHtmlIdPrefix('rule_');

            $fieldset = $form->addFieldset(
                'base_fieldset',
                array(
                    'legend'    => Mage::helper('prediction')->__('General Information'),
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
                    'name'      => 'name',
                    'label'     => Mage::helper('prediction')->__('Rule Name'),
                    'title'     => Mage::helper('prediction')->__('Rule Name'),
                    'required'  => true,
                    'disabled'  => $isElementDisabled
                    )
            );

            $fieldset->addField(
                'is_active',
                'select',
                array(
                    'label'     => Mage::helper('prediction')->__('Status'),
                    'title'     => Mage::helper('prediction')->__('Status'),
                    'name'      => 'is_active',
                    'required'  => true,
                    'options'   => array(
                        '1'     => Mage::helper('prediction')->__('Active'),
                        '0'     => Mage::helper('prediction')->__('Inactive'),
                        ),
                    'disabled'  => $isElementDisabled
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
                    'values'    => Mage::getResourceModel('customer/group_collection')->toOptionArray(),
                    'disabled'  => $isElementDisabled
                    )
            );

            $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
            $fieldset->addField(
                'from_date',
                'date',
                array(
                    'name'          => 'from_date',
                    'label'         => Mage::helper('prediction')->__('From Date'),
                    'title'         => Mage::helper('prediction')->__('From Date'),
                    'image'         => $this->getSkinUrl('images/grid-cal.gif'),
                    'input_format'  => Varien_Date::DATE_INTERNAL_FORMAT,
                    'format'        => $dateFormatIso,
                    'disabled'      => $isElementDisabled
                    )
            );

            $fieldset->addField(
                'to_date',
                'date',
                array(
                    'name'          => 'to_date',
                    'label'         => Mage::helper('prediction')->__('To Date'),
                    'title'         => Mage::helper('prediction')->__('To Date'),
                    'image'         => $this->getSkinUrl('images/grid-cal.gif'),
                    'input_format'  => Varien_Date::DATE_INTERNAL_FORMAT,
                    'format'        => $dateFormatIso,
                    'disabled'      => $isElementDisabled
                    )
            );

            if (!Mage::app()->isSingleStoreMode()) {
                $field = $fieldset->addField('store_ids', 'multiselect', array(
                    'name'      => 'store_ids[]',
                    'label'     => Mage::helper('prediction')->__('Store View'),
                    'title'     => Mage::helper('prediction')->__('Store View'),
                    'required'  => true,
                    'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, false),
                    'disabled'  => $isElementDisabled,
                    ));
                $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
                $field->setRenderer($renderer);
            }
            else {
                $fieldset->addField('store_ids', 'hidden', array(
                    'name'      => 'store_ids[]',
                    'value'     => Mage::app()->getStore(true)->getId()
                    ));
                $model->setStoreId(Mage::app()->getStore(true)->getId());
            }

            $form->setValues($model->getData());
            $form->setFieldNameSuffix('rule');
            $this->setForm($form);
            Mage::dispatchEvent('adminhtml_prediction_blacklist_homepage_edit_main_prepare_form', array('form' => $form));
            return parent::_prepareForm();
        }

        protected function _isAllowedAction($action)
        {
            return Mage::getSingleton('admin/session')->isAllowed('prediction/blacklist/blacklist_homepage/' . $action);
        }

    }
