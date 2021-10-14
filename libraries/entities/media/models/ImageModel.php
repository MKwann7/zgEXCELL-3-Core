<?php

namespace Entities\Media\Models;

use App\Core\AppModel;

class ImageModel extends AppModel
{
    protected $EntityName = "Media";
    protected $ModelName = "Image";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [
            "image_id" => ["type" => "int","length" => 15],
            "user_id" => ["type" => "int","length" => 15,"fk" => ["table" => "user","key" => "user_id","value" => "username"]],
            "entity_id" => ["type" => "int","length" => 15],
            "entity_name" => ["type" => "varchar","length" => 45],
            "image_class" => ["type" => "varchar","length" => 25],
            "title" => ["type" => "varchar","length" => 75],
            "url" => ["type" => "varchar","length" => 150],
            "thumb" => ["type" => "varchar","length" => 150],
            "width" => ["type" => "int","length" => 6],
            "height" => ["type" => "int","length" => 6],
            "type" => ["type" => "varchar","length" => 15],
            "created_on" => ["type" => "datetime"],
            "created_by" => ["type" => "int","length" => 15,"fk" => ["table" => "user","key" => "user_id","value" => "username"]],
            "last_updated" => ["type" => "datetime"],
            "updated_by" => ["type" => "int","length" => 15,"fk" => ["table" => "user","key" => "user_id","value" => "username"]],
            "sys_row_id" => ["type" => "char","length" => 36]
        ];
    }
}