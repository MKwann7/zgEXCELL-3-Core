<?php

namespace Entities\Groups\Classes\Base;

use App\Core\AppController;
use Entities\Groups\Classes\Groups;

class GroupController extends AppController
{
    public function __construct($app)
    {
        $this->AppEntity = new Groups();
        parent::__construct($app);
    }
}