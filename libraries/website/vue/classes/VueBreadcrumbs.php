<?php

namespace App\Website\Vue\Classes;

use App\Website\Vue\Classes\Base\VueComponent;
use Entities\Cart\Components\Vue\CartWidget\CartWidget;

class VueBreadcrumbs extends VueComponent
{
    protected string $name = "compBC";
    protected string $vueType = "compBC";

    public function __construct()
    {
        parent::__construct();
    }

    protected function renderComponentMethods() : string
    {
        return '
            openCart: function()
            {   
                if (appCart === null || typeof appCart.openCart === "undefined") { return; }
                appCart.openCart({className: false});
            },
            ';
    }

    protected function renderTemplate() : string
    {
        return '
            <div class="breadCrumbs">
                <div class="breadCrumbsInner">
                    <a href="/account" class="breadCrumbHomeImageLink">
                        <img src="/media/images/home-icon-01_white.png" class="breadCrumbHomeImage" width="15" height="15" />
                    </a> &#187;
                    <a href="/account" class="breadCrumbHomeImageLink">
                        <span class="breadCrumbPage">Home</span>
                    </a> &#187;
                    <span class="breadCrumbPage">Profile</span>
                    <ul class="breadcrumb-right-menu-list">
                        <li class="pointer">
                            <img v-on:click="openCart" src="/_ez/images/financials/cart-icon-white.png" class="pointer" />
                        </li>
                    </ul>
                </div>
            </div>
        ';
    }
}