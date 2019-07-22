<?php
/**
* 2007-2019 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2019 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once(_PS_MODULE_DIR_.'mattributedetails/classes/AttributeDatails.php');

class Mattributedetails extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'mattributedetails';
        $this->tab = 'content_management';
        $this->version = '1.0.0';
        $this->author = 'Rafał Woźniak';
        $this->need_instance = 1;
        $this->errors = [];

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Attribute details');
        $this->description = $this->l('Display a details of attribute ');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall my module?');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('MATTRIBUTEDETAILS_LIVE_MODE', false);

        include(dirname(__FILE__).'/sql/install.php');

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayHeader');
    }

    public function uninstall()
    {
        Configuration::deleteByName('MATTRIBUTEDETAILS_LIVE_MODE');

        include(dirname(__FILE__).'/sql/uninstall.php');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        $output = '';

        if (((bool)Tools::isSubmit('submitMattributedetailsModule')) == true) {
            if($this->saveAttributeDetails()) {
                $output .= $this->displayConfirmation($this->l('New attribute details has been added successfully'));
            }   else {
                return $this->displayError($this->errors).$this->renderForm();
            }
        }
        
        if (((bool)Tools::isSubmit('editMattributedetailsModule')) == true) {
            if($this->updateAttributeDetails()) {
                $output .= $this->displayConfirmation($this->l('Attribute details has been updated successfully'));
            }   else {
                return $this->displayError($this->errors).$this->renderForm();
            }
        }

        if (((bool)Tools::isSubmit('deletemattributedetails')) == true) {
            $id_attribute_details = (int)Tools::getValue('id_mattributedetails');

            $attributeDetails = new AttributeDetails($id_attribute_details);
            if($attributeDetails->delete()) {
                $output .= $this->displayConfirmation($this->l('Attribute details has been removed successfully'));
            }
        }   

        if (((bool)Tools::isSubmit('addmattributedetails')) == true || ((bool)Tools::isSubmit('updatemattributedetails')) == true) {
            return $this->renderForm();
        }

        
        return $output.$this->getAttributeDetailsList();
    }

    public function getAttributeDetailsList()
    {

        $attributes_details = AttributeDetails::getAll();

        $fields_list = array(
            'id_mattributedetails' => array(
                'title' => $this->l('Id'),
                'width' => 'auto',
                'type' => 'text', 
            ),
            'title' => array(
                'title' => $this->l('Name'),
                'width' => 'auto',
                'type' => 'text'
            ),
            'active' => array(
                'title'      => $this->l('Status'),
                'active'     => 'status',
                'filter_key' => '!active',
                'type'       => 'bool',
                'width'      => 'auto',
                'orderby'    => false,
                'search'     => false,
            )
        );

        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->identifier = 'id_mattributedetails';
        // $helper->position_identifier = 'position';
        // $helper->orderBy = 'position';
        // $helper->orderWay = 'ASC';
        $helper->actions = array('edit', 'delete', 'add');
        $helper->show_toolbar = false;
        $helper->toolbar_btn['new'] = array(
            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&add'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Add new...')
        );
        $helper->table_id = 'module-'.$this->name;
        $helper->title = $this->displayName;
        $helper->table = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        return $helper->generateList($attributes_details, $fields_list);

    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = Tools::getIsset('id_mattributedetails') ? 'editMattributedetailsModule' : 'submitMattributedetailsModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        
        if(Tools::getIsset('id_mattributedetails')) {
            $id_attribute_details = (int)Tools::getValue('id_mattributedetails');
            $attributeDetails = new AttributeDetails($id_attribute_details);

            $image = $attributeDetails->getCoverPath();
            $image_url = ImageManager::thumbnail($image, $this->table.'_'.(int)$attributeDetails->id.'.png', 350, 'jpg', true, true);
            $image_size = file_exists($image) ? filesize($image) / 1000 : false;
        }
        
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Add new attribute details'),
                'icon' => 'icon-plus',
                ),
                'input' => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'MATTRIBUTEDETAILS_ID',
                    ),
                    array(
                        'type' => 'file',
                        'label' => $this->l('Cover Image'),
                        'name' => 'MATTRIBUTEDETAILS_COVER_IMAGE',
                        'image' => Tools::getIsset('id_mattributedetails') ? $image_url : false,
                        'size' => Tools::getIsset('id_mattributedetails') ? $image_size : false,
                        'display_image' => true,
                        'col' => 8,
                        'hint' => $this->l('Upload your cover image.')
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Active'),
                        'name' => 'MATTRIBUTEDETAILS_ACTIVE',
                        'is_bool' => true,
                        'hint' => $this->l('Use this if you want to set this content visible'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'col' => 4,
                        'type' => 'text',
                        'name' => 'MATTRIBUTEDETAILS_TITLE',
                        'required' => false,
                        'label' => $this->l('Name'),
                        'desc' => $this->l('Enter a valid attribute name'),
                    ),
                    array(
                        'col' => 7,
                        'type' => 'textarea',
                        'label' => $this->l('Content'),
                        'name' => 'MATTRIBUTEDETAILS_CONTENT',
                        'required' => false,
                        'class' => 'rte',
                        'autoload_rte' => true,
                        'desc' => $this->l('Enter a attribute datials content'),
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        
        if(Tools::getIsset('id_mattributedetails')) {
            $id_attribute_details = (int)Tools::getValue('id_mattributedetails');
            $attributeDetails = new AttributeDetails($id_attribute_details);
        }
        
        return array(
            'MATTRIBUTEDETAILS_ID' => Tools::getIsset('id_mattributedetails') ? $attributeDetails->id : false,
            'MATTRIBUTEDETAILS_COVER_IMAGE' => '',
            'MATTRIBUTEDETAILS_ACTIVE' => Tools::getIsset('id_mattributedetails') ? $attributeDetails->active : false,
            'MATTRIBUTEDETAILS_TITLE' => Tools::getIsset('id_mattributedetails') ? $attributeDetails->title : '',
            'MATTRIBUTEDETAILS_CONTENT' => Tools::getIsset('id_mattributedetails') ? $attributeDetails->content : '',
        );
    }

    protected function saveAttributeDetails() {

        $active = Tools::getValue('MATTRIBUTEDETAILS_ACTIVE');
        $title = Tools::getValue('MATTRIBUTEDETAILS_TITLE');
        $content = Tools::getValue('MATTRIBUTEDETAILS_CONTENT');

        $attributeDetails = new AttributeDetails;

        $temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
        $salt = sha1(microtime());
        if ($error = ImageManager::validateUpload($_FILES['MATTRIBUTEDETAILS_COVER_IMAGE']))
            $this->errors[] = $error;
        elseif (!$temp_name || !move_uploaded_file($_FILES['MATTRIBUTEDETAILS_COVER_IMAGE']['tmp_name'], $temp_name))
            return false;
        elseif (!ImageManager::resize($temp_name, dirname(__FILE__).'/images/'.$salt.'_'.$_FILES['MATTRIBUTEDETAILS_COVER_IMAGE']['name'], 600, 600, 'png'))
            $this->errors[] = $this->l('An error occurred during the image upload process.');
        else
            $attributeDetails->cover_image = $salt.'_'.$_FILES['MATTRIBUTEDETAILS_COVER_IMAGE']['name'];

        if (isset($temp_name))
            @unlink($temp_name);

        if(empty($title) || !Validate::isGenericName($title))
            $this->errors[] = $this->l('Title field is required.');

        if(empty($content) || !Validate::isCleanHtml($content))
            $this->errors[] = $this->l('Content field is required.');

        $attributeDetails->title = $title;
        $attributeDetails->content = $content;
        $attributeDetails->active = $active;

        
        if(empty($this->errors)) {
            return $attributeDetails->add();
        } else {
            return false;
        }
            

    }

    protected function updateAttributeDetails() {

        
        $id_attribute_details = Tools::getValue('MATTRIBUTEDETAILS_ID');
        $active = Tools::getValue('MATTRIBUTEDETAILS_ACTIVE');
        $title = Tools::getValue('MATTRIBUTEDETAILS_TITLE');
        $content = Tools::getValue('MATTRIBUTEDETAILS_CONTENT');

        $attributeDetails = new AttributeDetails($id_attribute_details);
        
        if($_FILES['MATTRIBUTEDETAILS_COVER_IMAGE']['name']) {

            $temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
            $salt = sha1(microtime());
            if ($error = ImageManager::validateUpload($_FILES['MATTRIBUTEDETAILS_COVER_IMAGE'])) {
                $this->errors[] = $error;
    
            } elseif (!$temp_name || !move_uploaded_file($_FILES['MATTRIBUTEDETAILS_COVER_IMAGE']['tmp_name'], $temp_name)) {
                return false;
            } elseif (!ImageManager::resize($temp_name, dirname(__FILE__).'/images/'.$salt.'_'.$_FILES['MATTRIBUTEDETAILS_COVER_IMAGE']['name'], 600, 600, 'png')) {
                $this->errors[] = $this->l('An error occurred during the image upload process.');
            } else {
                unlink($attributeDetails->getCoverPath());
                $attributeDetails->cover_image = $salt.'_'.$_FILES['MATTRIBUTEDETAILS_COVER_IMAGE']['name'];
            }
                
            if (isset($temp_name))
                @unlink($temp_name);
        }

        if(empty($title) || !Validate::isGenericName($title))
            $this->errors[] = $this->l('Title field is required.');

        if(empty($content) || !Validate::isCleanHtml($content))
            $this->errors[] = $this->l('Content field is required.');

        $attributeDetails->title = $title;
        $attributeDetails->content = $content;
        $attributeDetails->active = $active;
        
        if(empty($this->errors)) {
            return $attributeDetails->save();
        } else {
            return false;
        }
            

    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    public function hookDisplayHeader()
    {
        /* Place your code here. */
    }
}
