<?php
class PictureOM extends ObjectModel
{
    public $id_picture;
    public $hook;
    public $position;
    public $image;
    public $active;

    public $hook_cur;

    public static $definition = array(
        'table'     => 'picture',
        'primary'   => 'id_picture',
        'multilang' => false,
        'multishop' => true,
        'fields'    => array
        (
            'hook'     => array('type' => self::TYPE_STRING,  'validate' => 'isString', 'required' => true, 'size' => 255),
            'image'    => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 255),
            'position' => array('type' => self::TYPE_INT,     'validate' => 'isInt'),
            'active'   => array('type' => self::TYPE_BOOL,    'validate' => 'isBool'),
        ),
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        Shop::addTableAssociation(self::$definition['table'], array('type' => 'shop'));
        parent::__construct($id, $id_lang, $id_shop);
        if (!empty($this->hook)) {
            $this->hook_cur = $this->hook;
        }
    }

    public function add($auto_date = true, $null_values = false)
    {
        return parent::add($auto_date, $null_values);
    }

    public function update($null_values = false)
    {
        return parent::update($null_values);
    }

    public function validateController($htmlentities = true)
    {
        return parent::validateController($htmlentities);
    }

    public static function getAllPctures($id_shop = null, $active = false)
    {
        if(empty($id_shop)){
            $id_shop = Context::getContext()->shop->id;
        }
        $sql = 'SELECT * FROM '._DB_PREFIX_.self::$definition['table'].' t
                LEFT JOIN '._DB_PREFIX_.self::$definition['table'].'_shop ts on(t.'.self::$definition['primary'].' = ts.'.self::$definition['primary'].')
                WHERE id_shop = '.$id_shop.($active?" AND t.active = 1 ORDER BY order":'');

       if (!($pictures = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql))) {
            return array();
        }

        return $pictures;
    }

    public static function getActivePicturesByHook($hook_name, $active = true)
    {
        $html_blocks = self::getAllPctures(Context::getContext()->shop->id);
        $active_blocks = array();
        $hook_name = strtolower($hook_name);
        $retro_name = strtolower(Hook::getRetroHookName($hook_name));
        foreach ($html_blocks as $html_block){
            if ($hook_name == $html_block['hook']) {
                $active_blocks[] = $html_block;
            } elseif ($retro_name == $html_block['hook']) {
                $active_blocks[] = $html_block;
            }
        }

        foreach ($active_blocks as &$a_b) {
            $a_b['image'] = Context::getContext()->link->getMediaLink(_MODULE_DIR_.'picture/images/'.$a_b['image']);
        }

        return $active_blocks;
    }

    public static function getBlockHTML($id_pwblockhtml)
    {
        if ( !$id_pwblockhtml ) return array();

        $block_html = new static($id_pwblockhtml);
        return $block_html->getFields();
    }
}
