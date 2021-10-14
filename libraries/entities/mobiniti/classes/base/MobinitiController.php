<?php

namespace Entities\Mobiniti\Classes\Base;

use App\Core\AppController;
use Entities\Mobiniti\Classes\Mobiniti;

class MobinitiController extends AppController
{
    public function __construct($app)
    {
        $this->AppEntity = new Mobiniti();
        parent::__construct($app);
    }
}