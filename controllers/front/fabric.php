<?php


class MattributedetailsFabricModuleFrontController extends ModuleFrontController
{

    /**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
        
        $this->display_column_left = false;

        $this->context = Context::getContext();

        parent::initContent();

        // $this->context->smarty->assign('wishlist', $wishlist);
		// $this->setTemplate('fabric.tpl');
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

    public function ajaxProcessGetFabric()
    {

        $result = [
            'hasError' => true,
            'error' => $this->module->l('You do not have a wish list')
        ];   
        
        return die(Tools::jsonEncode($result));
	}


}
