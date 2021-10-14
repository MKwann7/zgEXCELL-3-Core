<?php

namespace Entities\Opportunity\Classes\Base;

use App\Core\AppController;
use Entities\Opportunity\Classes\Opportunity;

class OpportunityController extends AppController
{
    public function __construct($app)
    {
        $this->AppEntity = new Opportunity();
        parent::__construct($app);
    }
}