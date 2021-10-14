<?php

namespace Entities\Cart\Classes;

use App\Core\App;
use Entities\Packages\Models\PackageLineModel;
use Entities\Products\Classes\ProductProcessor;
use Entities\Tickets\Classes\Journey\Journeys;
use Entities\Tickets\Classes\Tickets;

class CartTicketProcess
{
    /** @var ProductProcessor $processor */
    private $processor;
    private $app;

    public function __construct (ProductProcessor $processor, App $app)
    {
        $this->processor = $processor;
        $this->app       = $app;
    }

    public function registerTickets() : void
    {
        /** @var CartProductCapsule $currCartItem */
        foreach ($this->processor->cartItems as $currCartItem)
        {
            $packageLine = $currCartItem->getPackageLine();

            if(!empty($packageLine->journey_id))
            {
                $this->createNewTicketFromJourneyId($packageLine);
            }
        }
    }

    private function createNewTicketFromJourneyId(PackageLineModel $packageLine) : void
    {
        // create tickets based on Journey record.
        $objJourney = new Journeys();
        $journeyResult = $objJourney->getFullJourneyById($packageLine->journey_id);

        if ($journeyResult->Result->Count !== 1) { return; }

        $objTickets = new Tickets();
        $objTickets->createTicketsByFullJourney($journeyResult->Data->First());
    }
}