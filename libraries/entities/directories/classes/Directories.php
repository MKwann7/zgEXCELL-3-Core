<?php

namespace Entities\Directories\Classes;

use App\Core\AppEntity;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Directories\Classes\Factories\DirectoryFactory;
use Entities\Directories\Models\DirectoryModel;

class Directories extends AppEntity
{
    public string $strEntityName       = "Directories";
    public $strDatabaseTable        = "directory";
    public $strDatabaseName         = "Apps";
    public $strMainModelName        = DirectoryModel::class;
    public $strMainModelPrimary     = "directory_id";
    public $isPrimaryModule         = true;

    public function getFullRecordByUuid(string $uuid) : ExcellTransaction
    {
        $objDirectoryResult = $this->getWhere(["instance_uuid" => $uuid]);
        return $this->buildFullDirectory($objDirectoryResult);
    }

    public function getDirectoryPersonaRecordsByUuid(string $uuid) : ExcellTransaction
    {
        $directoryResult = $this->getWhere(["instance_uuid" => $uuid]);

        if ($directoryResult->getResult()->Count === 0) {
            return new ExcellTransaction(false, "No directory was found by this UUID.");
        }

        $directoryFactory = new DirectoryFactory($this->app, new Directories(), new DirectoryMemberRels());

        if (!$directoryFactory->getPersonasFromDirectoryRecord($directoryResult->getData()->first())) {
            return $directoryResult;
        }

        $directoryResult->getData()->HydrateChildModelData("personas", ["directory_id" => "directory_id"], $directoryFactory->getPersonas());

        return $directoryResult;
    }

    public function getFullRecordAndMembersByUuid(string $uuid, string $addons = null) : ExcellTransaction
    {
        $directoryResult = $this->getFullRecordByUuid($uuid);

        if ($directoryResult->getResult()->Count === 0) {
            return new ExcellTransaction(false, "No directory was found by this UUID.");
        }

        $directoryFactory = new DirectoryFactory($this->app, new Directories(), new DirectoryMemberRels());
        if (!$directoryFactory->getPersonasFromDirectoryRecord($directoryResult->getData()->first())) {
            return new ExcellTransaction(false, $directoryFactory->getMessage());
        }

        $directoryResult->getData()->HydrateChildModelData("personas", ["directory_id" => "directory_id"], $directoryFactory->getPersonas());

        return $directoryResult;
    }

    public function findRegisteredUserId($id) : ExcellTransaction
    {
        $userRecords = new DirectoryMemberRels();
        return $userRecords->getWhere(["user_id" => $id]);
    }

    protected function buildFullDirectory(ExcellTransaction $objDirectoryResult) : ExcellTransaction
    {
//        print_r(traceArray());
//        dd($objDirectoryResult);
        $colDirectoryTemplate = (new DirectoryTemplates())->getById($objDirectoryResult->getData()->first()->template_id)->getData();
        $objDirectoryResult->getData()->HydrateChildModelData("template", ["directory_template_id" => "template_id"], $colDirectoryTemplate, true);

        $objDirectoryDefaultResult = (new DirectoryDefaults())->getWhere(["directory_id" => $objDirectoryResult->getData()->first()->directory_id]);
        $objDirectoryResult->getData()->HydrateChildModelData("defaults", ["directory_id" => "directory_id"], $objDirectoryDefaultResult->getData(), false);

        $objDirectorySettingsResult = (new DirectorySettings())->getWhere(["directory_id" => $objDirectoryResult->getData()->first()->directory_id]);
        $objDirectoryResult->getData()->HydrateChildModelData("settings", ["directory_id" => "directory_id"], $objDirectorySettingsResult->getData(), false);

        return $objDirectoryResult;
    }

    public function buildBatchWhereClause($filterIdField = null, $filterEntity = null, int $typeId = 1) : string
    {
        $objWhereClause = $this->entityListPrimaryDataForDisplay($filterIdField, $filterEntity);

//        if ($filterEntity !== null)
//        {
//            $objWhereClause .= "AND ( (cowner.{$filterIdField} = {$filterEntity} OR cuser.{$filterIdField} = {$filterEntity})";
//            $objWhereClause .= " OR (card_rel.{$filterIdField} = {$filterEntity} AND card_rel.status = 'Active') AND card_rel.card_rel_type_id != 9)"; // 9 = card affiliate
//        }

//        if (!in_array($this->app->getActiveLoggedInUser()->user_id, [1000, 1001, 90990]))
//        {
//            $objWhereClause .= " AND (card.template_card = 0) ";
//        }

        //$objWhereClause .= " AND card_type_id = {$typeId}";
        //$objWhereClause .= " GROUP BY(card.card_id) ORDER BY card.card_num DESC";
        $objWhereClause .= " ORDER BY dct.directory_id DESC";

        return $objWhereClause;
    }

    private function entityListPrimaryDataForDisplay($filterIdField = null, $filterEntity = null)
    {
        $objWhereClause = "SELECT dct.* FROM excell_apps.directory dct ";

        if ($filterEntity !== null)
        {
            $objWhereClause .= "LEFT JOIN excell_main.user usr ON usr.user_id = dct.user_id ";
        }

        //$objWhereClause .= "WHERE card.company_id = {$this->app->objCustomPlatform->getCompanyId()} AND card.status != 'Deleted' ";
        $objWhereClause .= "WHERE dct.company_id = {$this->app->objCustomPlatform->getCompanyId()} ";

        return $objWhereClause;
    }
}