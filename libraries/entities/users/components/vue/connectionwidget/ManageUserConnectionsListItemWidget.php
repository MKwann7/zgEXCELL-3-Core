<?php

namespace Entities\Users\Components\Vue\ConnectionWidget;

use App\website\vue\classes\VueComponentListTable;

class ManageUserConnectionsListItemWidget extends VueComponentListTable
{
    protected $id = "9664416b-d393-4fc2-b8fb-2576f1381e97";
    protected $noMount = true;

    public function __construct(?array $props = [])
    {
        parent::__construct(null, null, $props);
    }

    protected function renderTemplate() : string
    {
        return '
            <tr class="cardConnection pointer sortable-item" v-on:dblclick="editConnection(connection)">
                <v-style>
                    .cardConnection .cardConnectionHandle,
                    .cardConnection .cardConnectionLabel,
                    .cardConnection .cardConnectionType,
                    .cardConnection .cardConnectionOrder {
                        width: 15px;
                    }
                </v-style>
                <td class="cardConnectionHandle"><span v-handle class="handle"></span></td>
                <td class="cardConnectionOrder mobile-hide">{{ connection.display_order }}</td>
                <td class="cardConnectionType" style="width:35px;text-align:center;" v-bind:alt="connection.connection_type_name" v-bind:title="connection.connection_type_name"><span v-bind:class="connection.font_awesome"></span></td>
                <td class="cardConnectionLabel mobile-hide">{{ connection.connection_type_name }}</td>
                <td class="cardConnectionLabel mobile-hide"><b>{{ displayAction(connection.action) }}</b></td>
                <td><strong class="entityEmailName">{{ trunc(connection.connection_value, 35, true) }}</strong></td>
                <td class="text-right">
                    <span v-on:click="editConnection(connection)" class="pointer editEntityButton"></span>
                    <span v-bind:class="{ disabledButton: connection.connection_type_name == \'blank\' }" v-on:click="removeConnection(connection)" class="pointer deleteEntityButton" style="margin-left:6px;"></span>
                </td>
            </tr>
        ';
    }
}