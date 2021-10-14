<?php

use Entities\Users\Classes\Users;

if ($strViewTitle === "addAffiliate" || $strViewTitle === "editAffiliate")
{
    $strButtonText = "Add New Member";

    $intAffiliateRandId = time();

    if (!empty($this->app->objHttpRequest->Data->PostData->user_id))
    {
        $intUserId = $this->app->objHttpRequest->Data->PostData->user_id;
        $objUserResult = (new Users())->getById($intUserId);

        if ( $objUserResult->Result->Success === false)
        {
            die("Error: No user was found for id: $intUserId.");
        }

        $objUser = $objUserResult->Data->First();

        $strButtonText = "Edit Member";
    }

    $intUsernameRandId = time();

    ?>
    <form id= "<?php echo $strViewTitle; ?>Form" action="/affiliates/user-data/create-affiliate-data?type=profile<?php echo  !empty($intUserId) ? '&user_id='.$intUserId : ''; ?>" method="post">
        <div style="background:#ddd;padding: 0px 8px 0px;border-radius:5px;box-shadow:rgba(0,0,0,.2) 0 0 10px inset;">
            <table class="table" style="margin-bottom: 5px; margin-top:10px;">
                <tr>
                    <td style="width:100px;vertical-align: middle;">Customer</td>
                    <td>
                        <input autocomplete="off" id="ca_<?php echo $intAffiliateRandId; ?>" placeholder="Start Typing..." class="form-control">
                        <input name="sponsor_id" id="ca_<?php echo $intAffiliateRandId; ?>_id" value="" type="hidden">
                    </td>
                </tr>
            </table>
        </div>
        <button class="btn btn-primary w-100"><?php echo $strButtonText; ?></button>
    </form>
    <script type="text/javascript">
        $( function() {
            $(document).on("blur", "#username_<?php echo $intUsernameRandId; ?>",function(){
                let strUserName = $(this).val();
                if ( strUserName == "") { $("#username_<?php echo $intUsernameRandId; ?>").removeClass("error-validation").removeClass("pass-validation"); $(".username-error").remove(); return; }
                ajax.Send("api/v1/users/check-user-username?username=" + strUserName + "&user_id=<?php echo $intUserId; ?>", null, function(objResult) {
                    switch(objResult.match) {
                        case true:
                            $("#username_<?php echo $intUsernameRandId; ?>").addClass("error-validation").removeClass("pass-validation");
                            if( $(".username-error").length == 0) {
                                $("#username_<?php echo $intUsernameRandId; ?>").after('<div class="error-text username-error">This Username Already Exists</div>');
                            }
                            break;
                        default:
                            $("#username_<?php echo $intUsernameRandId; ?>").removeClass("error-validation").addClass("pass-validation");
                            $(".username-error").remove();
                            break;
                    }
                });
            });
            app.Search("api/v1/users/get-users", "#ca_<?php echo $intAffiliateRandId; ?>", "users", ["user_id", "first_name", "last_name"], ["user_id", "first_name.last_name"]);
        });
    </script>
<?php } ?>
