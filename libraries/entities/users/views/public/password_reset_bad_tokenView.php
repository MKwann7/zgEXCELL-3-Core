<?php

use App\Core\App;

$this->CurrentPage->BodyId            = "password-reset-page";
$this->CurrentPage->BodyClasses       = ["page", "password-reset-page", "no-columns"];
$this->CurrentPage->Meta->Title       = "Reset Your Account Password | " . $this->app->objCustomPlatform->getPortalDomain();
$this->CurrentPage->Meta->Description = "Lose your password? No worries. We have you coverted.";
$this->CurrentPage->Meta->Keywords    = "Password Reset, Account Credentials Update";
$this->CurrentPage->SnipIt->Title     = "Password Reset";
$this->CurrentPage->SnipIt->Excerpt   = "Lose your password? No worries. We have you coverted.";
$this->CurrentPage->Columns           = 0;

/** @var App $this->app */
?>
<div class="wrapper loggedOutBody">
    <div class="content">
        <h1 class="page-title-main">Reset Your Password</h1>

        <div class="space10"></div>
        <h2>Woops.</h2>
        <hr>
        <p style="text-align:center;">Looks like we are missing an active reset token.</p>
        <hr>
        <h3>Need to Reset your Password?</h3>
        <p>You can try <a href="<?php getFullUrl(); ?>/login" >resetting your password</a> again to get a new one via your primary <?php echo $this->app->objCustomPlatform->getPortalName(); ?> account email.</p>
    </div>

</div>

