<?php

namespace Entities\Division\Classes\Base;

use App\Core\AppController;
use Entities\Division\Classes\Division;

class DivisionController extends AppController
{
    public function __construct($app)
    {
        $this->AppEntity = new Division();
        parent::__construct($app);
    }
}