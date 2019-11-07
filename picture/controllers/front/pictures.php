<?php
class picturepicturesModuleFrontController extends ModuleFrontController
{
    protected $templateFinder = null;
    protected $pictures = null;

  	public function initContent()
  	{
  	  	parent::initContent();
  	  	$this->pictures = new picture();
  	  	$this->context->smarty->assign($this->pictures->getWidgetVariables());
  	  	$this->setTemplate('display_pictures.tpl');
  	}

    public function getTemplateFinder()
    {
        if (!$this->templateFinder) {
            $this->context->smarty->addTemplateDir(_PS_MODULE_DIR_.'picture/views/templates/front/');
            $this->templateFinder = new TemplateFinder($this->context->smarty->getTemplateDir(), '.tpl');
        }

        return $this->templateFinder;
    }
}