<?php

namespace Entities\Contacts\Components\Vue;

use App\Website\Vue\Classes\VueModal;
use Entities\Contacts\Components\Vue\ContactWidget\MyListContactsWidget;

class MyContactsMainApp extends ContactsMainApp
{
    protected string $appNamePlural = "My Contacts";
    protected string $appNameSingular = "My Contact";

    public function __construct($domId, ?VueModal &$modal = null)
    {
        parent::__construct($domId, $modal);

        $this->setDefaultComponentId(MyListContactsWidget::getStaticId())->setDefaultComponentAction("view");


    }
}