<?php
/**
 * Created by PhpStorm.
 * User: micah
 * Date: 1/11/2020
 * Time: 11:47 AM
 */

use Entities\Cards\Classes\Cards;
use Entities\Visitors\Classes\Visitors;
global $app;

$cardTitle = "Card";

if ($app->objCustomPlatform->getCompanyId() === 0)
{
    $cardTitle = "EZcard";
}

?>

<div class="width100 entityDetails">
    <div class="width50">
        <div class="card-tile-50">
            <h4>My <?php echo $app->objCustomPlatform->getPortalName(); ?> Success Journey</h4>
            <!--<iframe src="https://player.vimeo.com/video/384218503?autoplay=1&color=f3f3f3&title=0&byline=0&portrait=0" width="800" height="450" frameborder="0" style="width:100%;margin-top:15px;" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>-->
        </div>
    </div>
    <div class="width50">
        <div class="card-tile-50">
            <h4>My <?php echo $app->objCustomPlatform->getPortalName(); ?> Account Overview</h4>
            <div class="entityDetailsInner cardProfile" style="margin-top:20px;">
                <table>
                    <tbody>
                        <tr>
                            <td style="width:150px;"><a href="/account/cards">My <?php echo $cardTitle; ?></a>:</td>
                            <td><?php
                                $lstUserCards = (new Cards())->GetByUserId($this->app->intActiveUserId);
                                echo $lstUserCards->Result->Count;
                                ?></td>
                        </tr>
                        <tr>
                            <td>My Visitors:</td>
                            <td><?php
                                $lstCardsTraffic = (new Visitors())->getWhereIn("card_id", $lstUserCards->Data->FieldsToArray(["card_id"]));
                                echo number_format($lstCardsTraffic->Result->Count,0,".",",");
                                ?></td>
                        </tr>
                        <tr>
                            <td>My Points:</td>
                            <td>TBA</td>
                        </tr>
                        <tr>
                            <td>My Commission Level:</td>
                            <td>TBA</td>
                        </tr>
                        <tr>
                            <td>Founder Level:</td>
                            <td>TBA</td>
                        </tr>
                        <tr>
                            <td>Game Changers:</td>
                            <td>TBA</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-tile-50" style="margin-top:15px;">
            <h4>My Messages</h4>
            <div class="entityDetailsInner cardProfile" style="margin-top:20px;">
                Coming Soon.
            </div>
        </div>
    </div>
</div>
