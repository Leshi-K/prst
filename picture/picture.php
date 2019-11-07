<?php
if (!defined('_PS_VERSION_')) exit;

require_once 'classes/PictureOM.php';


class picture extends Module
{
    private $exists_hook_names;

    public function __construct()
    {
        $this->name          = 'picture';
        $this->tab           = 'seo';
        $this->tab_class     = 'Adminpicture';
        $this->version       = '1.0';
        $this->author        = 'Leshi';
        $this->need_instance = 0;
        $this->bootstrap     = true;

        if (Module::isEnabled($this->name)) {
            $this->exists_hook_names  = $this->getExistsHookNames();
        }

        parent::__construct();

        $this->displayName = $this->l('Картинки');
        $this->description = $this->l('Размести картинку там, где тебе нужно.');
    }

    public function install()
    {
        return parent::install() && $this->registerHook('displayHeader') && $this->createTables() && $this->createTab('Картинки');;
    }

    public function uninstall()
    {
        return parent::uninstall() && $this->deleteTab() && $this->deleteTables();
    }

    private function createTables()
    {
        $res = (bool)Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'picture` (
                `id_picture` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `active` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
                `position` int(10) unsigned NOT NULL DEFAULT \'0\',
                `hook` varchar(255) NOT NULL,
                `image` varchar(255) NOT NULL,
                PRIMARY KEY (`id_picture`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
        ');

        $res &= (bool)Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'picture_shop` (
                `id_picture` int(11) NOT NULL,
                `id_shop` int(11) NOT NULL,
                PRIMARY KEY (`id_picture`, `id_shop`)
            ) CHARACTER SET utf8;
        ');

        return $res;
    }

    private function deleteTables()
    {
        $res  = (bool)Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'picture`;');
        $res &= (bool)Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'picture_shop`;');
        return $res;
    }

    private function createTab($text, $id_parent = 0)
    {
        $this->deleteTab();
        $langs = Language::getLanguages();
        $tab = new Tab();
        $tab->class_name = $this->tab_class;
        $tab->module = $this->name;
        $tab->id_parent = $id_parent;

        foreach ($langs as $l) {
            $tab->name[$l['id_lang']] = $text;
        }
        return $tab->save();
    }

    private function deleteTab()
    {
        $tab_id = Tab::getIdFromClassName($this->tab_class);
        $tab = new Tab($tab_id);
        return $tab->delete();
    }

    public function __call($function_name, $arguments)
    {
        if (($hook_name = $this->getHookName($function_name)))
        {
            $this->context->smarty->assign(array(
                'html_blocks' => PictureOM::getActivePicturesByHook($hook_name),
            ));
            return $this->display(__FILE__, 'html_block.tpl');
        }
    }

    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
        $pictures = $this->getPictures(true);

        foreach ($pictures as &$pictur) {
            $picture['image'] = $this->context->link->getMediaLink(_MODULE_DIR_.'picture/images/'.$picture['image']);
        }

        return array("pictures" => $pictures);
    }

    public function getPictures($active = null) {

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'picture` '.($active ? ' WHERE `active` = 1' : ' ').' ORDER BY `position`');
    }

    private function getHookName($function_name)
    {
        $function_name = strtolower($function_name);

        preg_match('/^hook(.+)/', $function_name, $matches);
        $hook_name = $matches[1];
        if ( in_array($hook_name, $this->exists_hook_names) )
            return $hook_name;

        elseif ( in_array($function_name, $this->exists_hook_names) )
            return $function_name;

        return false;
    }

    private function getExistsHookNames()
    {
        $hook_names = array();

        $hooks   = Hook::getHooks();
        $aliases = Hook::getHookAliasList();

        foreach ($hooks as $hook)
            $hook_names[] = strtolower($hook['name']);

        foreach ($aliases as $alias => $hook_name)
            $hook_names[] = strtolower($alias);

        return $hook_names;
    }

    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink($this->tab_class));
    }
}
