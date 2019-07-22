<?php

class AttributeDetails extends ObjectModel {
     
    public $title;
    public $content;
    public $cover_image;
    public $active;

    public static $definition = [
        'table' => 'mattributedetails',
        'primary' => 'id_mattributedetails',
        'fields' => [
            'title' =>          array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 128),
            'content' =>        array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
            'cover_image' =>    array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 128),
            'active' =>         array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
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

    
    public static function getAll() {
        return Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'mattributedetails`');
    }

}