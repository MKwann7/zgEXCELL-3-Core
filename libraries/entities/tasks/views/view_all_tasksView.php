<?php
/**
 * Created by PhpStorm.
 * User: Micah.Zak
 * Date: 10/11/2018
 * Time: 9:29 AM
 */

$this->CurrentPage->BodyId            = "view-tasks-page";
$this->CurrentPage->BodyClasses       = ["admin-page", "view-tasks-page", "no-columns"];
$this->CurrentPage->Meta->Title       = "EZcard Tasks | Admin | " . $this->app->objCustomPlatform->getPortalName();
$this->CurrentPage->Meta->Description = "Welcome to the NEW AMAZING WORLD of EZ Digital Cards, where you can create and manage your own cards!";
$this->CurrentPage->Meta->Keywords    = "";
$this->CurrentPage->SnipIt->Title     = "EZcard Tasks";
$this->CurrentPage->SnipIt->Excerpt   = "Welcome to the NEW AMAZING WORLD of EZ Digital Cards, where you can create and manage your own cards!";
$this->CurrentPage->Columns           = 0;

?>
<div class="breadCrumbs">
    <div class="breadCrumbsInner">
        <a href="/account/dashboard/" class="breadCrumbHomeImageLink">
            <img src="/media/images/home-icon-01_white.png" class="breadCrumbHomeImage" width="15" height="15" />
        </a> &#187;
        <a href="/account/dashboard/" class="breadCrumbHomeImageLink">
            <span class="breadCrumbPage">Account</span>
        </a> &#187;
        <a href="/account/dashboard/" class="breadCrumbHomeImageLink">
            <span class="breadCrumbPage">Admin</span>
        </a> &#187;
        <span class="breadCrumbPage">Tasks</span>
    </div>
</div>
<div class="BodyContentBox">
    <style type="text/css">
        .BodyContentBox .formwrapper-inner {
            padding:15px;
        }
        .BodyContentBox .account-page-title {
            padding-left:3px;
            font-family: ArialNarrow;
        }
        .BodyContentBox .cellTitle {
            font-size:20px;
            font-family: ArialNarrow;
            margin-bottom:15px;
        }
        .BodyContentBox .form-container-main {
            margin: 0px 15px;
        }
        .BodyContentBox .cellCard {
            display: block !important;
            box-shadow: 0px 0px 5px rgba(0,0,0,.3);
            background: #fff;
            padding: 15px;
            min-height: 350px;
        }
        .BodyContentBox .errorTextTask {
            color: red;
            font-weight: bold;
            padding-bottom: 15px;
            margin-top: -14px;
        }
    </style>
    <div id="app" class="formwrapper" >
        <div class="formwrapper-inner" v-cloak>
            <input v-model="searchQuery" class="form-control" type="text" placeholder="Search for..."/>
        </div>
        <div class="formwrapper-control">
            <div>
                <table class="table" style="margin-bottom:0px;">
                    <tbody>
                    <tr>
                        <td>
                            <h3 class="account-page-title">Tasks</h3>
                        </td>
                        <td class="text-right page-count-display" style="vertical-align: middle;display:none;">
                            Current: <span>{{ pageIndex }}</span>
                            Pages: <span>{{ totalPages }}</span>
                            <button v-on:click="prevPage()" class="btn prev-btn" :disabled="pageIndex == 1">Prev</button>
                            <button v-on:click="nextPage()" class="btn" :disabled="pageIndex == totalPages">Next</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="form-container-main">
                <div id="auto-generated-services" class="divTable sub-pages-inner sub-page-count-4 sub-page-horizontal auto-generated-thumbnails sub-page-desc sub-page-readmore default-sub-page-theme sub-page-no-llp">
                    <div class="divRow">
                        <div class="divCell zgSubpage_thumb cellCard">
                            <h4 class="cellTitle">Page Cloning / Copying</h4>
                            <div>
                                <div class="errorTextTask1 errorTextTask"></div>
                                <form method="post" id="frmTabCopyAll" action="/tasks/copy-all-card-pages-to-card">
                                    <table border="0" cellpadding="0" cellspacing="0" style="margin-bottom:25px;">
                                        <tr>
                                            <td width=75 style="text-align: left;">From</td>
                                            <td width=145 style="text-align: left;">
                                                <input style="width:150px;" list="templates" placeholder="####" type="text" value=""
                                                       name="src" id="src" size=7>
                                                <datalist id="templates">
                                                    <?php if(!empty($objTemplateCards)) { ?>
                                                    <?php foreach ($objTemplateCards as $strKey => $objTemplateCardData) { ?>
                                                    <option value="<?php echo $objTemplateCardData["id"]; ?>">
                                                    <?php } ?>
                                                    <?php } ?>
                                                </datalist>
                                            </td>
                                            <td width=205><input style="margin-left:5px;" id="tabSelectTabsForCopy" name="tabSelectTabsForCopy" type="button" value="Select Tabs"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" style="height:5px;"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                To
                                            </td>
                                            <td>
                                                <textarea style="width:150px;" placeholder="####" value="<?php echo $_POST['dst']; ?>" name="dst"
                                                    id="dst" size=7></textarea>
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" style="height:5px;"></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>
                                                <input style="width:100%;" id="tabCopyAllFormSubmit" name="tabCopyAllFormSubmit" type="button" value="Copy Tabs">
                                                <p style="padding-left:5px;font-size:12px;font-weight:bold;padding-top:5px;">^^ This one copies tabs...</p>
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" style="height:5px;"></td>
                                        </tr>
                                        <tr>
                                            <td>
                                            </td>
                                            <td><input style="width:100%;" id="mainImageCopySubmit" name="mainImageCopySubmit" type="button"
                                                       value="Copy Image & Settings">
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" style="height:5px;"></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td><input style="width:100%;" id="socialMediaConnectionCopySubmit" name="socialMediaConnectionCopySubmit"
                                                       type="button" value="Copy Connections">
                                            </td>
                                            <td></td>
                                        </tr>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="divRow">
                        <div class="divCell zgSubpage_thumb cellCard">
                            <h4 class="cellTitle">Page Updating 2018-08-03</h4>
                            <div>
                                <div class="errorTextTask2 errorTextTask"></div>
                                <table border="0" cellpadding="0" cellspacing="0" style="margin-bottom:25px;">
                                    <tr>
                                        <td><input style="width:150px;margin-bottom:5px;" placeholder="####" type="text" value=""
                                                   name="vollara_card_1" id="vollara_card_1" size=7><p style="padding-left:5px;font-size:12px;font-weight:bold;padding-top:5px;">^^ Leave this empty to affect all cards</p></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td><input style="width:100%;" id="tabRemoveOldTabs" name="tabRemoveOldTabs" type="button" value="Remove Old Tabs"></td>
                                        <td id="tabRemoveOldTabsResult" style="padding-left:5px;font-size:12px;font-weight:bold;padding-top:5px;"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="height:5px;"></td>
                                    </tr>
                                    <tr>
                                        <td><input style="width:100%;" id="tabAddNewTabs" name="tabAddNewTabs" type="button"
                                                   value="Add New Page">
                                        </td>
                                        <td id="tabAddNewTabsResult" ></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="height:5px;"></td>
                                    </tr>
                                    <tr>
                                        <td><input style="width:100%;" id="tabAppendAboutTabText" name="tabAppendAboutTabText"
                                                   type="button" value="Append About Tab Text">
                                        </td>
                                        <td id="tabAppendAboutTabTextResult" ></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="height:5px;"></td>
                                    </tr>
                                    <tr>
                                        <td><input style="width:100%;" id="tabOrderTabs" name="tabOrderTabs"
                                                   type="button" value="Update Tab Order">
                                        </td>
                                        <td id="tabOrderTabsResult" ></td>
                                    </tr>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="height:5px;"></td>
                                    </tr>
                                    <tr>
                                        <td><input style="width:100%;" id="tabVollaraCommunityUpdate" name="tabVollaraCommunityUpdate"
                                                   type="button" value="Update Vollara Community Tab">
                                        </td>
                                        <td id="tabVollaraCommunityUpdate" ></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="height:5px;"></td>
                                    </tr>
                                    <tr>
                                        <td><input style="width:100%;" id="tabVollaraVideoUpdate" name="tabVollaraVideoUpdate"
                                                   type="button" value="Update Vollara Video">
                                        </td>
                                        <td id="tabVollaraCommunityUpdate" ></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="divRow">
                        <div class="divCell zgSubpage_thumb cellCard">
                            <h4 class="cellTitle">Update Customers & Cards To V2</h4>
                            <div>
                                <div class="errorTextTask3 errorTextTask"></div>
                                <table border="0" cellpadding="0" cellspacing="0" style="margin-bottom:25px;">
                                    <tr>
                                        <td><input style="width:150px;margin-bottom:5px;" placeholder="####" type="text" value=""
                                                   name="v1_card_id_for_update" id="v1_card_id_for_update" size=7><p style="padding-left:5px;font-size:12px;font-weight:bold;padding-top:5px;">^^ Leave this empty to affect all cards</p></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="height:5px;"></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input style="width:100%;" id="convertConnectionsDbToV2" name="convertConnectionsDbToV2" type="button" value="Update V2 Connections From V1 Data">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="height:5px;"></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input style="width:100%;" id="convertCustomerDbToV2" name="convertCustomerDbToV2" type="button" value="Update V2 Card Tabs From V1 Data">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="height:5px;"></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input style="width:100%;" id="convertMainImageDbToV2" name="convertMainImageDbToV2" type="button" value="Update V2 Main Images From V1 Data">
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="divRow">
                        <div class="divCell zgSubpage_thumb cellCard">
                            <h4 class="cellTitle">Coming Soon...</h4>
                            <div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    function adminTaskSystem() {
        var _ = this;
        this.load = function()
        {
            _.EngagePageFormButtons();
            _.EngagePageForms();
        }
        this.EngagePageFormButtons = function()
        {
            $(document).on("click", "#tabCopyAllFormSubmit", function (e)
            {
                if ( $("#src").val() == "")
                {
                    $("#src").focus();
                    $(".errorTextTask1").html("Please enter a source EZcard id.");
                    return;
                }

                if ( $("#dst").val() == "")
                {
                    $("#dst").focus();
                    $(".errorTextTask1").html("Please enter a destination EZcard id.");
                    return;
                }

                if ( !$.isNumeric($("#src").val()))
                {
                    $("#src").focus();
                    $(".errorTextTask1").html("Please enter a valid source EZcard id.");
                    return;
                }

                var objSubscriptionData = {};
                objSubscriptionData["card_id"] = $("#dst").val();

                modal.EngageFloatShield();

                var data = {};
                data.title = "Cloning EZcard Tabs...";
                data.html = "We are processing your EZcard tab cloning request.<br>Please wait a moment...";
                modal.EngagePopUpDialog(data, 450, 115, false);
                $(".zgpopup-dialog-box-inner").addClass("dialog-right-loading-anim");

                var data = {};
                data.html = "We've backed up the current tabs for card " + $("#dst").val() +". Attempting tab cloning...";
                modal.AddFloatDialogMessage(data);

                $("#frmTabCopyAll").submit();
            });

            $(document).on("click", "#tabSelectTabsForCopy", function (e)
            {
                if ( $("#src").val() == "")
                {
                    $("#src").focus();
                    $(".errorTextTask1").html("Please enter a source EZcard id.");
                    return;
                }

                if ( !$.isNumeric($("#src").val()))
                {
                    $("#src").focus();
                    $(".errorTextTask1").html("Please enter a valid source EZcard id.");
                    return;
                }

                var intSourceCardId = $("#src").val();

                modal.EngageFloatShield();

                var data = {};
                data.title = "Select EZcard Tabs For Cloning...";
                modal.EngagePopUpDialog(data, 450, 115, false);

                var objSubscriptionData = {};
                objSubscriptionData["card_id"] = $("#src").val();

                ajax.Send("modules/cards/views/select-tabs-for-copy.php", objSubscriptionData, function(objSubscriptionResult) {
                    if ( objSubscriptionResult.success == false) {
                        var data = {};
                        data.title = "Card Tab Selection Error...";
                        data.html = objSubscriptionResult.message;
                        modal.AddFloatDialogMessage(data);
                        return false;
                    }

                    var data = {};
                    data.html = objSubscriptionResult.html;
                    modal.ReplaceFloatDialogMessage(data);

                }, "POST");
            });

            $(document).on("click", "#mainImageCopySubmit", function (e)
            {
                if ( $("#src").val() == "")
                {
                    $("#src").focus();
                    $(".errorTextTask1").html("Please enter a source EZcard id.");
                    return;
                }

                if ( $("#dst").val() == "")
                {
                    $("#dst").focus();
                    $(".errorTextTask1").html("Please enter a destination EZcard id.");
                    return;
                }

                if ( !$.isNumeric($("#src").val()))
                {
                    $("#src").focus();
                    $(".errorTextTask1").html("Please enter a valid source EZcard id.");
                    return;
                }

                var intDestinationCardId = $("#dst").val();
                var intSourceCardId = $("#src").val();

                modal.EngageFloatShield();

                var data = {};
                data.title = "Cloning EZcard Main Image...";
                data.html = "This will clone the EZcard Main Image from card #" + intSourceCardId + " to card #" + intDestinationCardId  + ".<hr />Do you wish to continue?<div style=\"text-align:center;padding-top:8px;\"><a onclick=\"_.ClonePrimaryImage(" + intDestinationCardId + "," + intSourceCardId +");\" class=\"dynamicButtons\">Continue!</a>";
                modal.EngagePopUpDialog(data, 450, 115, false);
            });

            $(document).on("click", "#socialMediaConnectionCopySubmit", function (e)
            {
                if ( $("#src").val() == "")
                {
                    $("#src").focus();
                    $(".errorTextTask1").html("Please enter a source EZcard id.");
                    return;
                }

                if ( $("#dst").val() == "")
                {
                    $("#dst").focus();
                    $(".errorTextTask1").html("Please enter a destination EZcard id.");
                    return;
                }

                if ( !$.isNumeric($("#src").val()))
                {
                    $("#src").focus();
                    $(".errorTextTask1").html("Please enter a valid source EZcard id.");
                    return;
                }

                var intDestinationCardId = $("#dst").val();
                var intSourceCardId = $("#src").val();

                modal.EngageFloatShield();

                var data = {};
                data.title = "Cloning EZcard Connections...";
                data.html = "This will clone the EZcard connections from card #" + intSourceCardId + " to card #" + intDestinationCardId  + ".<hr />Do you wish to continue?<div style=\"text-align:center;padding-top:8px;\"><a onclick=\"_.CloneConnections(" + intDestinationCardId + "," + intSourceCardId +");\" class=\"dynamicButtons\">Continue!</a>";
                modal.EngagePopUpDialog(data, 450, 115, false);
            });

            $(document).on("click", "#tabRemoveOldTabs", function (e)
            {
                var intVollaraCardId = $("#vollara_card_1").val();

                modal.EngageFloatShield();

                var data = {};
                data.title = "Removing Specific Vollara Tabs...";
                data.html = "This will remove specific tabs from all the Vollara cards.<hr />Do you wish to continue?<div style=\"text-align:center;padding-top:8px;\"><a onclick=\"_.RemoveVollaraTabs("+intVollaraCardId+");\" class=\"dynamicButtons\">Continue!</a>";
                modal.EngagePopUpDialog(data, 450, 115, false);
            });

            $(document).on("click", "#tabAddNewTabs", function (e)
            {
                var intVollaraCardId = $("#vollara_card_1").val();

                modal.EngageFloatShield();

                var data = {};
                data.title = "Adding Specific Vollara Tabs...";
                data.html = "This will add specific tabs from all the Vollara cards.<hr />Do you wish to continue?<div style=\"text-align:center;padding-top:8px;\"><a onclick=\"_.AddVollaraTabs("+intVollaraCardId+");\" class=\"dynamicButtons\">Continue!</a>";
                modal.EngagePopUpDialog(data, 450, 115, false);
            });

            $(document).on("click", "#tabAppendAboutTabText", function (e)
            {
                var intVollaraCardId = $("#vollara_card_1").val();

                modal.EngageFloatShield();

                var data = {};
                data.title = "Appnding Vollara Tab Text...";
                data.html = "This will append about tab text to all the Vollara cards.<hr />Do you wish to continue?<div style=\"text-align:center;padding-top:8px;\"><a onclick=\"_.AppendVollaraAboutTabText("+intVollaraCardId+");\" class=\"dynamicButtons\">Continue!</a>";
                modal.EngagePopUpDialog(data, 450, 115, false);
            });

            $(document).on("click", "#tabOrderTabs", function (e)
            {
                var intVollaraCardId = $("#vollara_card_1").val();

                modal.EngageFloatShield();

                var data = {};
                data.title = "Appnding Vollara Tab Text...";
                data.html = "This will append about tab text to all the Vollara cards.<hr />Do you wish to continue?<div style=\"text-align:center;padding-top:8px;\"><a onclick=\"_.OrderSpecificVollaraTabs("+intVollaraCardId+");\" class=\"dynamicButtons\">Continue!</a>";
                modal.EngagePopUpDialog(data, 450, 115, false);
            });

            $(document).on("click", "#tabVollaraCommunityUpdate", function (e)
            {
                var intVollaraCardId = $("#vollara_card_1").val();

                modal.EngageFloatShield();

                var data = {};
                data.title = "Appnding Vollara Community Tab Text...";
                data.html = "This will replace the Vollara Community tab text to all the Vollara cards.<hr />Do you wish to continue?<div style=\"text-align:center;padding-top:8px;\"><a onclick=\"_.UpdateVollaraCommunityTab("+intVollaraCardId+");\" class=\"dynamicButtons\">Continue!</a>";
                modal.EngagePopUpDialog(data, 450, 115, false);
            });

            $(document).on("click", "#tabVollaraVideoUpdate", function (e)
            {
                var intVollaraCardId = $("#vollara_card_1").val();

                modal.EngageFloatShield();

                var data = {};
                data.title = "Appnding Vollara Video URL...";
                data.html = "This will replace the Vollara Video URL to all the Vollara cards.<hr />Do you wish to continue?<div style=\"text-align:center;padding-top:8px;\"><a onclick=\"_.UpdateVollaraVideo("+intVollaraCardId+");\" class=\"dynamicButtons\">Continue!</a>";
                modal.EngagePopUpDialog(data, 450, 115, false);
            });

            $(document).on("click", "#convertConnectionsDbToV2", function (e)
            {
                var intV1CardId = $("#v1_card_id_for_update").val();
                modal.EngageFloatShield();

                var data = {};
                data.title = "Update V2 Connections From V1 Data";
                data.html = "This will update the EZcard V2 connections to their v1 version.<hr />Do you wish to continue?<div style=\"text-align:center;padding-top:8px;\"><a onclick=\"_.ConvertConnectionsDbToV1("+intV1CardId+");\" class=\"dynamicButtons\">Continue!</a>";
                modal.EngagePopUpDialog(data, 450, 115, true);
            });

            $(document).on("click", "#convertCustomerDbToV2", function (e)
            {
                var intV1CardId = $("#v1_card_id_for_update").val();
                modal.EngageFloatShield();

                var data = {};
                data.title = "Update V2 Card Tabs From V1 Data";
                data.html = "This will update the EZcard V2 Tabs to their v1 version.<hr />Do you wish to continue?<div style=\"text-align:center;padding-top:8px;\"><a onclick=\"_.ConvertTabDataToV1("+intV1CardId+");\" class=\"dynamicButtons\">Continue!</a>";
                modal.EngagePopUpDialog(data, 450, 115, true);
            });

            $(document).on("click", "#convertMainImageDbToV2", function (e)
            {
                var intV1CardId = $("#v1_card_id_for_update").val();
                modal.EngageFloatShield();

                var data = {};
                data.title = "Update V2 Main Image From V1 Data";
                data.html = "This will update the EZcard V2 Main Image to their v1 version.<hr />Do you wish to continue?<div style=\"text-align:center;padding-top:8px;\"><a onclick=\"_.ConvertMainImageToV1("+intV1CardId+");\" class=\"dynamicButtons\">Continue!</a>";
                modal.EngagePopUpDialog(data, 450, 115, true);
            });
        }

        this.RemoveVollaraTabs = function(intVollaraCardId)
        {
            $(".universal-float-shield").last().find(".zgpopup-dialog-body").fadeTo(250,0,function() {
                $(this).html("We are now removing specific tabs from the Vollara card. Please wait a moment.<hr>").fadeTo(250,1,function(){
                    var objVollaraCard = {};
                    objVollaraCard["vollara_id"] = intVollaraCardId;

                    ajax.Send("vollara/engine/customer/vollara-tab-update-2018-08-03-remove-tabs.php", objVollaraCard, function(objSubscriptionResult) {
                        if ( objSubscriptionResult.success == false) {
                            console.log(objSubscriptionResult.message);
                            var data = {};
                            data.title = "Vollara Card Tab Removal Error...";
                            data.html = objSubscriptionResult.message;
                            modal.AddFloatDialogMessage(data);
                            return false;
                        }

                        console.log(objSubscriptionResult.message);

                        var data = {};
                        if (intVollaraCardId)
                        {
                            data.html = "Completed! Vollara Card #" + intVollaraCardId +" has had specific tabs removed.<br/>"+objSubscriptionResult.message+"<br/><div style=\"text-align:center;padding-top:8px;\"><a onclick=\"modal.CloseFloatShield(null);\" class=\"dynamicButtons\">Continue!</a>";
                        }
                        else
                        {
                            data.html = "Completed! All Vollara Cards have had their specific tabs removed.<br/>\"+objSubscriptionResult.message+\"<br/><div style=\"text-align:center;padding-top:8px;\"><a onclick=\"modal.CloseFloatShield(null);\" class=\"dynamicButtons\">Continue!</a>";
                        }

                        modal.AddFloatDialogMessage(data);
                    }, "POST");
                });
            });
        }

        this.AddVollaraTabs = function(intVollaraCardId)
        {
            $(".universal-float-shield").last().find(".zgpopup-dialog-body").fadeTo(250,0,function() {
                $(this).html("We are now adding specific tabs to the Vollara card. Please wait a moment.<hr>").fadeTo(250,1,function(){
                    var objVollaraCard = {};
                    objVollaraCard["vollara_id"] = intVollaraCardId;

                    ajax.Send("vollara/engine/customer/vollara-tab-update-2018-08-03-add-tabs.php", objVollaraCard, function(objSubscriptionResult) {
                        if ( objSubscriptionResult.success == false) {
                            console.log(objSubscriptionResult.message);
                            var data = {};
                            data.title = "Vollara Card Tab Addition Error...";
                            data.html = objSubscriptionResult.message;
                            modal.AddFloatDialogMessage(data);
                            return false;
                        }

                        console.log(objSubscriptionResult.message);

                        var data = {};
                        if (intVollaraCardId)
                        {
                            data.html = "Completed! Vollara Card #" + intVollaraCardId +" has had specific tabs added.<br/>"+objSubscriptionResult.message+"<br/><div style=\"text-align:center;padding-top:8px;\"><a onclick=\"modal.CloseFloatShield(null);\" class=\"dynamicButtons\">Continue!</a>";
                        }
                        else
                        {
                            data.html = "Completed! All Vollara Cards have had specific tabs added.<br/>\"+objSubscriptionResult.message+\"<br/><div style=\"text-align:center;padding-top:8px;\"><a onclick=\"modal.CloseFloatShield(null);\" class=\"dynamicButtons\">Continue!</a>";
                        }

                        modal.AddFloatDialogMessage(data);
                    }, "POST");
                });
            });
        }

        this.AppendVollaraAboutTabText = function(intVollaraCardId)
        {
            $(".universal-float-shield").last().find(".zgpopup-dialog-body").fadeTo(250,0,function() {
                $(this).html("We are now modifying specific tabs on the Vollara card. Please wait a moment.<hr>").fadeTo(250,1,function(){
                    var objVollaraCard = {};
                    objVollaraCard["vollara_id"] = intVollaraCardId;

                    ajax.Send("vollara/engine/customer/vollara-tab-update-2018-08-03-update-about-tabs.php", objVollaraCard, function(objSubscriptionResult) {
                        if ( objSubscriptionResult.success == false) {
                            console.log(objSubscriptionResult.message);
                            var data = {};
                            data.title = "Vollara Card Tab Update Error...";
                            data.html = objSubscriptionResult.message;
                            modal.AddFloatDialogMessage(data);
                            return false;
                        }

                        console.log(objSubscriptionResult.message);

                        var data = {};
                        if (intVollaraCardId)
                        {
                            data.html = "Completed! Vollara Card #" + intVollaraCardId +" has had a specific tab updated.<br/>"+objSubscriptionResult.message+"<br/><div style=\"text-align:center;padding-top:8px;\"><a onclick=\"modal.CloseFloatShield(null);\" class=\"dynamicButtons\">Continue!</a>";
                        }
                        else
                        {
                            data.html = "Completed! All Vollara Cards have had specific tabs updated.<br/>\"+objSubscriptionResult.message+\"<br/><div style=\"text-align:center;padding-top:8px;\"><a onclick=\"modal.CloseFloatShield(null);\" class=\"dynamicButtons\">Continue!</a>";
                        }

                        modal.AddFloatDialogMessage(data);
                    }, "POST");
                });
            });
        }

        this.OrderSpecificVollaraTabs = function(intVollaraCardId)
        {
            $(".universal-float-shield").last().find(".zgpopup-dialog-body").fadeTo(250,0,function() {
                $(this).html("We are now modifying specific tabs on the Vollara card. Please wait a moment.<hr>").fadeTo(250,1,function(){
                    var objVollaraCard = {};
                    objVollaraCard["vollara_id"] = intVollaraCardId;

                    ajax.Send("vollara/engine/customer/vollara-tab-update-2018-08-03-order-tabs.php", objVollaraCard, function(objSubscriptionResult) {
                        if ( objSubscriptionResult.success == false) {
                            console.log(objSubscriptionResult.message);
                            var data = {};
                            data.title = "Vollara Card Tab Order Update Error...";
                            data.html = objSubscriptionResult.message;
                            modal.AddFloatDialogMessage(data);
                            return false;
                        }

                        console.log(objSubscriptionResult.message);

                        var data = {};
                        if (intVollaraCardId)
                        {
                            data.html = "Completed! Vollara Card #" + intVollaraCardId +" has had its tab order updated.<br/>"+objSubscriptionResult.message+"<br/><div style=\"text-align:center;padding-top:8px;\"><a onclick=\"modal.CloseFloatShield(null);\" class=\"dynamicButtons\">Continue!</a>";
                        }
                        else
                        {
                            data.html = "Completed! All Vollara Cards have had their tab orders updated.<br/>"+objSubscriptionResult.message+"<br/><div style=\"text-align:center;padding-top:8px;\"><a onclick=\"modal.CloseFloatShield(null);\" class=\"dynamicButtons\">Continue!</a>";
                        }

                        modal.AddFloatDialogMessage(data);
                    }, "POST");
                });
            });
        }

        this.UpdateVollaraCommunityTab = function(intVollaraCardId)
        {
            $(".universal-float-shield").last().find(".zgpopup-dialog-body").fadeTo(250,0,function() {
                $(this).html("We are now modifying specific tabs on the Vollara card. Please wait a moment.<hr>").fadeTo(250,1,function(){
                    var objVollaraCard = {};
                    objVollaraCard["vollara_id"] = intVollaraCardId;

                    ajax.Send("vollara/engine/customer/vollara-tab-update-2018-08-08-vollara-community.php", objVollaraCard, function(objSubscriptionResult) {
                        if ( objSubscriptionResult.success == false) {
                            console.log(objSubscriptionResult.message);
                            var data = {};
                            data.title = "Vollara Card Tab Update Error...";
                            data.html = objSubscriptionResult.message;
                            modal.AddFloatDialogMessage(data);
                            return false;
                        }

                        console.log(objSubscriptionResult.message);

                        var data = {};
                        if (intVollaraCardId)
                        {
                            data.html = "Completed! Vollara Card #" + intVollaraCardId +" has had its tab updated.<br/>"+objSubscriptionResult.message+"<br/><div style=\"text-align:center;padding-top:8px;\"><a onclick=\"modal.CloseFloatShield(null);\" class=\"dynamicButtons\">Continue!</a>";
                        }
                        else
                        {
                            data.html = "Completed! All Vollara Cards have had their tab orders updated.<br/>"+objSubscriptionResult.message+"<br/><div style=\"text-align:center;padding-top:8px;\"><a onclick=\"modal.CloseFloatShield(null);\" class=\"dynamicButtons\">Continue!</a>";
                        }

                        modal.AddFloatDialogMessage(data);
                    }, "POST");
                });
            });
        }

        this.UpdateVollaraVideo = function(intVollaraCardId)
        {
            $(".universal-float-shield").last().find(".zgpopup-dialog-body").fadeTo(250,0,function() {
                $(this).html("We are now modifying specific tabs on the Vollara card. Please wait a moment.<hr>").fadeTo(250,1,function(){
                    var objVollaraCard = {};
                    objVollaraCard["vollara_id"] = intVollaraCardId;

                    ajax.Send("vollara/engine/customer/vollara-tab-update-2018-08-08-vollara-video.php", objVollaraCard, function(objSubscriptionResult) {
                        if ( objSubscriptionResult.success == false) {
                            console.log(objSubscriptionResult.message);
                            var data = {};
                            data.title = "Vollara Card Video Update Error...";
                            data.html = objSubscriptionResult.message;
                            modal.AddFloatDialogMessage(data);
                            return false;
                        }

                        console.log(objSubscriptionResult.message);

                        var data = {};
                        if (intVollaraCardId)
                        {
                            data.html = "Completed! Vollara Card #" + intVollaraCardId +" has had its video updated.<br/>"+objSubscriptionResult.message+"<br/><div style=\"text-align:center;padding-top:8px;\"><a onclick=\"modal.CloseFloatShield(null);\" class=\"dynamicButtons\">Continue!</a>";
                        }
                        else
                        {
                            data.html = "Completed! All Vollara Cards have had their videos updated.<br/>"+objSubscriptionResult.message+"<br/><div style=\"text-align:center;padding-top:8px;\"><a onclick=\"modal.CloseFloatShield(null);\" class=\"dynamicButtons\">Continue!</a>";
                        }

                        modal.AddFloatDialogMessage(data);
                    }, "POST");
                });
            });
        }

        this.ConvertTabDataToV1 = function(inV1CardId)
        {
            $(".universal-float-shield").last().find(".zgpopup-dialog-body").fadeTo(250,0,function() {
                $(this).html("We are now modifying specific tabs on the EZcard. Please wait a moment.<hr>").fadeTo(250,1,function(){

                    ajax.Send("tasks/copy-v1-tabs-to-v2-card?card_num=" + inV1CardId, null, function(objSubscriptionResult) {
                        if ( objSubscriptionResult.success == false) {
                            console.log(objSubscriptionResult.message);
                            var data = {};
                            data.title = "Customer Conversion to V2 Error...";
                            data.html = objSubscriptionResult.message;
                            modal.AddFloatDialogMessage(data);
                            return false;
                        }

                        console.log(objSubscriptionResult.message);

                        var data = {};

                        data.html = "Completed! All Card Tab Data have been upgraded from v1 data.<br/>"+objSubscriptionResult.message+"<br/><div style=\"text-align:center;padding-top:8px;\"><a onclick=\"modal.CloseFloatShield(null);\" class=\"dynamicButtons\">Continue!</a>";

                        modal.AddFloatDialogMessage(data);
                    }, "GET");
                });
            });
        }

        this.ConvertMainImageToV1 = function(inV1CardId)
        {
            $(".universal-float-shield").last().find(".zgpopup-dialog-body").fadeTo(250,0,function() {
                $(this).html("We are now modifying the main image on the EZcard. Please wait a moment.<hr>").fadeTo(250,1,function(){

                    ajax.Send("tasks/copy-v1-tabs-to-v2-card?card_num=" + inV1CardId, null, function(objSubscriptionResult) {
                        if ( objSubscriptionResult.success == false) {
                            console.log(objSubscriptionResult.message);
                            var data = {};
                            data.title = "Customer Conversion to V2 Error...";
                            data.html = objSubscriptionResult.message;
                            modal.AddFloatDialogMessage(data);
                            return false;
                        }

                        console.log(objSubscriptionResult.message);

                        var data = {};

                        data.html = "Completed! The main image has been upgraded from v1 data.<br/>"+objSubscriptionResult.message+"<br/><div style=\"text-align:center;padding-top:8px;\"><a onclick=\"modal.CloseFloatShield(null);\" class=\"dynamicButtons\">Continue!</a>";

                        modal.AddFloatDialogMessage(data);
                    }, "GET");
                });
            });
        }

        this.ConvertConnectionsDbToV1 = function(inV1CardId)
        {
            $(".universal-float-shield").last().find(".zgpopup-dialog-body").fadeTo(250,0,function() {
                $(this).html("We are now modifying specific connections on the EZcard. Please wait a moment.<hr>").fadeTo(250,1,function(){

                    ajax.Send("tasks/insert-v1-connection-values?card_num=" + inV1CardId, null, function(objSubscriptionResult) {
                        if ( objSubscriptionResult.success == false) {
                            console.log(objSubscriptionResult.message);
                            var data = {};
                            data.title = "Connections Conversion to V2 Error...";
                            data.html = objSubscriptionResult.message;
                            modal.AddFloatDialogMessage(data);
                            return false;
                        }

                        console.log(objSubscriptionResult.message);

                        var data = {};

                        data.html = "Completed! All connections have been upgraded to v1 data.<br/>"+objSubscriptionResult.message+"<br/><div style=\"text-align:center;padding-top:8px;\"><a onclick=\"modal.CloseFloatShield(null);\" class=\"dynamicButtons\">Continue!</a>";

                        modal.AddFloatDialogMessage(data);
                    }, "GET");
                });
            });
        }

        this.EngagePageForms = function()
        {
            app.Form('frmTabCopyAll', null, function (objPaymentAccountResult) {
                console.log(objPaymentAccountResult);
                if (objPaymentAccountResult.success == false) {
                    var data = {};
                    data.title = "Card Tab Copy Error...";
                    data.html = objPaymentAccountResult.message;
                    modal.AddFloatDialogMessage(data);
                    return false;
                }

                var data = {};
                data.html = objPaymentAccountResult.message;
                modal.AddFloatDialogMessage(data);

                var data = {};
                data.title = "Cloning EZcard Complete!";
                data.html = "Your EZcard has been cloned!<br/><div style=\"text-align:center;padding-top:8px;\"><a onclick=\"modal.CloseFloatShield(null);\" class=\"dynamicButtons\">Continue!</a></div>";
                modal.AddFloatDialogMessage(data);
                $(".universal-float-shield").last().find(".zgpopup-dialog-box-inner").removeClass("dialog-right-loading-anim");

            });

            app.Form('frmVollara2018_08_03', null, function (objPaymentAccountResult) {
                if (objPaymentAccountResult.success == false) {
                    var data = {};
                    data.title = "Vollara Card Tab Management Error...";
                    data.html = objPaymentAccountResult.message;
                    modal.AddFloatDialogMessage(data);
                    return false;
                }

                var data = {};
                data.html = objPaymentAccountResult.message;
                modal.AddFloatDialogMessage(data);

                var data = {};
                data.title = "Cloning EZcard Complete!";
                data.html = "Your EZcard has been cloned!<br/><div style=\"text-align:center;padding-top:8px;\"><a onclick=\"modal.CloseFloatShield(null);\" class=\"dynamicButtons\">Continue!</a></div>";
                modal.AddFloatDialogMessage(data);
                $(".universal-float-shield").last().find(".zgpopup-dialog-box-inner").removeClass("dialog-right-loading-anim");

            });
        }

        this.ClonePrimaryImage = function(intDestinationCardId, intSourceCardId)
        {
            $(".universal-float-shield").last().find(".zgpopup-dialog-body").fadeTo(250,0,function() {
                $(this).html("We are now cloning the primary image and colors. Please wait a moment.<hr>").fadeTo(250,1,function(){
                    var objClonePrimaryImageCardData = {};
                    objClonePrimaryImageCardData["destination_card_id"] = intDestinationCardId;
                    objClonePrimaryImageCardData["source_card_id"] = intSourceCardId;

                    ajax.Send("tasks/clone-card-primary-image", objClonePrimaryImageCardData, function(objSubscriptionResult) {
                        if ( objSubscriptionResult.success == false) {
                            console.log(objSubscriptionResult.message);
                            var data = {};
                            data.title = "Cloning Card Primary Image Error...";
                            data.html = objSubscriptionResult.message;
                            modal.AddFloatDialogMessage(data);
                            return false;
                        }

                        console.log(objSubscriptionResult.message);

                        var data = {};
                        data.html = "Completed! Card #" + intDestinationCardId +" now has #" + intSourceCardId + "'s primary image.<br/><div style=\"text-align:center;padding-top:8px;\"><a onclick=\"modal.CloseFloatShield(null);\" class=\"dynamicButtons\">Continue!</a>";
                        modal.AddFloatDialogMessage(data);
                    }, "POST");
                });
            });
        }

        this.CloneConnections = function(intDestinationCardId, intSourceCardId)
        {
            $(".universal-float-shield").last().find(".zgpopup-dialog-body").fadeTo(250,0,function() {
                $(this).html("We are now cloning the card connections. Please wait a moment.<hr>").fadeTo(250,1,function(){

                    var objClonePrimaryImageCardData = {};
                    objClonePrimaryImageCardData["destination_card_id"] = intDestinationCardId;
                    objClonePrimaryImageCardData["source_card_id"] = intSourceCardId;

                    ajax.Send("tasks/clone-card-connections", objClonePrimaryImageCardData, function(objSubscriptionResult) {
                        if ( objSubscriptionResult.success == false) {
                            var data = {};
                            data.title = "Cloning Card Primary Image Error...";
                            data.html = objSubscriptionResult.message;
                            modal.AddFloatDialogMessage(data);
                            return false;
                        }

                        var data = {};
                        data.html = "Completed! Card #" + intDestinationCardId +" now has #" + intSourceCardId + "'s connections.<br/><div style=\"text-align:center;padding-top:8px;\"><a onclick=\"modal.CloseFloatShield(null);\" class=\"dynamicButtons\">Continue!</a>";
                        modal.AddFloatDialogMessage(data);
                    }, "POST");
                });
            });
        }
    }

    var _ = new adminTaskSystem();

    $(document).ready(function() {
        _.load();
    });
</script>

