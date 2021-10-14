<?php

namespace Entities\Posts\Classes\Base;

use App\Core\AppController;
use Entities\Posts\Classes\Posts;

class PostController extends AppController
{
    public function __construct($app)
    {
        $this->AppEntity = new Posts();
        parent::__construct($app);
    }
}