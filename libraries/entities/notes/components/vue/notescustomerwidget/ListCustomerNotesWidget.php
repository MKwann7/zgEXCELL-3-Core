<?php

namespace Entities\Notes\Components\Vue\NotesCustomerWidget;

use App\Website\Vue\Classes\VueProps;
use Entities\Notes\Components\Vue\NotesWidget\ListNotesWidget;
use Entities\Notes\Components\Vue\NotesWidget\ManageNotesWidget;
use Entities\Notes\Models\NoteModel;

class ListCustomerNotesWidget extends ListNotesWidget
{
    protected $id = "61673966-90f6-4576-ba61-75f5c1bda28b";
    protected $noEntitiesWarning = "No notes...";
    protected $batchLoadEndpoint = "api/v1/notes/get-customer-note-batches";

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
                '. $this->activateDynamicComponentByIdInModal(ManageNotesWidget::getStaticId(), "","add", "{}", "this.mainEntityList", ["entityUserId" => "this.mainEntity.user_id", "entityType" => "'customer'"], "this", true).'
            },
            editMainEntity: function(entity)
            {    
                '. $this->activateDynamicComponentByIdInModal(ManageNotesWidget::getStaticId(), "","edit", "entity", "this.mainEntityList", ["entityUserId" => "this.mainEntity.user_id", "entityType" => "'customer'"], "this", true ).'
            },';
    }
}