<?php

namespace Entities\Cards\Classes;

use App\Core\AppController;
use App\Core\AppEntity;
use App\Utilities\Database;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Cards\Models\CardPageRelModel;
use Entities\Modules\Classes\AppInstanceRels;
use Entities\Modules\Classes\AppInstanceRelSettings;

class CardPageRels extends AppEntity
{
    public string $strEntityName       = "Cards";
    public $strDatabaseTable    = "card_tab_rel";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = CardPageRelModel::class;
    public $strMainModelPrimary = "card_tab_rel_id";

    public function GetByCardIdAndTabId($intCardId, $intCardPageId) : ExcellTransaction
    {
        $objReturnTransaction = new ExcellTransaction();

        if (empty($intCardId) || !isInteger($intCardId))
        {
            $objReturnTransaction->result->Success = false;
            $objReturnTransaction->result->Count = 0;
            $objReturnTransaction->result->Message = "The Card id must be an integer.";

            return $objReturnTransaction;
        }

        if (empty($intCardPageId) || !isInteger($intCardPageId))
        {
            $objReturnTransaction->result->Success = false;
            $objReturnTransaction->result->Count = 0;
            $objReturnTransaction->result->Message = "The Card Tab id must be an integer.";

            return $objReturnTransaction;
        }

        return $this->getWhere(["card_tab_id" => $intCardPageId, "card_id" => $intCardId]);
    }

    public function getCardPageAndRelWithWidgetByRelId($intRelId) : ExcellTransaction
    {
        $strCardPageRelQuery = "SELECT 
                    ctr.card_tab_rel_id, 
                    ctr.card_tab_id, 
                    ctr.card_id, 
                    ctr.user_id, 
                    ct.card_tab_type_id, 
                    ct.title, 
                    ct.menu_title, 
                    ct.library_tab, 
                    ct.visibility, 
                    ct.permanent, 
                    ct.instance_count, 
                    ctr.rel_sort_order,  
                    ct.order_number,
                    ct.content,
                    ctr.rel_visibility,
                    ct.card_tab_data,  
                    ctr.card_tab_rel_data, 
                    ctr.card_tab_rel_type,
                    pd.product_id as card_product_id,
                    pdu.enduser_label as card_product_enduser       
                FROM excell_main.card_tab_rel ctr 
                LEFT JOIN excell_main.card_tab ct ON ctr.card_tab_id = ct.card_tab_id 
                LEFT JOIN excell_main.card cd ON ctr.card_id = cd.card_id 
                LEFT JOIN excell_main.product pd ON cd.product_id = pd.product_id 
                LEFT JOIN excell_main.product_enduser pdu ON pd.product_enduser_id = pdu.product_enduser_id 
                WHERE ctr.card_tab_rel_id = " . $intRelId . ";";

        $objCardPageRelResult = Database::getSimple($strCardPageRelQuery,"card_tab_rel_id");
        $objCardPageRelResult->getData()->HydrateModelData(CardPageRelModel::class, true);

        $objModuleApp = new AppInstanceRels();
        $objCardWidget = $objModuleApp->getByPageRelId($intRelId);

        if ($objCardWidget->getResult()->Count === 0) {
            return $objCardPageRelResult;
        }

        $objModuleAppWidgetSettings = new AppInstanceRelSettings();
        $widgetSettings = $objModuleAppWidgetSettings->getByInstanceRelId($objCardWidget->getData()->first()->app_instance_rel_id);

        $objCardWidget->getData()->HydrateChildModelData("__settings", ["app_instance_rel_id" => "app_instance_rel_id"], $widgetSettings->getData());
        $objCardPageRelResult->getData()->HydrateChildModelData("__app", ["card_page_rel_id" => "card_tab_rel_id"], $objCardWidget->getData(), true);

        return $objCardPageRelResult;
    }
}

