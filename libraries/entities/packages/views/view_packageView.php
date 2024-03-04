<?php

use Entities\Products\Classes\Products;

if ($strViewTitle === "editProfile")
{
    if (!isset($this->app->objHttpRequest->Data->PostData->product_id))
    {
        die("Error: You must supply a package id to this controller. " . json_encode($this->app->objHttpRequest->Data->PostData));
    }
    $intPackageId  = $this->app->objHttpRequest->Data->PostData->product_id;
    $objPackageResult = (new Products())->getById($intPackageId);

    if ( $objPackageResult->result->Success === false)
    {
        die("Error: No package was found for id: $intPackageId.");
    }

    $objPackage = $objPackageResult->getData()->first();
    ?>
    <form id= "<?php echo $strViewTitle; ?>Form" action="/customers/user-data/update-user-data?type=profile&id=<?php echo $intPackageId; ?>" method="post">
        <table class="table no-top-border">
            <tr>
                <td style="width:100px;vertical-align: middle;">Package Title</td>
                <td><input class="form-control" type="text" placeholder="Enter Title Name..." value="<?php echo $objPackage->title; ?>"/></td>
            </tr>
            <tr>
                <td style="width:100px;vertical-align: middle;">Abbreviation</td>
                <td><input class="form-control" type="text" placeholder="Enter Unique Abbreviation..." value="<?php echo $objPackage->abbreviation; ?>"/></td>
            </tr>
            <tr>
                <td style="width:100px;vertical-align: middle;">Description</td>
                <td><input class="form-control" type="text" placeholder="Enter Description Name..." value="<?php echo $objPackage->description; ?>"/></td>
            </tr>
        </table>
        <button class="btn btn-primary w-100">Update Profile</button>
    </form>
<?php } ?>