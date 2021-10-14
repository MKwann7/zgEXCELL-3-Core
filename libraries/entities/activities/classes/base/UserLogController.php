<?php

namespace Entities\Activities\Classes\Base;

use App\Core\AppController;
use Entities\Activities\Classes\UserLogs;

class UserLogController extends AppController
{
    public function __construct($app)
    {
        $this->AppEntity = new UserLogs();
        parent::__construct($app);
    }
}