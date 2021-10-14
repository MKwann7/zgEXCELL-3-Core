<?php

namespace Entities\Cards\Components\Vue\CardPaymentWidget;

use App\Core\AppModel;
use App\Website\Vue\Classes\Base\VueComponent;

class CardPaymentAccountWidget extends VueComponent
{
    protected $id = "21270983-4a6e-49db-ad3c-91263e6cba1f";
    protected $modalWidth = 750;

    public function __construct(?AppModel $entity = null, $name = "Card Payment Account Widget", $props = [])
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
        return '
            cardEntity: null,
            cardOriginal: null,
            creditCartType: "other",
        ';
    }

    protected function renderComponentMethods() : string
    {
        global $app;
        return '
            renderCardType: function()
            {
                if (this.cardPaymentAccount.payment_account_id === null || this.cardPaymentAccount.type === "") { this.creditCartType = "other"; }
                else { this.creditCartType = this.cardPaymentAccount.type; }
                
                return "' . $app->objCustomPlatform->getFullPortalDomain() . '/_ez/images/financials/cc_small_" + this.creditCartType + ".png";
            },
            modifyCardPaymentAccount: function()
            {
                const card = {user_id: this.cardEntity.owner_id, card_id: this.cardEntity.card_id}
                card.PaymentAccount = this.cardOriginal.PaymentAccount;
                modal.EngageFloatShield()
               ' . $this->activateDynamicComponentByIdInModal(ManageCardPaymentAccountWidget::getStaticId(), "", "edit", "card", "this.mainEntityList", ["entity" => "card"], "this", true,"function(component) {
                    modal.CloseFloatShield()
                }") . '
            },          
        ';
    }

    protected function renderComponentHydrationScript() : string
    {
        return '
            let self = this;
            this.cardEntity = _.clone(props.entity);
            this.cardOriginal = props.entity;
        ';
    }

    protected function renderComponentComputedValues() : string
    {
        return '
            cardPaymentAccount: function()
            {
                let self = this;
                if (typeof self.cardEntity === "undefined" || self.cardEntity === null || typeof self.cardEntity.PaymentAccount === "undefined") { return {}; }                
                return self.cardEntity.PaymentAccount;
            },
            cardPaymentAccountLoaded: function()
            {
                let self = this;
                if (typeof self.cardEntity === "undefined" || self.cardEntity === null || typeof self.cardEntity.PaymentAccount === "undefined") { return false; }                
                return true;
            },
        ';
    }

    protected function renderTemplate() : string
    {
        return '
        <div class="cardPaymentAccountWidget" style="margin-top: 25px;">
            <v-style type="text/css">
                .selectedPaymentAccount {
                    padding: 15px 20px;
                    box-shadow:rgba(0,0,0,.3) 0 0 5px;
                }
                .selectedPaymentAccount .divCell {
                    vertical-align:middle;
                }
                .selectedPaymentAccount .divCell:nth-child(1) {
                    width: 75px; 
                }
                .selectedPaymentAccount .divCell:nth-child(2),
                .selectedPaymentAccount .divCell:nth-child(3) {
                    width: 125px; 
                }
            </v-style>'.'
            <div v-if="cardEntity !== null">
                <ul>
                    <li v-if="cardEntity && cardEntity.PaymentAccount" class="selectedPaymentAccount">
                        <div class="divTable">
                            <div class="divRow">
                                <div class="divCell">
                                    <img v-bind:src="renderCardType()"/>
                                </div>
                                <div class="divCell">
                                    <div><b>Last 4 Digits</b></div>
                                     {{ cardPaymentAccount.display_1 }}
                                </div>
                                <div class="divCell">
                                    <div><b>Expiration</b></div>
                                    {{ cardPaymentAccount.display_2 }}
                                </div>
                                <div class="divCell text-right">
                                    <span v-on:click="modifyCardPaymentAccount" class="pointer editEntityButton entityButtonFixInTitle"></span>
                                </div>
                            </div>
                        </div> 
                    </li>
                    <li v-if="!cardEntity || !cardEntity.PaymentAccount" class="selectedPaymentAccount">
                        <div class="divTable">
                            <div v-on:click="modifyCardPaymentAccount" class="divRow">
                                <div class="divCell">
                                    <div><b>Billing Account:</b></div>
                                     No Card on File
                                </div>
                                <div style="width:15%;" class="divCell text-right">
                                    <span class="pointer addNewEntityButton entityButtonFixInTitle"></span>
                                </div>
                            </div>
                        </div> 
                    </li>
                </ul>
            </div>
        <div>
        ';
    }
}