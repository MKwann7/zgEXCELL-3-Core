<?php

namespace Entities\Notes\Components\Vue\NotesWidget;

use App\Website\Vue\Classes\Base\VueComponent;
use Entities\Notes\Models\NoteModel;

class ManageNotesAdminWidget extends VueComponent
{
    protected string $id = "27159358-f9be-4fdd-894a-86c11b1bd7d2";
    protected string $title = "Note Dashboard";
    protected string $endpointUriAbstract = "note-dashboard/{id}";

    public function __construct(array $components = [])
    {
        $defaultEntity = (new NoteModel());

        parent::__construct($defaultEntity, $components);

        $this->modalTitleForAddEntity = "Add Note";
        $this->modalTitleForEditEntity = "Edit Note";
        $this->modalTitleForDeleteEntity = "Delete Note";
        $this->modalTitleForRowEntity = "View Note";
    }
}