<?php

namespace App\Website\Vue\Classes\Breadcrumbs;

use App\Website\Vue\Classes\VueBreadcrumbs;

class VueBreadcrumbsVer1 extends VueBreadcrumbs
{
    protected string $id = "946baf79-46bc-43f6-ab97-0ec85f0c0881";

    protected function renderComponentDataAssignments() : string
    {
        return '
                breadcrumbs: null,
                subPages: null,
        ';
    }

    protected function renderComponentMethods() : string
    {
        return '
            updateBreadCrumb: function(breadcrumbData)
            {
                this.breadcrumbs = breadcrumbData;
            },
            updateSubPageLinks: function(subPageData)
            {
                this.subPages = subPageData;
            },
            openMobileMenu: function()
            {
                alert("here!");
            },
            ';
    }

    protected function renderTemplate() : string
    {
        return '
            <div class="breadCrumbs">
                <ul class="breadCrumbsInner">
                    <li v-for="(breadcrumb, index) in breadcrumbs" v-bind:class="{labelBreadcrumb: breadcrumb.linkType == \'link\'}">
                        <a v-if="breadcrumb.linkType == \'link\'" v-bind:href="breadcrumb.linkHref">{{ breadcrumb.linkLabel }}</a>
                        <span v-if="breadcrumb.linkType == \'title\'">{{ breadcrumb.linkLabel }}</span>
                    </li>
                    <li v-bind:class="{activeSubPage: subpage.active == \'true\'}" class="sub-page-link" v-for="(subpage, index) in subPages">
                        <a v-bind:href="subpage.linkHref">{{ subpage.linkLabel }}</a>
                    </li>
                </ul>
            </div>
        ';
    }
}