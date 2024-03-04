<?php

$this->CurrentPage->BodyId = "password-reset-page";
$this->CurrentPage->BodyClasses       = ["page", "password-reset-page", "no-columns"];
$this->CurrentPage->Meta->Title       = "Reset Your Account Password | " . $this->app->objCustomPlatform->getPortalDomainName();
$this->CurrentPage->Meta->Description = "Lose your password? No worries. We have you coverted.";
$this->CurrentPage->Meta->Keywords    = "Password Reset, Account Credentials Update";
$this->CurrentPage->SnipIt->Title     = "Password Reset";
$this->CurrentPage->SnipIt->Excerpt   = "Lose your password? No worries. We have you coverted.";
$this->CurrentPage->Columns           = 0;

$intRandomIdSuffix = rand(1000,9999);

?>
<div class="wrapper loggedOutBody">
    <div class="content">
        <div class="space10"></div>
        <div class="loginMainfloat">
            <div class="aboutUsSectionTitleText" style="color:#000;margin-bottom: 12px; text-align: center;">
                Reset Your Password
            </div>
            <div class="siteLoginFloat">
                <fieldset class="fieldsetGrouping siteCrmLogin">
                    <form id="frmPasswordReset" method="post" action="/api/v1/users/reset-password-from-site" novalidate="novalidate" _lpchecked="1">
                        <input name="__RequestVerificationToken" type="hidden" value="ylLQGWa6w_wFQJQD0AaB5iWCmVk2YmQpKdaD0TeCrHPKJ7QbxKC9SJOH8jevUICVu6UTLLOpsmNyGSKkPVi6ev9HTcQdyI1D6gxuO-lO9X01">
                        <input name="username" type="hidden" value="<?php echo $user->username; ?>" />
                        <div class="login-field-table">
                            <input type="hidden" name="request_token" value="<?php echo $resetPasswordToken; ?>" />
                            <div class="login-field-row">
                                <div class="editor-label">
                                    <label for="Username">New Password</label>
                                </div>
                                <div class="editor-field">
                                    <input id="password_for_reset_<?php echo $intRandomIdSuffix; ?>" autocomplete="new-password" name="password_for_reset" type="password" value="" style="background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGP6zwAAAgcBApocMXEAAAAASUVORK5CYII=&quot;); cursor: auto;">
                                    <span class="field-validation-valid" data-valmsg-for="password1"></span>
                                </div>
                                <div class="clear"></div>
                            </div>
                            <div class="login-field-row">
                                <div class="editor-label">
                                    <label for="Password">Re-enter It</label>
                                </div>
                                <div class="editor-field">
                                    <input id="password_for_reset_reenter_<?php echo $intRandomIdSuffix; ?>" autocomplete="new-password-confirm" name="password_for_reset_reenter" type="password" style="background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGP6zwAAAgcBApocMXEAAAAASUVORK5CYII=&quot;); cursor: auto;">
                                    <span class="field-validation-valid" data-valmsg-for="password2"></span>
                                </div>
                            </div>
                        </div>
                        <div class="clear editor-label login-button-box">
                            <button type="submit" id="loginbtn" value="Log In" class="form-submit-buttons css3button pointer">Set New Password</button>
                        </div>
                    </form>
                    <script type="application/javascript">

                        if (typeof ResetPassword !== "function")
                        {
                            function ResetPassword()
                            {
                                let _ = this;

                                this.load = function () {
                                    _.EngagePasswordResetForm();
                                }

                                this.EngagePasswordResetForm = function () {
                                    app.Form('frmPasswordReset', function() {
                                            modal.EngageFloatShield();
                                        },
                                        function (objAccountAuthResult)
                                        {
                                            console.log(objAccountAuthResult);
                                            if (objAccountAuthResult.success != true) {
                                                let data = {};
                                                data.title = "EZcard Password Reset Error...";
                                                data.html = "Looks like we were unable to update your password.<br>We've logged the error.<br><br><div style='text-align:center;'><b>Please contact us so we can reset it for you!</div></b>";
                                                modal.EngagePopUpAlert(data, function() {
                                                    modal.CloseFloatShield();
                                                }, 500, 115, true);

                                                return false;
                                            }

                                            setTimeout(function() {
                                                modal.EngageFloatShield();
                                                let data = {};
                                                data.title = "Password Reset Complete!";
                                                data.html = "Let's take you back to the login page so you can access your EZcard account.";
                                                modal.EngagePopUpAlert(data, function() {
                                                    app.GotoPage("login");
                                                }, 350, 115);
                                            },1000);
                                        },
                                        function()
                                        {
                                            let strPassword1 = $("#password_for_reset_<?php echo $intRandomIdSuffix; ?>").val();
                                            let strPassword2 = $("#password_for_reset_reenter_<?php echo $intRandomIdSuffix; ?>").val();

                                            let intErrors = 0;

                                            if ($("#frmPasswordReset .error-text").length > 0)
                                            {
                                                return false;
                                            }

                                            $("#frmPasswordReset .error-validation").removeClass("error-validation");


                                            if ( strPassword1 == "" )
                                            {
                                                intErrors++;
                                                $("#password_for_reset_<?php echo $intRandomIdSuffix; ?>").addClass("error-validation").blur(function() {
                                                    $(this).removeClass("error-validation");
                                                    $(".password-retype").remove();
                                                });
                                                $("#password_for_reset_<?php echo $intRandomIdSuffix; ?>").after('<div class="error-text password-retype">Woops. You forgot to enter a password.</div>');
                                            }

                                            if (strPassword1 != strPassword2)
                                            {
                                                intErrors++;
                                                $("#password_for_reset_reenter_<?php echo $intRandomIdSuffix; ?>").addClass("error-validation").blur(function() {
                                                    $(this).removeClass("error-validation");
                                                    $(".password-retype").remove();
                                                });
                                                $("#password_for_reset_reenter_<?php echo $intRandomIdSuffix; ?>").after('<div class="error-text password-retype">Sorry! Your passwords do not match.</div>');
                                            }

                                            if (strPassword1.includes("--") || strPassword1.includes("'") || strPassword1.includes('"') || strPassword1.includes('`') || strPassword1.includes(' '))
                                            {
                                                intErrors++;
                                                $("#password_for_reset_reenter_<?php echo $intRandomIdSuffix; ?>").addClass("error-validation").blur(function() {
                                                    $(this).removeClass("error-validation");
                                                    $(".password-retype").remove();
                                                });
                                                $("#password_for_reset_reenter_<?php echo $intRandomIdSuffix; ?>").after('<div class="error-text password-retype">Sorry! Your password contains illegal character(s) for our system. e.g., --, ", `, etc.</div>');
                                            }

                                            if (intErrors > 0)
                                            {
                                                return false;
                                            }

                                            return true;
                                        });

                                    $('#frmPasswordReset input').bind("keypress.key13", function(event) {
                                        let objLoginForm = $('#frmLogin');
                                        let eventKey = event.charCode || event.keyCode;
                                        if (eventKey == 13) {
                                            if (objLoginForm.hasClass("confirmed_click")) { return false; }
                                            objLoginForm.addClass("confirmed_click");
                                            objLoginForm.unbind("keypress.key13");
                                            objLoginForm.removeClass("confirmed_click");
                                            $("#loginbtn").click();
                                        }
                                    });
                                }
                            }
                        }

                        let resetPassword = new ResetPassword();

                        $(document).ready(function () {
                            resetPassword.load();
                        });
                    </script>
                    <style type="text/css">
                        #frmPasswordReset .login-field-table .login-field-row > div:first-child {
                            width: 115px;
                        }
                        #frmPasswordReset .error-validation {
                            border:2px solid #ff0000 !important;
                            box-shadow: #ff0000 0 0 5px !important;
                        }
                    </style>
                </fieldset>
            </div>
        </div>
    </div>

</div>

