<?php

namespace Entities\Users\Components\Vue\UserWidget;

use App\Website\Vue\Classes\Base\VueComponent;
use App\Website\Vue\Classes\Base\VueCustomMethods;
use App\Website\Vue\Classes\VueProps;
use Entities\Cards\Components\Vue\CardWidget\ListCardWidget;
use Entities\Cards\Components\Vue\CardWidget\ManageCardImageWidget;
use Entities\Cards\Components\Vue\CardWidget\ManageCardMainColorWidget;
use Entities\Cards\Components\Vue\CardWidget\ManageCardProfileWidget;
use Entities\Notes\Components\Vue\NotesCustomerWidget\ListCustomerNotesWidget;
use Entities\Users\Components\Vue\ConnectionWidget\ManageUserConnectionsListWidget;
use Entities\Users\Components\Vue\ConnectionWidget\ManageUserConnectionsWidget;
use Entities\Users\Models\UserModel;

class ManageCustomerWidget extends ManageUserWidget
{
    protected string $id = "e3564b0f-c3bb-446e-ab27-5756a197a5fe";
    protected string $title = "Customer Dashboard";
    protected string $endpointUriAbstract = "customer-dashboard/{id}";
}