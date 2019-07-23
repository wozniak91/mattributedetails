<?php

class AttributeDetails extends ObjectModel {
     
    public $title;
    public $cover_image;
    public $active;
    public $content;

    public static $definition = [
        'table' => 'mattributedetails',
        'primary' => 'id_mattributedetails',
        'fields' => [
            'title' =>          array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 128),
            'cover_image' =>    array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 128),
            'active' =>         array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'content' =>        array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
        ]
    ];

    /**
     * Deletes current object from database
     *
     * @return bool True if delete was successful
     * @throws PrestaShopException
     */

    public function delete()
    {
        $this->deleteCoverImage();
        return parent::delete();
    }

    public function deleteCoverImage() {

        if(file_exists($this->getCoverPath())) {
            unlink($this->getCoverPath());
        }

        return true;
    }

    public function getCoverPath() {
        return _PS_MODULE_DIR_.'mattributedetails/images/'.$this->cover_image;
    }

    
    public static function getAll($only_active = false) {
        return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'mattributedetails`'.($only_active ? ' WHERE active = 1' : ''));
    }

    public static function search($query) {

        if(!empty($query)) {

            $query = preg_replace('/\d+/', '', $query);
            $query = explode(" ", $query);
            $query = array_unique($query);
            $query = array_filter($query, 'strlen');
            
            $sql = 'SELECT * FROM `'._DB_PREFIX_.'mattributedetails` WHERE active = 1 AND';

            foreach ($query as $k => $q) {

                $title = pSQL(strtolower($q));

                $sql .= ($k ? ' OR': '')." LOWER(title) LIKE '%{$title}%'";
            }

            return Db::getInstance()->executeS($sql);

        } else {
            return array();
        }


    }

}