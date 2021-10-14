<?php

namespace Entities\Forms\Classes\Base;

use App\Core\AppController;
use Entities\Forms\Classes\Forms;

class FormController extends AppController
{
    public function __construct($app)
    {
        $this->AppEntity = new Forms();
        parent::__construct($app);
    }
}