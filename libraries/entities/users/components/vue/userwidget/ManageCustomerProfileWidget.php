<?php

namespace Entities\Users\Components\Vue\UserWidget;

class ManageCustomerProfileWidget extends ManageUserProfileWidget
{
    protected $id = "293e2fa8-f6cc-4d28-bd89-bc1f55a0289e";
    protected $title              = "Create Customer";
    protected $saveNewButtonTitle = "Save New Customer";
    protected $updateButtonTitle = "Update Customer";
    protected $assignUserType = false;
    protected $assignUserRoles = false;

    public function __construct(array $components = [])
    {
        parent::__construct($components);

        $this->modalTitleForAddEntity = "Create Customer";
        $this->modalTitleForEditEntity = "Modify Customer";
        $this->modalTitleForDeleteEntity = "Delete Customer";
        $this->modalTitleForRowEntity = "View Customer";
        $this->setDefaultAction("view");
    }
}