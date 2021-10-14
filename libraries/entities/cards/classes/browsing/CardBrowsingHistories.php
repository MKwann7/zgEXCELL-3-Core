<?php

namespace Entities\Cards\Classes\Browsing;

use App\Core\AppEntity;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Cards\Models\CardBrowsingHistoryModel;

class CardBrowsingHistories extends AppEntity
{
    public function __construct()
    {
        parent::__construct();
    }

    public $strEntityName       = "Cards";
    public $strDatabaseTable    = "card_browsing_history";
    public $strDatabaseName     = "Traffic";
    public $strMainModelName    = CardBrowsingHistoryModel::class;
    public $strMainModelPrimary = "card_browsing_history_id";

    public function upsertHistoryRecord($userId, $cardId) : ExcellTransaction
    {
        $cardHistory = $this->getWhere(["user_id" => $userId, "card_id" => $cardId])->Data->First();

        if ($cardHistory === null)
        {
            return $this->createNew(new CardBrowsingHistoryModel([
                "company_id" => $this->app->objCustomPlatform->getCompanyId(),
                "user_id" => $userId,
                "card_id" => $cardId
            ]));
        }

        return $this->update($cardHistory);
    }
}