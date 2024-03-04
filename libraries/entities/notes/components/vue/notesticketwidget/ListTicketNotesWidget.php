<?php

namespace Entities\Notes\Components\Vue\NotesTicketWidget;

use App\Website\Vue\Classes\VueProps;
use Entities\Notes\Components\Vue\NotesWidget\ListNotesWidget;
use Entities\Notes\Components\Vue\NotesWidget\ManageNotesWidget;
use Entities\Notes\Models\NoteModel;

class ListTicketNotesWidget extends ListNotesWidget
{
    protected string $id                = "b6e7dbd7-084b-4ad2-a196-b323e985eedc";
    protected string $noEntitiesWarning = "No notes...";
    protected string $batchLoadEndpoint = "api/v1/notes/get-ticket-note-batches";

    public function __construct (array $components = [])
    {
        $defaultEntity = (new NoteModel())->setDefaultSortColumn("note_id", "DESC")->setDisplayColumns([
                "date",
                "type",
                "creator",
                "summary",
                "visibility",
                "ticket"
            ]
            )->setRenderColumns([
                "note_id",
                "summary",
                "creator",
                "description",
                "date",
                "visibility",
                "type",
                "ticket",
                "created_on",
                "last_updated"
            ]
            );

        parent::__construct($defaultEntity, $components);

        $filterEntity          = new VueProps("filterEntityId", "object", "filterEntityId");
        $filterByEntityValue   = new VueProps("filterByEntityValue", "boolean", "filterByEntityValue");
        $filterByEntityRefresh = new VueProps("filterByEntityRefresh", "boolean", true);

        $this->addProp($filterEntity);
        $this->addProp($filterByEntityValue);
        $this->addProp($filterByEntityRefresh);

        $this->setEntityPageDisplayCount(5);
    }

    protected function renderEntityManagementModals (): string
    {
        return '
            addMainEntity: function()
            {
                ' . $this->activateDynamicComponentByIdInModal(ManageNotesWidget::getStaticId(), "", "add", "{}", "this.mainEntityList", [
                "entityUserId" => "this.mainEntity.ticket_id",
                "entityType"   => "'ticket'"
            ], "this", true
            ) . '
            },
            editMainEntity: function(entity)
            {    
                ' . $this->activateDynamicComponentByIdInModal(ManageNotesWidget::getStaticId(), "", "edit", "entity", "this.mainEntityList", [
                "entityUserId" => "this.mainEntity.ticket_id",
                "entityType"   => "'ticket'"
            ], "this", true
            ) . '
            },';
    }
}