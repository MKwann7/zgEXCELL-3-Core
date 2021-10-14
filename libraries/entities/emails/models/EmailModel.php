<?php

namespace Entities\Emails\Models;

use App\Core\AppModel;

class EmailModel extends AppModel
{
    protected $EntityName = "Emails";
    protected $ModelName = "Email";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }
    private function loadDefinitions()
    {
        return [
            "email_id" => ["type" => "int","length" => 15],
            "user_id" => ["type" => "int","length" => 15,"fk" => ["table" => "user","key" => "user_id","value" => "username"],"nullable" => true],
            "card_id" => ["type" => "int","length" => 15,"nullable" => true],
            "company_id" => ["type" => "int","length" => 15,"fk" => ["table" => "company","key" => "company_id","value" => "company_name"]],
            "division_id" => ["type" => "int","length" => 15,"fk" => ["table" => "division","key" => "division_id","value" => "division_name"]],
            "email_type_id" => ["type" => "int","length" => 5,"fk" => ["table" => "email_type","key" => "email_type_id","value" => "name"]],
            "content_file_reference" => ["type" => "char","length" => 36,"nullable" => true],
            "created_on" => ["type" => "datetime"],
            "created_by" => ["type" => "int","length" => 15,"fk" => ["table" => "user","key" => "user_id","value" => "username"]],
            "sys_row_id" => ["type" => "char","length" => 36,"nullable" => true]
        ];
    }
}