<?php

namespace Entities\Activities\Classes\Base;

use App\Core\AppController;
use Entities\Activities\Classes\AdminLogs;

class AdminLogController extends AppController
{
    public function __construct($app)
    {
        $this->AppEntity = new AdminLogs();
        parent::__construct($app);
    }
}