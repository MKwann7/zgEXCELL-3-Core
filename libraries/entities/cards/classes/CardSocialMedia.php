<?php

namespace Entities\Cards\Classes;

use App\Core\AppEntity;
use App\Utilities\Database;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Cards\Models\CardSocialMediaModel;
use Entities\Users\Models\ConnectionModel;

class CardSocialMedia extends AppEntity
{
    public string $strEntityName       = "Cards";
    public $strDatabaseTable    = "card_socialmedia";
    public $strDatabaseName     = "Main";
    public $strMainModelName    = CardSocialMediaModel::class;
    public $strMainModelPrimary = "card_socialmedia_id";

    public function getByCardId($cardId) : ExcellTransaction
    {
        $strCardConnectionsQuery = "
            SELECT 
                cnr1.*,
                cn.user_id, 
                cn.company_id, 
                cnt.name AS connection_type_name,
                cn.connection_type_id, 
                cn.connection_value, 
                cn.is_primary, 
                cn.connection_class, 
                cnt.action AS default_action,
                cnt.font_awesome 
            FROM excell_main.card_socialmedia cnr1 
            JOIN (SELECT MAX(cnrx.card_socialmedia_id) AS most_recent_rel, cnrx.card_socialmedia_id FROM excell_main.card_socialmedia cnrx GROUP BY cnrx.card_socialmedia_id) cnr3
            JOIN excell_main.card_socialmedia cnr2 ON (cnr1.card_socialmedia_id = cnr3.most_recent_rel && cnr2.card_socialmedia_id = cnr3.most_recent_rel)
            LEFT JOIN excell_main.connection cn ON cn.connection_id = cnr1.connection_id 
            LEFT JOIN  excell_main.connection_type cnt ON cnt.connection_type_id = cn.connection_type_id 
            WHERE cnr1.card_id = {$cardId} ORDER BY cnr1.display_order ASC;";

        $colCardConnectionsResult = Database::getSimple($strCardConnectionsQuery);
        $colCardConnectionsResult->getData()->HydrateModelData(ConnectionModel::class, true);

        return $colCardConnectionsResult;
    }
}