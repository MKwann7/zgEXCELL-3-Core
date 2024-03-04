<?php

namespace Entities\Users\Components\Vue\UserWidget;

class ManageCustomerProfileWidget extends ManageUserProfileWidget
{
    protected string $id = "293e2fa8-f6cc-4d28-bd89-bc1f55a0289e";
    protected string $title              = "Create Customer";
    protected string $saveNewButtonTitle = "Save New Customer";
    protected string $updateButtonTitle = "Update Customer";
    protected bool $assignUserType = false;
    protected bool $assignUserRoles = false;
    protected bool $enableOriginator = false;
    protected bool $enableAccountEditing = false;

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