<?php

namespace Entities\Cards\Components\Vue\CardPaymentWidget;

use App\Core\AppModel;
use App\Website\Vue\Classes\Base\VueComponent;

class CardPaymentHistoryWidget extends VueComponent
{
    protected string $id = "6de345df-70a8-4226-903e-9ce54b6a2a71";
    protected string $modalWidth = "750";

    public function __construct(?AppModel $entity = null, $name = "Card Payment History Widget", $props = [])
    {
        $this->loadProps($props);
        $this->name = $name;

        parent::__construct($entity);

        $this->modalTitleForAddEntity = "Add " . $name;
        $this->modalTitleForEditEntity = "Edit " . $name;
        $this->modalTitleForDeleteEntity = "Delete " . $name;
        $this->modalTitleForRowEntity = "View " . $name;
    }

    protected function renderComponentDataAssignments() : string
    {
        return "
            cardEntity: null,
        ";
    }

    protected function renderComponentMethods() : string
    {
        global $app;
        $loggedInUser = $app->getActiveLoggedInUser();
        return '
            renderMoney: function(num) 
            {                
                return "$" + parseFloat(this.renderCartCurrency(num)).toFixed(2);
            },
            renderCartCurrency: function(num) 
            {
               return num;
            },
        ';
    }

    protected function renderComponentHydrationScript() : string
    {
        return '
            let self = this;
            this.cardEntity = _.clone(props.entity);
        ';
    }

    protected function renderComponentComputedValues() : string
    {
        return '
            cardPaymentHistory: function()
            {
                let self = this;
                if (typeof self.cardEntity === "undefined" || self.cardEntity === null || typeof self.cardEntity.PaymentHistory === "undefined") { return []; }                
                return self.cardEntity.PaymentHistory;
            },
        ';
    }

    protected function renderTemplate() : string
    {
        return '
        <div class="cardPaymentHistoryWidget" style="margin-top: 25px;">
            <v-style type="text/css">
                .cardPaymentHistoryWidget .cardPaymentHistory td:nth-child(1) {
                    width: 75px; 
                }
                .cardPaymentHistoryWidget .cardPaymentHistory td:nth-child(2),
                .cardPaymentHistoryWidget .cardPaymentHistory td:nth-child(3) {
                    width: 155px; 
                }
            </v-style>'.'
            <div v-if="cardEntity !== null" class="cardPaymentHistory">
                <table class="table table-striped no-top-border table-shadow" style="box-shadow:rgba(0,0,0,.3) 0 0 5px;">
                    <tbody>
                        <tr v-for="currTransaction in cardPaymentHistory">
                            <td>
                                {{ currTransaction.ar_invoice_id }} 
                            </td>
                            <td>
                                {{ formatDateForDisplay(currTransaction.created_on) }} 
                            </td>
                            <td>
                                {{ currTransaction.payment_user }} 
                            </td>
                            <td class="text-right">
                                {{ renderMoney(currTransaction.gross_value) }} 
                            </td> 
                        </tr>
                    </tbody>
                </table>
            </div>
        <div>
        ';
    }
}