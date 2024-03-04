<?php

namespace Entities\Settings\Components\Vue\SettingsWidget;

use App\Website\Vue\Classes\Base\VueComponent;

class ManageSettingsWidget extends VueComponent
{
    protected string $id = "d2e94688-b2f8-434a-a9a4-78d2323d2385";
    protected string $title = "My Settings";

    public function __construct(array $components = [])
    {
        parent::__construct();
    }
}