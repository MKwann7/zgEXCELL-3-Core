<?php

namespace Entities\Tickets\Classes;

use App\Core\AppEntity;
use App\Utilities\Database;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Companies\Models\CompanyModel;
use Entities\Tickets\Classes\Journey\Journeys;
use Entities\Tickets\Models\JourneyModel;
use Entities\Tickets\Models\TicketModel;

class Tickets extends AppEntity
{
    public string $strEntityName       = "Tickets";
    public $strDatabaseTable    = "ticket";
    public $strDatabaseName     = "Crm";
    public $strMainModelName    = TicketModel::class;
    public $strMainModelPrimary = "ticket_id";
    public $isPrimaryModule     = true;

    public function __construct()
    {
        parent::__construct();
    }

    public function getByUuid($uuid) : ExcellTransaction
    {
        $objWhereClause = "
            SELECT tk.*,
            (SELECT cp.platform_name FROM `excell_main`.`company` cp WHERE cp.company_id = tk.company_id LIMIT 1) AS platform, 
            (SELECT queue_type_id FROM `excell_crm`.`ticket_queue` tq WHERE tq.ticket_queue_id = tk.ticket_queue_id LIMIT 1) AS queue_type_id,
            (SELECT CONCAT(ur.first_name, ' ', ur.last_name) FROM `excell_main`.`user` ur WHERE ur.user_id = tk.assignee_id LIMIT 1) AS owner,
            (SELECT CONCAT(ur.first_name, ' ', ur.last_name) FROM `excell_main`.`user` ur WHERE ur.user_id = tk.user_id LIMIT 1) AS user 
            FROM `excell_crm`.`ticket` tk ";

        $objWhereClause .= "WHERE tk.sys_row_id = '".$uuid."' LIMIT 1";

        $ticketResult = Database::getSimple($objWhereClause, "ticket_id");
        $ticketResult->getData()->HydrateModelData(TicketModel::class, true);

        if ($ticketResult->result->Count !== 1)
        {
            return new ExcellTransaction(false, $ticketResult->result->Message, ["errors" => [$ticketResult->result->Message, $objWhereClause]]);
        }

        return $ticketResult;
    }

    public function createTicketsByFullJourney(JourneyModel $journey) : ExcellTransaction
    {
        $this->recursivelyCreateTicketsFromJourney($journey, null);
    }

    private function recursivelyCreateTicketsFromJourney(JourneyModel $journey, $parentTicketId = null) : void
    {
        $newTicket = new TicketModel();
        $newTicket->company_id = $journey->company_id;
        $newTicket->division_id = $journey->division_id;
        $newTicket->journey_id = $journey->journey_id;
        $newTicket->ticket_queue_id = $journey->ticket_queue_id;
        $newTicket->summary = "";
        $newTicket->description = "";
        $newTicket->status = "pending";
        $newTicket->entity_id = null;
        $newTicket->entity_name = null;
        $newTicket->type = null;
        $newTicket->expected_completion = null;

        // Custom/Optional
        $newTicket->parent_ticket_id = $parentTicketId;
        $newTicket->follows_id = $journey->follows_id;
        $newTicket->user_id = $journey->user_id;
        $newTicket->assignee_id = $journey->assignee_id;

        $ticketCreationResult = (new Journeys())->createNew($newTicket);

        if ($ticketCreationResult->result->Success !== true)
        {
            return;
        }

        if (!empty($journey->children) && $journey->children->Count() > 0)
        {
            foreach($journey->children as $currChildJourney)
            {
                $this->recursivelyCreateTicketsFromJourney($currChildJourney, $ticketCreationResult->getData()->first());
            }
        }
    }
}