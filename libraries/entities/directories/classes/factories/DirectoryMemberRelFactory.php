<?php

namespace Entities\Directories\Classes\Factories;

use App\Core\Abstracts\AbstractFactory;
use App\Core\App;
use Entities\Directories\Classes\Directories;
use Entities\Directories\Classes\DirectoryMemberRels;
use Entities\Users\Models\UserModel;

class DirectoryMemberRelFactory extends AbstractFactory
{
    private App $app;
    private Directories $directories;
    private DirectoryMemberRels $memberRels;
    private bool $success = false;
    private array $errors = [];
    private string $message = "";

    public function __construct(App $app, Directories $directories, DirectoryMemberRels $memberRels)
    {
        $this->app = $app;
        $this->directories = $directories;
        $this->memberRels = $memberRels;
    }

    public function renderUserReturnArray(UserModel $user, string $email, string $phone) : array
    {
        $arUser = $user->ToPublicArray(["sys_row_id", "user_id", "first_name", "last_name", "user_email", "user_phone", "username"]);
        $arUser["id"] = $arUser["sys_row_id"];
        $arUser["email"] = $email;
        $arUser["phone"] = $phone;
        unset($arUser["sys_row_id"]);

        return $arUser;
    }
}