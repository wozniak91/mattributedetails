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
            'active' =>         array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
        ]
    ];

}