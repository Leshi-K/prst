<?php
class Students extends ObjectModel
{
    public $id_students;
    public $date_born;
    public $average_mark;
    public $active;
    public $name;

	public static $definition = array(
	  'table' => 'students',
	  'primary' => 'id_students',
	  'multilang' => true,
	  'fields' => array(
	    'date_born'    => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
	    'average_mark' => array('type' => self::TYPE_INT),
	    'active'       => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),

	    // Language fields
	    'name'         => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 300),
	  ),
	);

	public function getAllStudents($idLang) {

        $sql = new DbQuery();
        $sql->select('`name`');
        $sql->from('students_lang');
        $sql->where('id_lang = ' . (int)$idLang);
        $result = Db::getInstance()->executeS($sql);

        return $result;
    }

    public function getBestStudent() {

        $sql = new DbQuery();
        $sql->select('`name`');
        $sql->from('students_lang', 'sl');
        $sql->innerJoin('students', 's', 'sl.id_students = s.id_students AND sl.id_lang = '.(int)$id_lang);
        $sql->orderBy('s.average_mark DESC');
        $sql->limit('1'); // В принципе, таких учеников может быть несколько, но в задании написано найти одного
        $result = Db::getInstance()->executeS($sql);

        return $result;
    }

    public function getMaxAverageMark() {

        $sql = new DbQuery();
        $sql->select('MAX(`average_mark`)');
        $sql->from('students');
        $result = Db::getInstance()->executeS($sql);

        return $result;
    }
}