<?php


namespace Entities\Modules\Commands;


use App\Utilities\Command\Command;
use Entities\Cards\Classes\CardAddon;
use Entities\Cards\Classes\Cards;
use Entities\Cards\Models\CardAddonModel;
use Entities\Modules\Classes\AppInstanceRels;
use Entities\Orders\Classes\OrderLines;
use Entities\Orders\Classes\Orders;
use Module\Orders\Models\OrderLineModel;

class UpdateCardAppOrderLines extends Command
{
    public $name = "Apps.UpdateCardApps";
    public $description = "This is a data migration process to loop through member directory data and create value records that work with the new system.";

    /**
     * Executes the command
     */
    public function Run()
    {
        $objAppInstanceRels = new AppInstanceRels();
        $appInstanceRelResult = $objAppInstanceRels->getWhere(["card_addon_id" => ExcellNull]);

        $objCards = new Cards();
        $cardResult = $objCards->getWhereIn("card_id", $appInstanceRelResult->Data->FieldsToArray(["card_id"]));

        $cardResult->Data->Filter(function($currCard)
        {
            if (!empty($currCard->order_line_id)) { return $currCard; }
        });

        $objOrderLines = new OrderLines();
        $orderLineResult = $objOrderLines->getWhereIn("order_line_id", $cardResult->Data->FieldsToArray(["order_line_id"]));

        $objOrders = new Orders();
        $orderResult = $objOrders->getWhereIn("order_id", $orderLineResult->Data->FieldsToArray(["order_id"]));

        $orderLineAllResult = $objOrderLines->getWhereIn("order_id", $orderResult->Data->FieldsToArray(["order_id"]));
        $orderLineAllResult->Data->HydrateChildModelData("card", ["order_line_id" => "order_line_id"], $cardResult->Data, true);

        $orderResult->Data->HydrateChildModelData("order_lines", ["order_id" => "order_id"], $orderLineAllResult->Data, false);

        dump("Processing: " . $orderResult->Result->Count);

        $orderResult->Data->Foreach(function($currOrder) use (&$appInstanceRelResult, $objAppInstanceRels, $objOrderLines)
        {
            if (empty($currOrder->order_lines)) { return; }

            $orderLineWithCard = $currOrder->order_lines->Find(function($currOrderLine)
            {
                if(!empty($currOrderLine->card))
                {
                    return $currOrderLine;
                }
            });

            if ($orderLineWithCard === null) { return; }

            $cardId = $orderLineWithCard->card->card_id;
            $companyId = $orderLineWithCard->card->company_id;

            if (empty($cardId)) { return; }

            $orderLinesForWidget = $currOrder->order_lines->FindMatching(function($currOrderLine) use ($objAppInstanceRels)
            {
                if ($currOrderLine->product_id !== 1003) { return; }
                $appInstanceResult = $objAppInstanceRels->getWhere(["order_line_id" => $currOrderLine->order_line_id]);
                if ($appInstanceResult->Result->Count !== 0) { return; }
                return $currOrderLine;
            });

            $appInstanceRelsForWidget = $appInstanceRelResult->Data->FindMatching(function($currAppInstanceRel) use ($cardId)
            {
                if ($currAppInstanceRel->card_id === $cardId) { return $currAppInstanceRel; }
            });

            dump("card id: " . $cardId);
            $appInstanceRelsForWidgetCount = $appInstanceRelsForWidget->Count();
            $orderLinesForWidgetCount = $orderLinesForWidget->Count();

            dump($appInstanceRelsForWidgetCount . " =? " . $orderLinesForWidgetCount);

            if ($appInstanceRelsForWidgetCount > $orderLinesForWidgetCount)
            {
                for($currAppIndex = $orderLinesForWidgetCount; $currAppIndex < $appInstanceRelsForWidgetCount; $currAppIndex++)
                {
                    $orderLineModel = new OrderLineModel();
                    $orderLineModel->order_id = $currOrder->order_id;
                    $orderLineModel->product_id = 1003;
                    $orderLineModel->user_id = $currOrder->user_id;
                    $orderLineModel->price = 0.00;
                    $orderLineModel->title = "Member Directory Widget";
                    $orderLineModel->status = "started";
                    $orderLineModel->billing_date = $currOrder->created_on;
                    $orderLineModel->created_on = $currOrder->created_on;
                    $orderLineModel->last_updated = $currOrder->last_updated;
                    $orderLineModel->division_id = 0;
                    $orderLineModel->company_id = $companyId;

                    $orderLine = $objOrderLines->createNew($orderLineModel)->Data->First();

                    $orderLinesForWidget->Add($orderLine);
                }
            }

            if ($appInstanceRelsForWidgetCount >= $orderLinesForWidgetCount)
            {
                $currAppIndex = 0;
                $appInstanceRelsForWidget->Foreach(function($currAppInstance) use ($orderLinesForWidget, &$currAppIndex, $cardId, $companyId, $objAppInstanceRels, $currOrder, $objOrderLines) {
                    $orderLine = $orderLinesForWidget->FindByIndex($currAppIndex);

                    $currAppInstance->user_id = $currOrder->user_id;

                    if (empty($orderLine))
                    {
                        $orderLineModel = new OrderLineModel();
                        $orderLineModel->order_id = $currOrder->order_id;
                        $orderLineModel->product_id = 1003;
                        $orderLineModel->user_id = $currOrder->user_id;
                        $orderLineModel->price = 0.00;
                        $orderLineModel->title = "Member Directory Widget";
                        $orderLineModel->status = "started";
                        $orderLineModel->billing_date = $currOrder->created_on;
                        $orderLineModel->created_on = $currOrder->created_on;
                        $orderLineModel->last_updated = $currOrder->last_updated;
                        $orderLineModel->division_id = 0;
                        $orderLineModel->company_id = $companyId;

                        $orderLine = $objOrderLines->createNew($orderLineModel)->Data->First();
                    }

                    $currAppInstance->order_line_id = $orderLine->order_line_id;

                    $objCardAddon = new CardAddon();
                    $cardAddon = $objCardAddon->getWhere(["order_line_id" => $currAppInstance->order_line_id])->Data->First();

                    if (empty($cardAddon))
                    {
                        $cardAddonModal = new CardAddonModel();
                        $cardAddonModal->status = "active";
                        $cardAddonModal->product_id = 1003;
                        $cardAddonModal->product_type_id = 5;
                        $cardAddonModal->order_id = $currOrder->order_id;
                        $cardAddonModal->order_line_id = $orderLine->order_line_id;
                        $cardAddonModal->card_id = $cardId;
                        $cardAddonModal->user_id = $orderLine->user_id;
                        $cardAddonModal->division_id = 0;
                        $cardAddonModal->company_id = $companyId;

                        $cardAddon = $objCardAddon->createNew($cardAddonModal)->Data->First();
                    }

                    $currAppInstance->card_addon_id = $cardAddon->card_addon_id;

                    $objAppInstanceRels->update($currAppInstance);

                    $currAppIndex++;

                    return $currAppInstance;
                });
            }
        });
    }
}