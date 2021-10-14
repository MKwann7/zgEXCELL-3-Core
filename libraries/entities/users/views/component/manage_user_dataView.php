<?php
/**
 * Created by PhpStorm.
 * User: Micah.Zak
 * Date: 10/16/2018
 * Time: 11:29 AM
 */

use App\Core\App;
use Entities\Media\Classes\Images;
use Entities\Users\Classes\Connections;
use Entities\Users\Classes\Users;

/** @var App $app */

?>
<?php

if ($strViewTitle === "addCustomer" || $strViewTitle === "editCustomer")
{
    $strButtonText = "Add New Customer";

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

        $strButtonText = "Edit Customer";
    }

    $intUsernameRandId = time();

    ?>
    <form id= "<?php echo $strViewTitle; ?>Form" action="/customers/user-data/create-user-data?type=profile<?php echo  !empty($intUserId) ? '&user_id='.$intUserId : ''; ?>" method="post">
        <div style="background:#ddd;padding: 0px 8px 0px;border-radius:5px;box-shadow:rgba(0,0,0,.2) 0 0 10px inset;">
            <table class="table" style="margin-bottom: 5px; margin-top:10px;">
                <tr>
                    <td style="width:100px;vertical-align: middle;">Affiliate</td>
                    <td>
                        <input autocomplete="off" id="ca_<?php echo $intAffiliateRandId; ?>" value="<?php echo $strAffiliateName; ?>" placeholder="Start Typing..." class="form-control">
                        <input id="ca_<?php echo $intAffiliateRandId; ?>_id" name="card_affiliate" value="<?php echo $intAffiliateId; ?>" type="hidden">
                    </td>
                </tr>
            </table>
        </div>
        <table class="table no-top-border">
            <tr>
                <td style="width:100px;vertical-align: middle;">First Name</td>
                <td><input name="first_name" class="form-control" type="text" placeholder="Enter First Name..." value="<?php echo $objUser->first_name ?? ''; ?>"/></td>
            </tr>
            <tr>
                <td style="width:100px;vertical-align: middle;">Last Name</td>
                <td><input name="last_name" class="form-control" type="text" placeholder="Enter Last Name..." value="<?php echo $objUser->last_name ?? ''; ?>"/></td>
            </tr>
            <?php if ($strButtonText === "Add New Customer") { ?>
            </table>
            <div style="background:#ddd;padding: 0px 8px 0px;border-radius:5px;box-shadow:rgba(0,0,0,.2) 0 0 10px inset;margin-top:-10px;margin-bottom:10px;">
                <table class="table" style="margin-bottom: 5px; margin-top:10px;">
                    <tr>
                        <td style="width:100px;vertical-align: middle;">Phone</td>
                        <td><input name="primary_phone" class="form-control" type="text" placeholder="Enter Primary Phone..." value=""/></td>
                    </tr>
                    <tr>
                        <td style="width:100px;vertical-align: middle;">Email</td>
                        <td><input name="primary_email" class="form-control" type="text" placeholder="Enter Primary Email..." value=""/></td>
                    </tr>
                </table>
            </div>
            <table class="table no-top-border">
            <?php } else { ?>
            </table>
            <div style="background:#ddd;padding: 0px 8px 0px;border-radius:5px;box-shadow:rgba(0,0,0,.2) 0 0 10px inset;margin-top:-10px;margin-bottom:10px;">
                <table class="table" style="margin-bottom: 5px; margin-top:10px;">
                    <tr>
                        <td style="width:100px;vertical-align: middle;">Phone</td>
                        <td><input name="primary_phone" class="form-control" type="text" placeholder="Enter Primary Phone..." value=""/></td>
                    </tr>
                    <tr>
                        <td style="width:100px;vertical-align: middle;">Email</td>
                        <td><input name="primary_email" class="form-control" type="text" placeholder="Enter Primary Email..." value=""/></td>
                    </tr>
                </table>
            </div>
            <table class="table no-top-border">
            <?php } ?>
            <tr>
                <td style="width:100px;vertical-align: middle;">Username</td>
                <td><input name="username" id="username_<?php echo $intUsernameRandId; ?>" class="form-control" type="text" placeholder="Enter User Name..." value="<?php echo $objUser->username ?? ''; ?>"/></td>
            </tr>
            <tr>
                <td style="width:100px;vertical-align: middle;">Password</td>
                <td><input name="password" class="form-control" type="text" placeholder="Enter New Password To Update Current..."></td>
            </tr>
            <tr>
                <td style="width:100px;vertical-align: middle;">Status</td>
                <td>
                    <select name="status" class="form-control">
                        <option value="Pending" <?php if ($strViewTitle === "editCustomer") { echo returnSelectedIfValuesMatch($objUser->status ?? '', "Pending"); } ?>>Pending</option>
                        <option value="Active" <?php echo returnSelectedIfValuesMatch($objUser->status ?? 'Active', "Active"); ?>>Active</option>
                        <option value="Inactive" <?php if ($strViewTitle === "editCustomer") { echo returnSelectedIfValuesMatch($objUser->status ?? '', "Inactive"); } ?>>Inactive</option>
                        <option value="Cancelled" <?php if ($strViewTitle === "editCustomer") { echo returnSelectedIfValuesMatch($objUser->status ?? '', "Cancelled"); } ?>>Cancelled</option>
                        <option value="Disabled" <?php if ($strViewTitle === "editCustomer") { echo returnSelectedIfValuesMatch($objUser->status ?? '', "Disabled"); } ?>>Disabled</option>
                    </select>
                </td>
            </tr>
        </table>
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
<?php

if ($strViewTitle === "editProfile" || $strViewTitle === "editCustomerProfile")
{
    if (empty($this->app->objHttpRequest->Data->PostData->user_id))
    {
        die("Error: You must supply a user id to this controller.");
    }

    $intUserId = $this->app->objHttpRequest->Data->PostData->user_id;
    $objUserResult = (new Users())->getById($intUserId);

    if ( $objUserResult->Result->Success === false)
    {
        die("Error: No user was found for id: $intUserId.");
    }

    $intUsernameRandId = time();
    $intAffiliateRandId = time();

    $objUser = $objUserResult->Data->First();

    $colUserPhoneConnections = (new Connections())->getFks()->getWhere([["user_id" => $objUser->user_id], "AND", [["connection_type_id" => 1], "OR", ["connection_type_id" => 3]]])->Data;
    $colUserEmailConnections = (new Connections())->getFks()->getWhere(["user_id" => $objUser->user_id, "connection_type_id" => 6])->Data;
    $colConnectionTypeResult = (new Users())->GetUserConnectionTypes();
    $colConnectionType = $colConnectionTypeResult->Data;

    $objUserAffiliateResult = (new Users())->GetAffiliateByUserId($objUser->user_id);
    $intAffiliateId = $objUserAffiliateResult->Result->Count > 0 ? $objUserAffiliateResult->Data->First()->user_id : "";

    $strAffiliateName = $objUserAffiliateResult->Result->Count > 0 ? $objUserAffiliateResult->Data->First()->first_name . " " . $objUserAffiliateResult->Data->First()->last_name : "";
    ?>
    <form id= "<?php echo $strViewTitle; ?>Form" action="/customers/user-data/update-user-data?type=profileAdmin&id=<?php echo $intUserId; ?>" method="post" autocomplete="off">
        <div style="background:#ddd;padding: 0px 8px 0px;border-radius:5px;box-shadow:rgba(0,0,0,.2) 0 0 10px inset;">
            <table class="table" style="margin-bottom: 5px; margin-top:10px;">
                <tr>
                    <td style="width:100px;vertical-align: middle;">Affiliate</td>
                    <td>
                        <input class="affiliate_name_for_user form-control" autocomplete="off" id="ca_<?php echo $intAffiliateRandId; ?>" value="<?php echo $strAffiliateName; ?>" placeholder="Start Typing...">
                        <input id="ca_<?php echo $intAffiliateRandId; ?>_id" name="card_affiliate" value="<?php echo $intAffiliateId; ?>" type="hidden">
                    </td>
                </tr>
            </table>
        </div>

        <table class="table no-top-border">
            <tr>
                <td style="width:100px;vertical-align: middle;">First Name</td>
                <td><input name="first_name" class="form-control" type="text" placeholder="Enter First Name..." value="<?php echo $objUser->first_name; ?>"/></td>
            </tr>
            <tr>
                <td style="width:100px;vertical-align: middle;">Last Name</td>
                <td><input name="last_name" class="form-control" type="text" placeholder="Enter Last Name..." value="<?php echo $objUser->last_name; ?>"/></td>
            </tr>
        </table>
        <div style="background:#ddd;padding: 0px 8px 0px;border-radius:5px;box-shadow:rgba(0,0,0,.2) 0 0 10px inset;margin-top:-10px;margin-bottom:10px;">
            <table class="table" style="margin-bottom: 5px; margin-top:10px;">
                <tr>
                    <td style="width:100px;vertical-align: middle;">Phone</td>
                    <td><input id="primary_p_<?php echo $intUsernameRandId; ?>" autocomplete="nope-<?php echo time(); ?>" name="user_phone" class="form-control" type="text" placeholder="Enter Primary Phone..." value="<?php echo $objUser->user_phone; ?>"/></td>
                </tr>
                <tr>
                    <td style="width:100px;vertical-align: middle;">Email</td>
                    <td><input id="primary_e_<?php echo $intUsernameRandId; ?>" autocomplete="nope-<?php echo time(); ?>" name="user_email" class="form-control" type="text" placeholder="Enter Primary Email..." value="<?php echo $objUser->user_email; ?>"/></td>
                </tr>
            </table>
        </div>
        <table class="table no-top-border">
            <tr>
                <td style="width:100px;vertical-align: middle;">User Name</td>
                <td><input id="username_<?php echo $intUsernameRandId; ?>" autocomplete="false" name="username" class="form-control pass-validation" type="text" placeholder="Enter User Name..." value="<?php echo $objUser->username; ?>"/></td>
            </tr>
            <tr>
                <td style="width:100px;vertical-align: middle;">Password</td>
                <td><input name="password"  class="form-control" type="text" placeholder="Enter New Password To Update Current..."></td>
            </tr>
            <?php if ($strViewTitle !== "editCustomerProfile") { ?>
            <tr>
                <td style="width:100px;vertical-align: middle;">Status</td>
                <td>
                    <select name="status" class="form-control">
                        <option value="Pending" <?php if ($strViewTitle === "editProfile") { echo returnSelectedIfValuesMatch($objUser->status, "Pending"); } ?>>Pending</option>
                        <option value="Active" <?php if ($strViewTitle === "editProfile") { echo returnSelectedIfValuesMatch($objUser->status, "Active"); } ?>>Active</option>
                        <option value="Inactive" <?php if ($strViewTitle === "editProfile") { echo returnSelectedIfValuesMatch($objUser->status, "Inactive"); } ?>>Inactive</option>
                        <option value="Cancelled" <?php if ($strViewTitle === "editProfile") { echo returnSelectedIfValuesMatch($objUser->status, "Cancelled"); } ?>>Cancelled</option>
                        <option value="Disabled" <?php if ($strViewTitle === "editProfile") { echo returnSelectedIfValuesMatch($objUser->status, "Disabled"); } ?>>Disabled</option>
                    </select>
                </td>
            </tr>
            <?php } ?>
        </table>
        <button class="btn btn-primary w-100">Update Profile</button>
    </form>
    <script type="text/javascript">
        $( function() {
            app.Search("api/v1/users/get-affiliates","#ca_<?php echo $intAffiliateRandId; ?>", "users", ["user_id","first_name","last_name"],["user_id","first_name.last_name"]);
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
                            $("#username_<?php echo $intVanityRandId; ?>").removeClass("error-validation").addClass("pass-validation");
                            $(".username-error").remove();
                            break;
                    }
                });
            });
            $('#primary_p_<?php echo $intUsernameRandId; ?>').inputpicker({
                data:[
                    <?php foreach($colUserPhoneConnections as $currConnection) {
                    $currConnectionType = $colConnectionType->FindEntityByValue("name", $currConnection->connection_type_id);
                    ?>
                    {id:"<?php echo $currConnection->connection_id; ?>", action: "<?php echo $currConnectionType->action; ?>", value:"<?php echo formatAsPhoneIfApplicable($currConnection->connection_value); ?>", type: "<?php echo $currConnection->connection_type_id; ?>"},
                    <?php } ?>
                ],
                fields:[
                    {name:'type',text:'Type'},
                    {name:'action',text:'Action'},
                    {name:'value',text:'Value'}
                ],
                autoOpen: true,
                headShow: true,
                fieldText : 'value',
                filterOpen: true,
                fieldValue: 'id'
            });
            $('#primary_e_<?php echo $intUsernameRandId; ?>').inputpicker({
                data:[
                    <?php foreach($colUserEmailConnections as $currConnection) {
                    $currConnectionType = $colConnectionType->FindEntityByValue("name", $currConnection->connection_type_id);
                    ?>
                    {id:"<?php echo $currConnection->connection_id; ?>", action: "<?php echo $currConnectionType->action; ?>", value:"<?php echo formatAsPhoneIfApplicable($currConnection->connection_value); ?>", type: "<?php echo $currConnection->connection_type_id; ?>"},
                    <?php } ?>
                ],
                fields:[
                    {name:'type',text:'Type'},
                    {name:'action',text:'Action'},
                    {name:'value',text:'Value'}
                ],
                autoOpen: true,
                headShow: true,
                fieldText : 'value',
                filterOpen: true,
                fieldValue: 'id'
            });
        });
    </script>
<?php } ?>
<?php

if ($strViewTitle === "editAccount")
{
    if (empty($this->app->objHttpRequest->Data->PostData->user_id))
    {
        die("Error: You must supply a user id to this controller.");
    }

    $intUserId = $this->app->objHttpRequest->Data->PostData->user_id;
    $objUserResult = (new Users())->getById($intUserId);

    if ( $objUserResult->Result->Success === false)
    {
        die("Error: No user was found for id: $intUserId.");
    }

    $objUser = $objUserResult->Data->First();
    ?>
    <form id= "<?php echo $strViewTitle; ?>Form" action="/customers/user-data/update-user-data?type=account&id=<?php echo $intUserId; ?>" method="post">
        <table class="table no-top-border">
            <tr>
                <td style="width:100px;vertical-align: middle;">User Name</td>
                <td><input name="username" class="form-control" type="text" placeholder="Enter User Name..." value="<?php echo $objUser->username; ?>"/></td>
            </tr>
            <tr>
                <td style="width:100px;vertical-align: middle;">Password</td>
                <td><input name="password"  class="form-control" type="text" placeholder="Enter New Password To Update Current..."></td>
            </tr>
            <tr>
                <td style="width:100px;vertical-align: middle;">Status</td>
                <td>
                    <select name="status" class="form-control">
                        <option value="Pending" <?php if ($strViewTitle === "editAccount") { echo returnSelectedIfValuesMatch($objUser->status, "Pending"); } ?>>Pending</option>
                        <option value="Active" <?php if ($strViewTitle === "editAccount") { echo returnSelectedIfValuesMatch($objUser->status, "Active"); } ?>>Active</option>
                        <option value="Inactive" <?php if ($strViewTitle === "editAccount") { echo returnSelectedIfValuesMatch($objUser->status, "Inactive"); } ?>>Inactive</option>
                        <option value="Cancelled" <?php if ($strViewTitle === "editAccount") { echo returnSelectedIfValuesMatch($objUser->status, "Cancelled"); } ?>>Cancelled</option>
                        <option value="Disabled" <?php if ($strViewTitle === "editAccount") { echo returnSelectedIfValuesMatch($objUser->status, "Disabled"); } ?>>Disabled</option>
                    </select>
                </td>
            </tr>
        </table>
        <button class="btn btn-primary w-100">Update Account</button>
    </form>
<?php } ?>
<?php
if ($strViewTitle === "addConnection" || $strViewTitle === "editConnection")
{
    $intUserConnectionId = "new";
    $intUserId = "";
    $strSelectedConnectionName = "";
    $objConnection = null;
    $lstConnectionsResult = (new Users())->GetUserConnectionTypes();
    $lstConnections = $lstConnectionsResult->Data;
    $strButtonText = "Add Connection";
    $strUserIdField = PHP_EOL;

    if ($strViewTitle === "editConnection")
    {
        if (empty($this->app->objHttpRequest->Data->PostData->connection_id))
        {
            die("Error: You must supply a user connection id to this controller.");
        }

        $intUserConnectionId = $this->app->objHttpRequest->Data->PostData->connection_id;
        $objUserResult = (new Users())->GetConnectionById($intUserConnectionId);

        if ( $objUserResult->Result->Success === false)
        {
            die("Error: No connection was found for id: $intUserConnectionId.");
        }

        $objConnection = $objUserResult->Data->First();

        $strSelectedConnection = $lstConnections->FindEntityByValue("connection_type_id", $objConnection->connection_type_id);

        $strSelectedConnectionid = $strSelectedConnection->connection_type_id;
        $intUserId = $objConnection->user_id;

        $strButtonText = "Edit Connection";
    }

    if ($strViewTitle === "addConnection")
    {
        if (empty($this->app->objHttpRequest->Data->PostData->user_id))
        {
            die("Error: You must supply a user id to this controller.");
        }

        $intUserId = $this->app->objHttpRequest->Data->PostData->user_id;

        $strUserIdField = '<input type="hidden" id="'.$strViewTitle.'UserId" value="'.$intUserId.'" />'.PHP_EOL;
    }
?>
    <form id= "<?php echo $strViewTitle; ?>Form" action="/customers/user-data/update-user-data?type=connection&id=<?php echo $intUserId; ?>&connection_id=<?php echo $intUserConnectionId; ?>" method="post">
        <?php echo $strUserIdField; ?>
        <table class="table no-top-border">
            <tr>
                <td style="width:100px;vertical-align: middle;">Type</td>
                <td>
                    <select id="connection_type_id" name="connection_type_id" class="form-control">
                        <option value="">--Select Connection Type--</option>
                        <?php foreach($lstConnections as $currConnectionType) { ?>
                        <option value="<?php echo $currConnectionType->connection_type_id; ?>"<?php if ($strViewTitle === "editConnection") { echo returnSelectedIfValuesMatch($currConnectionType->connection_type_id, $strSelectedConnectionid); } ?>><?php echo $currConnectionType->name; ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td style="width:100px;vertical-align: middle;">Value</td>
                <td><input id="connection_value" name="connection_value" class="form-control" type="text" placeholder="Enter Connection Value..." value="<?php echo !empty($objConnection->connection_value) ? $objConnection->connection_value : ""; ?>"/></td>
            </tr>
        </table>
        <button class="btn btn-primary w-100"><?php echo $strButtonText; ?></button>
    </form>
<?php } ?>
<?php

if ($strViewTitle === "addAddress" || $strViewTitle === "editAddress" )
{
    $strButtonText = "Add Address";
    $strUserIdField = PHP_EOL;
    $intUserId = "";
    $intAddressId = "new";
    $objAddress = null;

    if ($strViewTitle === "editAddress")
    {
        if (empty($this->app->objHttpRequest->Data->PostData->address_id))
        {
            die("Error: You must supply an address id to this controller.");
        }

        $intAddressId = $this->app->objHttpRequest->Data->PostData->address_id;
        $objAddressResult = (new Users())->GetAddressById($intAddressId);

        if ( $objAddressResult->Result->Success === false)
        {
            die("Error: No address was found for id: $intAddressId.");
        }

        $objAddress = $objAddressResult->Data->First();
        $intAddressId = $objAddress->address_id;
        $intUserId = $objAddress->user_id;

        $strButtonText = "Edit Address";
    }

    if ($strViewTitle === "addAddress")
    {
        if (empty($this->app->objHttpRequest->Data->PostData->user_id))
        {
            die("Error: You must supply a user id to this controller.");
        }

        $intUserId = $this->app->objHttpRequest->Data->PostData->user_id;

        $strUserIdField = '<input type="hidden" id="'.$strViewTitle.'UserId" name="user_id" value="'.$intUserId.'" />'.PHP_EOL;
    }
    ?>
    <form id= "<?php echo $strViewTitle; ?>Form" action="/customers/user-data/update-user-data?type=address&address_id=<?php echo $intAddressId; ?>&id=<?php echo $intUserId; ?>" method="post">
        <?php echo $strUserIdField; ?>
        <table class="table no-top-border">
            <tr>
                <td style="width:100px;vertical-align: middle;">Address Name</td>
                <td><input name="display_name" class="form-control" type="text" placeholder="Enter Name..." value="<?php echo !empty($objAddress->display_name) ? $objAddress->display_name : ''; ?>"/></td>
            </tr>
            <tr>
                <td style="width:100px;vertical-align: middle;">Address Line 1</td>
                <td><input name="address_1" class="form-control" type="text" placeholder="Enter Address Line 1..." value="<?php echo !empty($objAddress->address_1) ? $objAddress->address_1 : ''; ?>"/></td>
            </tr>
            <tr>
                <td style="width:100px;vertical-align: middle;">Address Line 2</td>
                <td><input name="address_2" class="form-control" type="text" placeholder="Enter Address Line 2..." value="<?php echo !empty($objAddress->address_2) ? $objAddress->address_2 : ''; ?>"/></td>
            </tr>
            <tr>
                <td style="width:100px;vertical-align: middle;">City</td>
                <td><input name="city" class="form-control" type="text" placeholder="Enter Address City..." value="<?php echo !empty($objAddress->city) ? $objAddress->city : ''; ?>"/></td>
            </tr>
            <tr>
                <td style="width:100px;vertical-align: middle;">State</td>
                <td><input name="state" class="form-control" type="text" placeholder="Enter Address State..." value="<?php echo !empty($objAddress->state) ? $objAddress->state : ''; ?>"/></td>
            </tr>
            <tr>
                <td style="width:100px;vertical-align: middle;">Zip</td>
                <td><input name="zip" class="form-control" type="text" placeholder="Enter Address Zip..." value="<?php echo !empty($objAddress->zip) ? $objAddress->zip : ''; ?>"/></td>
            </tr>
            <tr>
                <td style="width:100px;vertical-align: middle;">Country</td>
                <td>
                    <select name="country" class="form-control">
                        <option value="USA" <?php if ($strViewTitle === "editAddress") { echo returnSelectedIfValuesMatch($objAddress->country, "USA"); } ?>>USA</option>
                    </select>
                </td>
            </tr>
        </table>
        <button class="btn btn-primary w-100"><?php echo $strButtonText; ?></button>
    </form>
<?php } ?>
<?php
if ($strViewTitle === "editProfilePhoto" )
{
    $intUserId = $this->app->objHttpRequest->Data->PostData->user_id;
    $strCardMainImage = "";

    $objUserResult = (new Users())->getById($intCardId);
    $objImageResult = (new Images())->getWhere(["entity_id" => $intUserId, "image_class" => "user-avatar", "entity_name" => "user"],"image_id.DESC");

    if ($objImageResult->Result->Success === true && $objImageResult->Result->Count > 0)
    {
        $strCardMainImage = $objImageResult->Data->First()->url;
    }

    // Fire wall for bad card id.

    $objUser = $objUserResult->Data->First();

    if($objImageResult->Result->Success === true && $objImageResult->Result->Count > 0) { ?>
        <?php echo $success_message; ?>
        <div class="mainImage">
            <div class="slim" data-ratio="1:1" data-force-size="650,650" data-service="/process/slim/upload?entity_id=<?php echo $intUserId; ?>&user_id=<?php echo $objLoggedInUser->user_id; ?>&entity_name=user&class=user-avatar" id="my-cropper">
                <input type="file"/>
                <img src="<?php echo $strCardMainImage; ?>" alt="">
            </div>
        </div>
    <?php } else { ?>
        <?php echo $success_message; ?>
        <div class="mainImage">
            <div class="slim" data-ratio="1:1" data-force-size="650,650" data-service="/process/slim/upload?entity_id=<?php echo $intUserId; ?>&user_id=<?php echo $objLoggedInUser->user_id; ?>&entity_name=user&class=user-avatar" id="my-cropper">
                <input type="file"/>
            </div>
        </div>
    <?php } ?>
    <script type="application/javascript">
        var objMyCropper = document.getElementById("my-cropper");
        Slim.create(objMyCropper, Slim.getOptionsFromAttributes(objMyCropper))
    </script>
    <?php
}
?>
