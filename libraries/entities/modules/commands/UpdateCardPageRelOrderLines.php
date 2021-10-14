<?php

namespace Entities\Modules\Commands;

use App\Utilities\Command\Command;
use Entities\Cards\Classes\CardAddon;
use Entities\Cards\Classes\CardPageRels;
use Entities\Cards\Classes\Cards;
use Entities\Cards\Models\CardAddonModel;
use Entities\Orders\Classes\OrderLines;
use Entities\Orders\Classes\Orders;
use Module\Orders\Models\OrderLineModel;

class UpdateCardPageRelOrderLines extends Command
{
    public $name = "Apps.UpdateCardPageRels";
    public $description = "This is a data migration process to loop through member directory data and create value records that work with the new system.";

    /**
     * Executes the command
     */
    public function Run()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(3000);

        $objCardPageRels = new CardPageRels();
        $cardPageRelResult = $objCardPageRels->getWhere(["card_addon_id" => ExcellNull]);

        $objCards = new Cards();
        $cardResult = $objCards->getWhereIn("card_id", $cardPageRelResult->Data->FieldsToArray(["card_id"]));

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

        $orderResult->Data->Foreach(function($currOrder) use (&$cardPageRelResult, $objCardPageRels, $objOrderLines)
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

            $orderLinesForWidget = $currOrder->order_lines->FindMatching(function($currOrderLine) use ($objCardPageRels)
            {
                if ($currOrderLine->product_id !== 1002) { return; }
                $appInstanceResult = $objCardPageRels->getWhere(["order_line_id" => $currOrderLine->order_line_id]);
                if ($appInstanceResult->Result->Count !== 0) { return; }
                return $currOrderLine;
            });

            $appInstanceRelsForWidget = $cardPageRelResult->Data->FindMatching(function($currAppInstanceRel) use ($cardId)
            {
                if ($currAppInstanceRel->card_id === $cardId) { return $currAppInstanceRel; }
            });

            dump("card id: " . $cardId);
            $appInstanceRelsForWidgetCount = $appInstanceRelsForWidget->Count();
            $orderLinesForWidgetCount = $orderLinesForWidget->Count();

            if ($appInstanceRelsForWidgetCount > $orderLinesForWidgetCount)
            {
                for($currAppIndex = $orderLinesForWidgetCount; $currAppIndex < $appInstanceRelsForWidgetCount; $currAppIndex++)
                {
                    $orderLineModel = new OrderLineModel();
                    $orderLineModel->order_id = $currOrder->order_id;
                    $orderLineModel->product_id = 1002;
                    $orderLineModel->user_id = $currOrder->user_id;
                    $orderLineModel->price = 0.00;
                    $orderLineModel->title = "Card Page";
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

            dump($appInstanceRelsForWidget->Count() . " =? " . $orderLinesForWidget->Count());

            if ($appInstanceRelsForWidgetCount >= $orderLinesForWidgetCount)
            {
                $currAppIndex = 0;
                $appInstanceRelsForWidget->Foreach(function($currAppInstance) use ($orderLinesForWidget, &$currAppIndex, $cardId, $companyId, $objCardPageRels, $currOrder, $objOrderLines) {
                    $orderLine = $orderLinesForWidget->FindByIndex($currAppIndex);

                    if (empty($orderLine))
                    {
                        $orderLineModel = new OrderLineModel();
                        $orderLineModel->order_id = $currOrder->order_id;
                        $orderLineModel->product_id = 1002;
                        $orderLineModel->user_id = $currOrder->user_id;
                        $orderLineModel->price = 0.00;
                        $orderLineModel->title = "Card Page";
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
                        $cardAddonModal->product_id = 1002;
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

                    $objCardPageRels->update($currAppInstance);

                    $currAppIndex++;

                    return $currAppInstance;
                });
            }
        });

        dd("done!");
    }
}