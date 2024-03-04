<?php

namespace Entities\Testimonials\Classes;

use App\Core\AppEntity;
use Entities\Testimonials\Models\TestimonialModel;

class Testimonials extends AppEntity
{
    public string $strEntityName       = "testimonials";
    public $strDatabaseTable    = "testimonial";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = TestimonialModel::class;
    public $strMainModelPrimary = "testimonial_id";
    public $isPrimaryModule     = true;
}
