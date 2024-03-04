<?php

namespace Entities\Users\Components\Vue\ConnectionWidget;

use App\Website\Vue\Classes\Base\VueComponent;
use Entities\Users\Models\UserModel;

class ManageUserConnectionsWidget extends VueComponent
{
    protected string $id = "601a0504-2b37-4a20-94eb-14379c699039";
    protected string $title = "User Connection";
    protected string $modalWidth = "750";

    public function __construct (array $components = [])
    {
        parent::__construct((new UserModel()), $components);

        $this->modalTitleForAddEntity = "Add User Connection";
        $this->modalTitleForEditEntity = "Edit User Connection";
        $this->modalTitleForDeleteEntity = "Delete User Connections";
        $this->modalTitleForRowEntity = "View User Connection";
    }

    protected function renderComponentDataAssignments() : string
    {
        return "
            entityClone: {},
            connectionTypeList: [],
        ";
    }

    protected function renderComponentMethods() : string
    {
        return '
            loadConnectionTypeList: function()
            {
                if (this.connectionTypeList.length > 0) return;
                
                let self = this;
                const url = "/api/v1/users/get-connection-types";
                ajax.Get(url, null, function(result)
                {
                    self.connectionTypeList = result.response.data.list;
                }, "GET");
            },
            updateConnection: function()
            {
                let self = this;
                const url = "/api/v1/users/update-user-connection?connection_id=" + this.entity.connection_id + "&action=" + this.action;
                const connectionData = {
                    connection_id: this.entity.connection_id, 
                    connection_type_id: this.entityClone.connection_type_id, 
                    connection_value: this.entityClone.connection_value, 
                    user_id: this.entityUserId, 
                };

                ajax.Send(url, connectionData, function(result)
                {
                    if (result.success === false) 
                    {
                        return;
                    }
                    
                    self.entity.connection_type_id = self.entityClone.connection_type_id;
                    self.entity.connection_type_name = self.getConnecionTypeNameById(self.entityClone.connection_type_id);
                    self.entity.font_awesome = self.getConnecionTypeFontById(self.entityClone.connection_type_id);
                    self.entity.connection_value = self.entityClone.connection_value;
                    
                    if (self.action === "add")
                    {
                        self.entities.push(result.response.data.connection);
                    }

                    let vue = self.findApp(self);
                    vue.$forceUpdate();
                                 
                    let modal = self.findModal(self);                 
                    modal.close();         
                });
            },
            getConnecionTypeNameById: function(id)
            {
                for(let currConnectionType of this.connectionTypeList)
                {
                    if (currConnectionType.connection_type_id === id)
                    {
                        return currConnectionType.name;
                    }
                }
                
                return "Unknown";
            },
            getConnecionTypeFontById: function(id)
            {
                for(let currConnectionType of this.connectionTypeList)
                {
                    if (currConnectionType.connection_type_id === id)
                    {
                        return currConnectionType.font_awesome;
                    }
                }
                
                return "Unknown";
            },
        ';
    }

    protected function renderComponentHydrationScript() : string
    {
        return parent::renderComponentHydrationScript() . "
            this.loadConnectionTypeList();
            this.entityClone = _.clone(this.entity);
        ";
    }

    protected function renderTemplate() : string
    {
        return '<div>
            <table class="table no-top-border">
                <tbody>
                    <tr>
                        <td style="width:100px;vertical-align: middle;">Type</td>
                        <td>
                            <select v-model="entityClone.connection_type_id" class="form-control">
                                <option>--Select Connection Type--</option>
                                <option v-if="connectionTypeList" v-for="connectionType in connectionTypeList" :value="connectionType.connection_type_id">{{ connectionType.name }}</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100px;vertical-align: middle;">Value</td>
                        <td><input class="form-control" v-model="entityClone.connection_value" type="text" placeholder="Enter Connection Value..."></td>
                    </tr>
                </tbody>
            </table>
            <button class="btn btn-primary w-100" v-on:click="updateConnection">{{ ucwords(action) }} Connection</button>
        </div>';
    }
}