<?php

namespace Entities\Notes\Components\Vue\NotesCardWidget;

use App\Website\Vue\Classes\VueProps;
use Entities\Notes\Components\Vue\NotesWidget\ListNotesWidget;
use Entities\Notes\Components\Vue\NotesWidget\ManageNotesWidget;
use Entities\Notes\Models\NoteModel;

class ListCardNotesWidget extends ListNotesWidget
{
    protected $id = "af8986f8-8ec8-40a2-9831-46285e374b84";
    protected $noEntitiesWarning = "No notes...";
    protected $batchLoadEndpoint = "api/v1/notes/get-card-note-batches";

    public function __construct(array $components = [])
    {
        $defaultEntity = (new NoteModel())
            ->setDefaultSortColumn("note_id", "DESC")
            ->setDisplayColumns(["date", "type", "creator", "summary", "visibility", "ticket"])
            ->setRenderColumns(["note_id", "summary", "creator", "description", "date", "visibility", "type", "ticket", "created_on", "last_updated"]);

        parent::__construct($defaultEntity, $components);

        $filterEntity = new VueProps("filterEntityId", "object", "filterEntityId");
        $filterByEntityValue = new VueProps("filterByEntityValue", "boolean", "filterByEntityValue");
        $filterByEntityRefresh = new VueProps("filterByEntityRefresh", "boolean", true);

        $this->addProp($filterEntity);
        $this->addProp($filterByEntityValue);
        $this->addProp($filterByEntityRefresh);

        $this->setEntityPageDisplayCount(5);
    }

    protected function renderEntityManagementModals() : string
    {
        return '
            addMainEntity: function()
            {
                '. $this->activateDynamicComponentByIdInModal(ManageNotesWidget::getStaticId(), "","add", "{}", "this.mainEntityList", ["entityUserId" => "this.mainEntity.card_id", "entityType" => "'card'"], "this", true).'
            },
            editMainEntity: function(entity)
            {    
                '. $this->activateDynamicComponentByIdInModal(ManageNotesWidget::getStaticId(), "","edit", "entity", "this.mainEntityList", ["entityUserId" => "this.mainEntity.card_id", "entityType" => "'card'"], "this", true ).'
            },';
    }
}