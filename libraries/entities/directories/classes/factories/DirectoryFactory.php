<?php

namespace Entities\Directories\Classes\Factories;

use App\Core\Abstracts\AbstractFactory;
use App\Core\App;
use App\Utilities\Excell\ExcellCollection;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Cards\Classes\Cards;
use Entities\Cards\Models\CardModel;
use Entities\Directories\Classes\Directories;
use Entities\Directories\Classes\DirectoryMemberRels;
use Entities\Directories\Models\DirectoryModel;

class DirectoryFactory extends AbstractFactory
{
    private App $app;
    private Directories $directories;
    private DirectoryMemberRels $memberRels;
    private ExcellCollection $directoryPersonas;

    public function __construct(App $app, Directories $directories, DirectoryMemberRels $memberRels)
    {
        $this->app = $app;
        $this->directories = $directories;
        $this->memberRels = $memberRels;
        $this->directoryPersonas = new ExcellCollection();
    }

    public function getPersonasFromDirectoryRecord(DirectoryModel $directory) : bool
    {
        $directoryRel = new DirectoryMemberRels();
        $directoryRelResult = $directoryRel->getWhere(["directory_id" => $directory->directory_id]);

        if ($directoryRelResult->getResult()->Count === 0) {
            $this->addError("no_personas", "no personas are registered for this directory", "This directory is empty.");
            $this->addMessage("This directory is empty.");
            return false;
        }

        $cards = new Cards();
        $personasResult = $cards->getWhereIn("card_id", $directoryRelResult->getData()->FieldsToArray(["persona_id"]));

        $personasResult->getData()->Foreach(function(CardModel $site) {
            $site->LoadCardOwner();
            $site->LoadCardSettings();
            return $site;
        });

        $directoryRelResult->getData()->HydrateChildModelData("persona", ["card_id" => "persona_id"], $personasResult->getData(), true);
        $this->directoryPersonas = $directoryRelResult->getData();

        return true;
    }

    public function getPersonas() : ExcellCollection
    {
        return $this->directoryPersonas;
    }
}