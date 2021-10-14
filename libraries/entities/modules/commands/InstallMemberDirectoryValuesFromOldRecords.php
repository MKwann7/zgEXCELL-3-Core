<?php

namespace Entities\Modules\Commands;

use App\Utilities\Command\Command;
use Modules\Ezcard\Widgets\MemberDirectory\Classes\EzcardMemberDirectories;
use Modules\Ezcard\Widgets\MemberDirectory\Classes\EzcardMemberDirectoryRecords;
use Modules\Ezcard\Widgets\MemberDirectory\Classes\EzcardMemberDirectoryRecordValues;
use Modules\Ezcard\Widgets\MemberDirectory\Models\EzcardMemberDirectoryModel;
use Modules\Ezcard\Widgets\MemberDirectory\Models\EzcardMemberDirectoryRecordModel;
use Modules\Ezcard\Widgets\MemberDirectory\Models\EzcardMemberDirectoryRecordValueModel;

class InstallMemberDirectoryValuesFromOldRecords extends Command
{
    public $name = "Apps.InstallMemberData";
    public $description = "This is a data migration process to loop through member directory data and create value records that work with the new system.";

    /**
     * Executes the command
     */
    public function Run()
    {
        $this->MigrateMemberDirectoryRecordDataToValues();
    }

    protected function MigrateMemberDirectoryRecordDataToValues() : void
    {
        $objMemDir = new EzcardMemberDirectories();
        $objMemDirRecords = new EzcardMemberDirectoryRecords();

        $colMemberDirectories = $objMemDir->getWhere(["instance_uuid" => "25b6248a-0fa9-4a92-8442-dbfb73c1338e"])->Data;

        $colMemberDirectories->Each(function(EzcardMemberDirectoryModel $currDirectory) use ($objMemDirRecords) {
            $colMemDirRecords = $objMemDirRecords->getWhere(["member_directory_id" => $currDirectory->member_directory_id])->Data;

            $objMemberRecordValues = new EzcardMemberDirectoryRecordValues();

            $colMemDirRecords->Each(function(EzcardMemberDirectoryRecordModel $currMemDirRecord) use ($objMemberRecordValues, $objMemDirRecords) {

                if ($currMemDirRecord->migrated === true)
                {
                    return;
                }

                $this->processCustomValueMigration($objMemberRecordValues, 1000, $currMemDirRecord->about_section, "text", $currMemDirRecord->member_directory_record_id, $currMemDirRecord->member_directory_id);
                $this->processCustomValueMigration($objMemberRecordValues, 1001, $currMemDirRecord->profile_image_url, "url", $currMemDirRecord->member_directory_record_id, $currMemDirRecord->member_directory_id);
                $this->processCustomValueMigration($objMemberRecordValues, 1002, $currMemDirRecord->organization_name, "text", $currMemDirRecord->member_directory_record_id, $currMemDirRecord->member_directory_id);
                $this->processCustomValueMigration($objMemberRecordValues, 1003, $currMemDirRecord->tag_line, "text", $currMemDirRecord->member_directory_record_id, $currMemDirRecord->member_directory_id);
                $this->processCustomValueMigration($objMemberRecordValues, 1004, $currMemDirRecord->organizations_about, "text", $currMemDirRecord->member_directory_record_id, $currMemDirRecord->member_directory_id);
                $this->processCustomValueMigration($objMemberRecordValues, 1005, $currMemDirRecord->organization_phone, "phone", $currMemDirRecord->member_directory_record_id, $currMemDirRecord->member_directory_id);
                $this->processCustomValueMigration($objMemberRecordValues, 1006, $currMemDirRecord->logo_url, "url", $currMemDirRecord->member_directory_record_id, $currMemDirRecord->member_directory_id);
                $this->processCustomValueMigration($objMemberRecordValues, 1007, $currMemDirRecord->street_address_1, "text", $currMemDirRecord->member_directory_record_id, $currMemDirRecord->member_directory_id);
                $this->processCustomValueMigration($objMemberRecordValues, 1008, $currMemDirRecord->street_address_2, "text", $currMemDirRecord->member_directory_record_id, $currMemDirRecord->member_directory_id);
                $this->processCustomValueMigration($objMemberRecordValues, 1009, $currMemDirRecord->city, "text", $currMemDirRecord->member_directory_record_id, $currMemDirRecord->member_directory_id);
                $this->processCustomValueMigration($objMemberRecordValues, 1010, $currMemDirRecord->state_province, "us_state", $currMemDirRecord->member_directory_record_id, $currMemDirRecord->member_directory_id);
                $this->processCustomValueMigration($objMemberRecordValues, 1011, $currMemDirRecord->postal_code, "us_postal", $currMemDirRecord->member_directory_record_id, $currMemDirRecord->member_directory_id);


                $this->processPositionCustomValueMigration($objMemberRecordValues, 1012, $currMemDirRecord, "position_1", $currMemDirRecord->member_directory_record_id, $currMemDirRecord->member_directory_id);
                $this->processPositionCustomValueMigration($objMemberRecordValues, 1013, $currMemDirRecord, "position_2", $currMemDirRecord->member_directory_record_id, $currMemDirRecord->member_directory_id);
                $this->processPositionCustomValueMigration($objMemberRecordValues, 1014, $currMemDirRecord, "position_3", $currMemDirRecord->member_directory_record_id, $currMemDirRecord->member_directory_id);
                $this->processPositionCustomValueMigration($objMemberRecordValues, 1015, $currMemDirRecord, "position_4", $currMemDirRecord->member_directory_record_id, $currMemDirRecord->member_directory_id);

                $currMemDirRecord->migrated = ExcellTrue;
                $objMemDirRecords->update($currMemDirRecord);

                dump("Name: " . $currMemDirRecord->first_name . " " . $currMemDirRecord->last_name);
            });
        });
    }

    protected function processPositionCustomValueMigration($objMemberRecordValues, $intColoumnId, $currMemDirRecord, $strpositionNumber, $intMemberRecordId, $intMemberDirectoryId)
    {
        switch(strtolower($currMemDirRecord->position_1_icon))
        {
            case "text sms":
                $this->processCustomValueMigration($objMemberRecordValues, $intColoumnId, $currMemDirRecord->{$strpositionNumber . "_phone"}, "sms", $intMemberRecordId, $intMemberDirectoryId);
                break;
            case "call mobile":
                $this->processCustomValueMigration($objMemberRecordValues, $intColoumnId, $currMemDirRecord->{$strpositionNumber . "_phone"}, "phone", $intMemberRecordId, $intMemberDirectoryId);
                break;
            case "email":
                $this->processCustomValueMigration($objMemberRecordValues, $intColoumnId, $currMemDirRecord->{$strpositionNumber . "_email"}, "email", $intMemberRecordId, $intMemberDirectoryId);
                break;
            case "schedule (calendar)":
            case "url":
                $this->processCustomValueMigration($objMemberRecordValues, $intColoumnId, $currMemDirRecord->{$strpositionNumber . "_url"}, "url", $intMemberRecordId, $intMemberDirectoryId);
                break;
        }
    }

    protected function processCustomValueMigration($objMemberRecordValues, $intColumnId, $objValue, $strType, $intMemberRecordId, $intMemberDirectoryId)
    {
        if ($objValue === null || $objValue === '') { return; }

        $objMemberRecordValueResult = $objMemberRecordValues->getWhere(["member_directory_column_id" => $intColumnId, "value" => $objValue, "member_directory_record_id" => $intMemberDirectoryId]);

        if ($objMemberRecordValueResult->Result->Count > 0)
        {
            return;
        }

        $objMemberRecordValueModel = new EzcardMemberDirectoryRecordValueModel();
        $objMemberRecordValueModel->member_directory_id = $intMemberRecordId;
        $objMemberRecordValueModel->member_directory_record_id = $intMemberDirectoryId;
        $objMemberRecordValueModel->member_directory_column_id = $intColumnId;
        $objMemberRecordValueModel->type = $strType;
        $objMemberRecordValueModel->value = $objValue;

        $result = $objMemberRecordValues->createNew($objMemberRecordValueModel);
    }
}