<?php

namespace Entities\Cards\Components\Vue\DigitalCardWidget\V2;

use App\Website\Vue\Classes\Base\VueComponent;

class DigitalCardPageWidget extends VueComponent
{
    protected $id = "5b959995-e3c9-48ec-9aa0-40874e7064a2";
    protected $title = "Digital Card Page";

    protected function renderComponentDataAssignments() : string
    {
        return "
            entityFound: false,
            page: null,
            pageContent: '',
            pageScripts: [],
        ";
    }

    protected function renderComponentHydrationScript() : string
    {
        return '
            this.page = props.cardPage;
            this.insertAndExecute();
        ';
    }

    protected function renderComponentMethods() : string
    {
        return '
            renderPageContent: function(content)
            {
                try {
                    return atob(content);
                }
                catch(ex)
                {
                    console.log("base64 conversion error");
                    return "Error converting string.";
                }
            },
            insertAndExecute: function() 
            {
                if (typeof this.page === "undefined" || this.page.content === null) return;
                this.pageContent = this.renderPageContent(this.page.content);
                
                if (this.pageScripts[this.page.card_tab_rel_id] === true)
                {
                    return;
                }
                
                this.executeScripts();
                this.pageScripts[this.page.card_tab_rel_id] = true;
            },
            executeScripts: function()
            {
                let errorNode = createNode("div", [".page-content-scripts"], this.pageContent);
                let scripts = Array.prototype.slice.call(errorNode.getElementsByTagName("script"));
                
                for (var i = 0; i < scripts.length; i++) 
                {
                    if (scripts[i].src != "") 
                    {
                        var tag = document.createElement("script");
                        tag.src = scripts[i].src;
                        document.getElementsByTagName("head")[0].appendChild(tag);
                    }
                    else 
                    {
                        eval(scripts[i].innerHTML);
                    }
                }
            },
        ';
    }

    protected function renderTemplate(): string
    {
        return '
            <div v-if="page != null" class="app-page">
                <div class="app-page-title" v-on:click="backToComponent()">
                    <a v-show="hasParent" class="back-to-entity-list pointer"></a>
                    <span>{{ page.title }}</span>
                </div>
                <div class="app-page-content">
                    <div class="app-page-content-inner" v-html="pageContent"></div>
                </div>
            </div>
                ';
    }
}