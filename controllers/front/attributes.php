<?php

// include_once(_PS_MODULE_DIR_.'mattributedetails/classes/AttributeDatails.php');

class MattributedetailsAttributesModuleFrontController extends ModuleFrontController
{

    public $context;

    /**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
        
        $this->display_column_left = false;
        $this->title = $this->module->l('Attributes details');
        $this->context = Context::getContext();

        parent::initContent();
        $this->context->controller->addCSS(_PS_MODULE_DIR_.$this->module->name.'/views/css/front/mattributedetails.css');
        $this->context->controller->addJS(_PS_MODULE_DIR_.$this->module->name.'/views/js/front/mattributedetails.js');

        $attributes_details = AttributeDetails::getAll();

        $this->context->smarty->assign('attributes_details', $attributes_details);
		$this->setTemplate('attributes_details.tpl');
	}

    /**
     * @TODO uses redirectAdmin only if !$this->ajax
     * @return bool
     */
    public function postProcess()
    {
        try {
            if ($this->ajax) {
                // from ajax-tab.php
                $action = Tools::getValue('action');
                // no need to use displayConf() here
                if (!empty($action) && method_exists($this, 'ajaxProcess'.Tools::toCamelCase($action))) {

                    $return = $this->{'ajaxProcess'.Tools::toCamelCase($action)}();

                    return $return;
                } 
            } 
        } catch (PrestaShopException $e) {
            $this->errors[] = $e->getMessage();
		}
		
        return false;
    }

    public function ajaxProcessGetAttributes()
    {
        $attributes_details = AttributeDetails::getAll(true);
        
        return die(Tools::jsonEncode($attributes_details));
    }
    
    public function ajaxProcessSearch()
    {
        $query = Tools::getValue('q');
        $results = AttributeDetails::search($query);
        
        return die(Tools::jsonEncode($results));
    }


}
