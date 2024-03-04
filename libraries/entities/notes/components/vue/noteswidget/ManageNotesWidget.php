<?php

namespace Entities\Notes\Components\Vue\NotesWidget;

use App\Website\Vue\Classes\Base\VueComponent;

class ManageNotesWidget extends VueComponent
{
    protected string $id = "d0cc2044-a7e3-43b9-9f84-1a4b379b0d0a";
    protected string $title = "Note Dashboard";
    protected string $endpointUriAbstract = "note-dashboard/{id}";

    public function __construct(array $components = [])
    {
        parent::__construct();

        $this->modalTitleForAddEntity = "Create New Note";
        $this->modalTitleForEditEntity = "Edit Note";
        $this->modalTitleForDeleteEntity = "Delete Note";
        $this->modalTitleForRowEntity = "View Note";
    }

    protected function renderComponentDataAssignments() : string
    {
        return "
            entityClone: {},
            showTicketDialog: false,
        ";
    }

    protected function renderComponentMethods() : string
    {
        return '
            updateNote: function()
            {
                modal.EngageFloatShield();
                
                let self = this;
                let url = "/api/v1/notes/create";
                
                let noteData = {
                    summary: this.entityClone.summary, 
                    description: this.entityClone.description, 
                    visibility: this.entityClone.visibility, 
                    type: this.entityClone.type, 
                    creator_id: this.parentData.loggedInUser.user_id, 
                    entity_id: this.entityUserId, 
                    entity_name: this.entityType, 
                };
                
                ezLog(noteData, "NoteData");
                
                if (this.action === "edit")
                {
                    url = "/api/v1/notes/update";
                    noteData["note_id"] = this.entity.note_id;
                }
                
                ajax.Post(url, noteData, function(result)
                {
                    modal.CloseFloatShield();
                    
                    if (result.success === false) 
                    {
                        ezLog(result, "Update Note Error");
                        return;
                    }
                    
                    self.entity.summary = self.entityClone.summary;
                    self.entity.visibility = self.entityClone.visibility;
                    self.entity.type = self.entityClone.type;
                    
                    ezLog(result.data);
                    
                    if (self.action === "add")
                    {
                        self.entities.push(result.response.data.note);
                    }

                    let vue = self.findApp(self);
                    vue.$forceUpdate();
                                 
                    let objModal = self.findModal(self);                 
                    objModal.close();         
                });
            },
            actionDisplay: function()
            {
                switch(this.action)
                {
                    case "add":
                        return "Save New";
                    default:
                        return this.action;
                }
            },
            openNewTicketDialog: function()
            {
                this.showTicketDialog = true;
            },
        ';
    }

    protected function renderComponentHydrationScript() : string
    {
        return parent::renderComponentHydrationScript() . "
            this.entityClone = _.clone(this.entity);
            
            if (typeof this.entityClone.visibility === 'undefined')
            {
                this.entityClone.visibility = '';
            }
            
            if (typeof this.entityClone.type === 'undefined')
            {
                this.entityClone.type = '';
            }
            
            this.showTicketDialog = false;
        ";
    }

    protected function renderTemplate() : string
    {
        return '<div>
            <div class="note-dailog">
                <table class="table no-top-border">
                    <tbody>
                        <tr v-if="action === \'edit\'">
                            <td style="width:100px;vertical-align: middle;">Creator</td>
                            <td>
                                {{ entityClone.creator }}
                            </td>
                        </tr>
                        <tr>
                            <td style="width:100px;vertical-align: middle;">Summary</td>
                            <td>
                                <input class="form-control" v-model="entityClone.summary" type="text" placeholder="Enter Summary...">
                            </td>
                        </tr>
                        <tr>
                            <td style="width:100px;vertical-align: middle;">Type</td>
                            <td>
                                <select v-model="entityClone.type" class="form-control">
                                    <option value="" selected disabled>--Select Note Type--</option>
                                    <option value="information">Information</option>
                                    <option value="card-build">Card Build</option>
                                    <option value="card-maintenance">Card Maintenance</option>
                                    <option value="module-tools">Module/Tools</option>
                                    <option value="billing">Billing</option>
                                    <option value="technical">Technical</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:100px;vertical-align: middle;">Description</td>
                            <td>
                                <textarea class="form-control" v-model="entityClone.description" placeholder="Enter Description..."></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:100px;vertical-align: middle;">Visibility</td>
                            <td>
                                <select v-model="entityClone.visibility" class="form-control">
                                    <option value="" selected disabled>--Select Note Visibility--</option>
                                    <option value="public">Public</option>
                                    <option value="admin">Admin Only</option>
                                    <option value="ezdigital">EZ Digital Only</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:100px;vertical-align: middle;">Ticket</td>
                            <td>
                                <button v-on:click="openNewTicketDialog" class="btn btn-primary">Add New Ticket</button> <button class="btn btn-primary">Link To Existing</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-show="showTicketDialog === true" class="ticket-dialo">
                <table class="table no-top-border">
                    <tbody>
                        <tr>
                            <td style="width:100px;vertical-align: middle;">Ticket Name</td>
                            <td>
                                <input class="form-control" type="text" placeholder="Enter Name..."></td>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:100px;vertical-align: middle;">Team Association</td>
                            <td>
                                <select  class="form-control">
                                    <option value="" selected disabled>--Select Team--</option>
                                    <option value="customer-service">Customer Service</option>
                                    <option value="operations">Operations</option>
                                    <option value="sales">Sales</option>
                                    <option value="it">IT</option>
                                    <option value="engineering">Engineering</option>
                                    <option value="ezdigital">EZ Digital</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:100px;vertical-align: middle;">Assign Team Member</td>
                            <td>
                                <select class="form-control">
                                    <option value="" selected disabled>--Select User --</option>
                                    <option value="1000">Micah Zak</option>
                                    <option value="1001">Jerry Johnson</option>
                                    <option value="1003">Morgan Brint</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <button class="btn btn-primary w-100" v-on:click="updateNote">{{ ucwords(actionDisplay()) }} Note</button>
        </div>';
    }
}