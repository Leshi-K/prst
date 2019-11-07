<?php
class AdminPictureController extends ModuleAdminController
{
    public $module;
	public $PictureOM;

    public function __construct()
    {
        $this->table         = 'picture';
        $this->className     = 'PictureOM';
        $this->module        = 'picture';
        $this->lang          = false;
        $this->bootstrap     = true;
        $this->need_instance = 0;

        $this->context       = Context::getContext();
        $this->errors        = array();
        $this->success       = 0;

        Shop::addTableAssociation($this->table, array('type' => 'shop'));
        parent::__construct();

        $this->token = Tools::getAdminTokenLite('Adminpicture');

        $this->fields_list = array(
            'id_picture' => array(
                'title'    => $this->l('ID'),
                'type'     => 'text',
                'orderby'  => true
            ),
            'position' => array(
                'title'    => $this->l('Порядок'),
                'type'     => 'text',
            ),
            'image' => array(
                'title'    => $this->l('Картинка'),
                'type'     => 'text',
            ),
            'hook' => array(
                'title'    => $this->l('Хук'),
                'type'     => 'text',
                'callback' => 'getHookNames',
            ),
            'active' => array(
                'title'    => $this->l('Статус'),
                'type'     => 'bool',
                'align'    => 'center',
                'active'   => 'status',

            ),
        );

        $this->addRowAction('delete');
    }

    public static function getHookNames($hooks)
    {
        return $hooks;
    }

    public function renderForm()
    {
		$this->loadPictureOM();

        $hooks = array();
        foreach (Hook::getHooks() as $h) {
            $hooks[] = $h['name'];
        }

        $block_html = $this->PictureOM;
        $block_html->image = $this->context->link->getMediaLink(_MODULE_DIR_.'picture/images/'.$block_html->image);

        $this->context->smarty->assign(array(
            'block_html'    => $block_html,
            'hooks'         => $hooks,
            'current_hook' => $this->PictureOM->hook_cur,
        ));

        return $this->context->smarty->fetch(_PS_MODULE_DIR_.'picture/views/templates/admin/add_form.tpl');
    }

    public function postProcess()
    {
        if (Tools::isSubmit('statuspicture')) {
            $this->loadPictureOM();
            $this->PictureOM->active = !(bool)$this->PictureOM->active;
            $this->PictureOM->update();
        }

        if (Tools::isSubmit('deletepicture')) {
            if (Tools::getValue('id_picture') && $block = $this->loadObject()) $block->delete();
        }

        if (Tools::getValue('savePicture')) {
            $this->loadPictureOM();

            if (!empty($_FILES['image']['name'])) {
                $type = Tools::strtolower(Tools::substr(strrchr($_FILES['image']['name'], '.'), 1));
                $imagesize = @getimagesize($_FILES['image']['tmp_name']);
                if (isset($_FILES['image']) &&
                    isset($_FILES['image']['tmp_name']) &&
                    !empty($_FILES['image']['tmp_name']) &&
                    !empty($imagesize) &&
                    in_array(Tools::strtolower(Tools::substr(strrchr($imagesize['mime'], '/'), 1)), array( 'jpg', 'gif', 'jpeg', 'png')) &&
                    in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
                ) {
                    $temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                    $salt = sha1(microtime());
                    if ($error = ImageManager::validateUpload($_FILES['image'])) {
                        $errors[] = $error;
                    } elseif (!$temp_name || !move_uploaded_file($_FILES['image']['tmp_name'], $temp_name)) {
                        return false;
                    } elseif (!ImageManager::resize($temp_name, dirname(__FILE__).'/../../images/'.$salt.'_'.$_FILES['image']['name'], null, null, $type)) {
                        $errors[] = $this->displayError($this->l('An error occurred during the image upload process.'));
                    }
                    if (isset($temp_name)) {
                        @unlink($temp_name);
                    }
                    $this->PictureOM->image = $salt.'_'.$_FILES['image']['name'];
                }
            }

            $this->PictureOM->hook = Tools::getValue('hook');
            $this->errors = $this->PictureOM->validateController();
            if (!count($this->errors)) {
                if($this->PictureOM->save()) {
                    $this->updateAssoShop($this->PictureOM->id);
                }
            }
        }
    }

	public function loadPictureOM()
	{
		if(!$this->PictureOM) {
			$idPicture = (int)Tools::getValue($this->identifier);
			if (!$idPicture||!$this->PictureOM = $this->loadObject()) $this->PictureOM = new PictureOM();
		}
	}
}
