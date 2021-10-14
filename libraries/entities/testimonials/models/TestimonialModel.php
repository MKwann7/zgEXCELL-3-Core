<?php

namespace Entities\Testimonials\Models;

use App\Core\AppModel;

class TestimonialModel extends AppModel
{
    protected $EntityName = "Testimonials";
    protected $ModelName = "Testimonial";

    public function __construct($entityData = null, $force = false)
    {
        $this->Definitions = $this->loadDefinitions();
        parent::__construct($entityData, $force);
    }

    private function loadDefinitions()
    {
        return [];
    }
}