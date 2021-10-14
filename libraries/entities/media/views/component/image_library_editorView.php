<?php
/**
 * Created by PhpStorm.
 * User: micah
 * Date: 3/31/2019
 * Time: 1:19 AM
 */

use Entities\Media\Classes\Images;
use Entities\Users\Classes\Users;

if ($strViewTitle === "editImage" )
{
    $intImageId = App::$objHttpRequest->Data->PostData->image_id;

    $strCardMainImage = "";
    $objUserResult = null;

    $objImageResult = (new Images())->getWhere(["image_id" => $intImageId],1);

    if ($objImageResult->Result->Success === true && $objImageResult->Result->Count > 0)
    {
        $strCardMainImage = $objImageResult->Data->First()->url;
        $objUser = (new Users())->getById($objImageResult->Data->First()->user_id)->Data->First();
    }

    if($objImageResult->Result->Success === true && $objImageResult->Result->Count > 0) { ?>
        <div class="mainImage divTable">
            <div class="divRow">
                <div class="divCell">
                    <img class="pointer" src="<?php echo $strCardMainImage; ?>" style="max-width:500px" alt="">
                </div>
                <div class="divCell" style="padding-left:20px;width:310px;">
                    <h3>Image Details</h3>
                    <form id="updateImageDataForm" autocomplete="off" action="/media/media-data/update-image-data" method="post">
                        <input type="hidden" name="image_id" value="<?php echo $intImageId; ?>" />
                        <input style="margin:15px 0;" name="title" class="form-control" type="text" placeholder="Image Title..." value="<?php echo $objImageResult->Data->First()->title ?? ''; ?>"/>
                        <button class="btn btn-primary w-100">Update Image Data</button>
                    </form>
                </div>
            </div>
        </div>
        <style type="text/css">
            @media (max-width:750px) {
                .mainImage .divCell {
                    display:block;
                }
                .mainImage .divCell img{
                    width:100%;
                }
                .mainImage .divCell:nth-child(2) {
                    padding-left:0px !important;
                    padding-top:15px;
                }
                .mainImage .divCell:nth-child(2) {
                    width:100% !important;
                }
            }
        </style>
    <?php } ?>
    <?php
}
?>
