<?php

namespace Entities\Cards\Classes\Factories;

use App\Core\App;

class CardPurchaseFactory
{
    private App $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }
}