<?php

namespace Entities\Contacts\Components\Vue\ContactWidget;

class MyListContactsWidget extends ListContactsWidget
{
    protected $id = "fca452a9-1dfd-45d5-9263-d1daeb8b41ef";
    protected $title = "My Contacts";

    protected function renderComponentHydrationScript() : string
    {
        global $app;
        return parent::renderComponentHydrationScript() . '
            this.filterEntityId = '.$app->getActiveLoggedInUser()->user_id.'
        ';
    }
}