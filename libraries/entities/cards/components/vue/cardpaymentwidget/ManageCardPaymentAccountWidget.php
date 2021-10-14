<?php

namespace Entities\Cards\Components\Vue\CardPaymentWidget;

use App\Core\AppModel;
use App\Website\Vue\Classes\Base\VueComponent;

class ManageCardPaymentAccountWidget extends VueComponent
{
    protected $id         = "3270ec12-567f-4625-ab79-886e15e5bf69";
    protected $modalWidth = 750;

    public function __construct (?AppModel $entity = null, $name = "Card Payment Account", $props = [])
    {
        $this->loadProps($props);
        $this->name = $name;

        parent::__construct($entity);

        $this->modalTitleForAddEntity    = "Add " . $name;
        $this->modalTitleForEditEntity   = "Edit " . $name;
        $this->modalTitleForDeleteEntity = "Delete " . $name;
        $this->modalTitleForRowEntity    = "View " . $name;
    }

    protected function renderComponentDataAssignments (): string
    {
        return '
            cardEntity: null,
            cardOriginal: null,
            creditCartType: "other",
            cartPaymentAccount: null,
            customerPaymentAccounts: [],
            toggleCreateNewCartPaymentAccount: false,
            errorText: {},
            newCardForCreate: {name:"", number: "", expMonth: "", expYear: "", cvc: "", line1: "",  line2: "",  state: "", zip: "", country: ""},
            creditCardTest: {visa: "^4[0-9]{0,15}$", mc: "^5[0-9]{0,15}$", amex: "^3[47][0-9]{0,13}$", disc: "^6(?:011|5[0-9]{2})[0-9].*$", diners: "^3(?:0[0-5]|[68][0-9])[0-9].*$", jcb: "^(?:2131|1800|35[0-9]{3})[0-9].*$", other: "[0-9]{6,}$"},
        ';
    }

    protected function renderComponentMethods (): string
    {
        global $app;
        return '
            renderMoney: function(num) 
            {                
                return "$" + parseFloat(this.renderCartCurrency(num)).toFixed(2);
            },
            renderCartCurrency: function(num) 
            {
               return num;
            },
            createNewCartPaymentAccount: function()
            {
                this.toggleCreateNewCartPaymentAccount = true;
            },
            getPaymentAccounts: function()
            {
                let self = this;
                const url = "'.$app->objCustomPlatform->getFullPortalDomain().'/cart/get-user-payment-accounts?id=" + this.cardEntity.user_id;
                                
                ajax.GetExternal(url, true, function(result)
                {
                    if (result.response.success === true)
                    {
                        self.customerPaymentAccounts = [];
                       
                        const paymentAccounts = Object.entries(result.response.data.paymentAccounts);
                        paymentAccounts.forEach(function([index, currPaymentAccount])
                        {
                            self.customerPaymentAccounts.push(currPaymentAccount);
                        });
                        
                        self.$forceUpdate();
                    }
                });
            },
            selectCartPaymentAccount: function(account)
            {
                this.cartPaymentAccount = account.payment_account_id;
                this.$forceUpdate();
            },
            checkForErrorTextDisplay: function(value)
            {
                if (typeof value === "undefined" || value === null || value === "")
                {
                    return false;
                }
                
                return true;
            },
            clearError: function(field)
            {
                this.errorText[field] = null;
                this.$forceUpdate();
            },
            createNewCardForCustomer: function()
            {
                const card = this.newCardForCreate;
            
                if (!this.dataValidation(card, {
                    name: "required",
                    number: "required",
                    expMonth: "required",
                    expYear: "required",
                    cvc: "required",
                    line1: "required",
                    city: "required",
                    state: "required",
                    zip: "required",
                    country: "required",
                }))
                {
                    return;
                }

                const self = this;
                modal.EngageFloatShield();
                const url = "'.$app->objCustomPlatform->getFullPortalDomain().'/cart/register-credit-card-with-user?id=" + this.cardEntity.user_id;
                
                ajax.PostExternal(url, card, true, function(result) 
                {
                    if (result.success === false || (typeof result.response !== "undefined" && result.response.success === false))
                    {
                        let data = {title: "Credit Card Error", html: result.response.message};
                        modal.EngagePopUpAlert(data, function() {
                            modal.CloseFloatShield();
                        }, 350, 115);
                        return;
                    }
                    
                    let paymentAccount = (typeof result.response !== "undefined" ? result.response.data : result.data);
                    self.cartPaymentAccount = paymentAccount.payment_account_id;

                    self.customerPaymentAccounts.push(paymentAccount);
                    self.toggleCreateNewCartPaymentAccount = false;
                    
                    self.clearNewCard();
                    self.$forceUpdate();
                });
            },
            renderCardType: function(account)
            {
                if (typeof account.type === "undefined" || account.type === null)
                {
                    account.type = "other";
                }
                
                return "' . $app->objCustomPlatform->getFullPortalDomain() . '/_ez/images/financials/cc_small_" + account.type + ".png";
            },
            clearNewCard: function()
            {
                this.newCardForCreate = {name:"", number: "", expMonth: "", expYear: "", cvc: "", line1: "",  line2: "",  state: "", zip: "", country: ""};
                this.errorText = [];
            },
            backToSelectPaymentAccount: function()
            {
                this.toggleCreateNewCartPaymentAccount = false;
            },
            testCreditCard: function(number)
            {
                const types = Object.entries(this.creditCardTest);
                const self = this;
                this.clearError(number);
                self.creditCartType = "other";
                let matched = false;
                
                ezLog(number, "Testing Number!");
                
                types.forEach(function([index, currType])
                {
                    var p = new RegExp(currType);
                    if (p.test(parseFloat(number)) && matched === false) 
                    {
                        matched = true;
                        self.creditCartType = index;
                        self.newCardForCreate.type = index;
                    }
                });
                
                this.$forceUpdate();
            },
            dataValidation: function(entity, validation)
            {
                let validForm = true;

                for (let currValidationField in validation)
                {
                    if (typeof entity[currValidationField] !== "undefined")
                    {
                        let currRules = validation[currValidationField];
                        let currEntityField = entity[currValidationField];
                        let currValidationResult = this.validateRules(currEntityField, currValidationField, currRules, entity);

                        if (currValidationResult.success == false)
                        {
                            validForm = false;
                            this.errorText[currValidationField] = currValidationResult.error;
                        }
                    }
                }
                
                this.$forceUpdate();

                return validForm;
            },
            validateRules: function(entityValue, validationField, rules, entity)
            {
                let colRules = rules.split("|");

                for (let currRule of colRules)
                {
                    if (currRule.includes(":"))
                    {
                        let conditionalRule = currRule.split(":");

                        switch (conditionalRule[0])
                        {
                            case "sameAs":
                                if (entity[conditionalRule[1]] && entity[conditionalRule[1]] !== entityValue)
                                {
                                    return this.validationObject(false, validationField, entityValue, this.ucWords(validationField) + " needs to match " + ucWords(conditionalRule[1]) + ".");
                                }
                                break;
                        }
                    }
                    else
                    {
                        switch(currRule)
                        {
                            case "required":
                                if (typeof entityValue === "undefined" || entityValue === null|| entityValue === "")
                                {
                                    return this.validationObject(false, validationField, entityValue, this.ucWords(validationField) + " cannot be blank.");
                                }
                                break;
                            case "passwordComplex":
                                if (!this.isComplexPassword(entityValue))
                                {
                                    return this.validationObject(false, validationField, entityValue, this.ucWords(validationField) + " isn\'t complex enough.");
                                }
                                break;
                            case "email":
                                if (!this.isEmail(entityValue))
                                {
                                    return this.validationObject(false, validationField, entityValue, this.ucWords(validationField) + " isn\'t an email address.");
                                }
                                break;
                        }
                    }
                }

                return this.validationObject(true, validationField, entityValue, "Passes validation.");
            },
            ucWords: function(text)
            {
                return text.replace(/_/g," ").replace(/\w\S*/g, function (txt) {
                    return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
                });
            },
            validationObject: function(result, name, value, message)
            {
                return {success: result, name: name, value: value, error: message};
            },
            assignPaymentAccount: function()
            {
                for(let currAccount of this.customerPaymentAccounts)
                {
                    if (currAccount.payment_account_id === this.cartPaymentAccount)
                    {
                        this.processPaymentAccountUpdate(currAccount);
                        
                    }
                }
            },
            processPaymentAccountUpdate: function(currAccount) 
            {
                let self = this;
                const url = "'.$app->objCustomPlatform->getFullPortalDomain().'/cart/register-payment-account-with-card?id=" + this.cardEntity.card_id;
                const card = {payment_account_id: currAccount.payment_account_id}
                
                ajax.PostExternal(url, card, true, function(result) 
                {
                    if (result.success === false || (typeof result.response !== "undefined" && result.response.success === false))
                    {
                        let data = {title: "Unable To Assign Payment Account", html: result.response.message};
                        modal.EngagePopUpAlert(data, function() {
                            modal.CloseFloatShield();
                        }, 350, 115);
                        return;
                    }
                    
                    self.cardOriginal.PaymentAccount.payment_account_id = currAccount.payment_account_id;
                    self.cardOriginal.PaymentAccount.display_1 = currAccount.display_1;
                    self.cardOriginal.PaymentAccount.display_2 = currAccount.display_2;
                    self.cardOriginal.PaymentAccount.type = currAccount.type;
                    self.cardOriginal.PaymentAccount.method = currAccount.method;
                    self.cardOriginal.PaymentAccount.sys_row_id = currAccount.sys_row_id;
                     
                    let objModal = self.findModal(self);                 
                    objModal.close();  
                });
                
            },
        ';
    }

    protected function renderComponentHydrationScript (): string
    {
        return parent::renderComponentHydrationScript() . '
            let self = this;
            this.cardEntity = _.clone(props.entity);
            this.cardOriginal = props.entity;
            this.getPaymentAccounts();
        ';
    }

    protected function renderComponentComputedValues (): string
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

    protected function renderTemplate (): string
    {
        global $app;
        return '
        <div class="cardPaymenyHistoryWidget">
            <v-style type="text/css">
                .cardPaymenyHistoryWidget .selected-payment-account-list td > .add-new-account {
                    position: relative;
                    padding-left: 40px;
                }
                .cardPaymenyHistoryWidget .selected-payment-account-list td > .add-new-account:before {
                    display: block;
                    width: 22px;
                    height: 22px;
                    background: url(\'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAASCAYAAABWzo5XAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAxdJREFUeNqcVL9vHEUYfTM7u3eXvcN3/nFxsM6XRAgJyyYyChISdBQgxWAEFDRuESkSqqQCKfwDNOmTIkqqSCQGCxASDVUEtowiEKGwZNnyjwCOfdi3vt2dGd7s3tkmFg0jfVrtvO+9efN9367ApXs4tiwmAt+7UAq8874nm0LAS1KzEsV6oZPob5jwANzMc232EP8Ssrb/RMH/tN5XvFgrFwoONl1ISQGrDZ60Y2xuRzfbneQzii0fF7I4PdRX/HJ0oDzuKQ8poXo5QKWgMni9tY92rKFoRGuN1b/21ij4FokLDpddJzWKzJ2pV8YFRbT0EFmBD19p4M4H41m8WPWxxz2HCc9Dc6jy7MlqadYZyIVoLSwG15qD5TEj+cokUAy+gnPmruRCMtyeYLgcwxo1BsKRShhcpxiZQowNV0sfSZKsoEBPSJEAcVg+Eq2n4A5zjiRxxThVPTElpHhdlQpquhYWgphVjUjNuCTt0WxyROjPSOP3zZh3YI5OEbANo6HCQBigXPTfU2GgXnadafaX8Orpflie6IzGFHm+FhwIXRirY/AkW+CqSqF4P8Gdn1ax5a7tiUkVKNnsGIuxehlXXmvgv9bM+RHMHN0wGt8urmL579gJDzl9D/9rifzi+WAKlWiz4ktxbmkrwu2fN7GTWETasrgSb5ztw8RgMaN9/dsf+GVHk8caGYO4k2Bb5wPItaU4ZPO+wNSjx3tYfNzGo21aNbnR8N3nDoRuPFjB3SViHolxAqQMYXtfxEPZ7qRzrSi2Rdd5N+6WpXfD7BkEvUSukpJ5x9wZDncjbnJ3FJqV1tof17ejW26jE6dAQr+peyZ50kFxbb7HjvEbOQxjfiD6lRtItNrJJ2tP2hsZ4JKdEEWDwzGCJ7pCPTzNBHcp9DHhWODy/bwHQrwUFv17u6ltZNPNA14YruDUM4UM/3WthY3dOP9t5G5ajPcJfZf3sCvU/QOchRSfw5PTbiihbbcpzpLMi+uuq833FLzKmO9R1VOjsUTwHaT6TQjzNt8n6WwoQ7TeouhD4nN8/8JV7Sj1HwEGAGXUUvW2OWvwAAAAAElFTkSuQmCC\') no-repeat center center / 100%;
                    position: absolute;
                    content: " ";
                    top: 2px;
                    left: 4px;
                }
                .cardPaymenyHistoryWidget .selected-payment-account-list td:not(.new-payment-account) {
                    padding: 0;
                }
                .cardPaymenyHistoryWidget .selected-payment-account-list label {
                    margin-left:25px;
                    margin-bottom: 0;
                    padding: .75rem;
                    width:100%;
                    height:100%;
                }
                .cardPaymenyHistoryWidget .create-new-payment-account-box > div {
                    margin-bottom:10px;
                }
                .cardPaymenyHistoryWidget .create-new-payment-account-box > div.inlineBlock > select {
                    display:inline-block;
                    width:50%;
                }
                .cardPaymenyHistoryWidget .create-new-payment-account-box > div.inlineBlock > .inlineBlockChild {
                    display:inline-block;
                    width:50%;
                }
                .cardPaymenyHistoryWidget .create-new-payment-account-box .billing-account-title { 
                    font-size: 18px;
                    margin: 25px 0 10px;
                }
                .cardPaymenyHistoryWidget .create-new-payment-account-box .csv-number { 
                    width:150px;
                }
            </v-style>' . '
            <div v-if="cardEntity !== null">
                <div v-if="toggleCreateNewCartPaymentAccount === false">
                    <p>Select an existing payment account to assign to this card, or create a new one!</p>
                    <table class="selected-payment-account-list table table-striped no-top-border table-shadow" style="box-shadow:rgba(0,0,0,.3) 0 0 10px;">
                        <tbody>
                        <tr v-for="currCard in customerPaymentAccounts" class="pointer">
                            <td v-on:click="selectCartPaymentAccount(currCard)">
                                <label v-bind:for="\'customer-payment-account-\' + currCard.payment_account_id">
                                    <input v-bind:id="\'customer-payment-account-\' + currCard.payment_account_id" name="cart-customer-payment-account" v-bind="cartPaymentAccount" :value="currCard.payment_account_id" class="form-check-input" type="radio" /> <img v-bind:src="renderCardType(currCard)" width="40" height="25" /> {{ currCard.display_1 }} {{ currCard.display_2 }}
                                </label>
                            </td>
                        </tr>
                        <tr class="pointer" v-on:click="createNewCartPaymentAccount" style="background: none;">
                            <td class="new-payment-account">
                                <div class="add-new-account">
                                    Add New Payment Account
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    
                    <button class="btn btn-primary w-100" v-on:click="assignPaymentAccount">Assign Payment Account</button>
                </div>
                <div v-if="toggleCreateNewCartPaymentAccount === true" class="selected-payment-account-checkout">
                    <h4 style="margin-bottom: 7px;"><a v-on:click="backToSelectPaymentAccount" id="back-to-entity-list" class="back-to-entity-list pointer" style=""></a> Create New Payment Account</h4>
                    <p>Create a new payment and assign it to your card.</p>
                    <div class="create-new-payment-account">
                        <div class="create-new-payment-account-box">
                            <h5 class="billing-account-title"><span class="ico ico-billing"></span> Credit Card Details</h5>
                            <div>
                                <input v-on:blur="clearError(\'name\')" v-model="newCardForCreate.name" type="text" class="form-control" placeholder="Name on Card">
                                <div v-if="checkForErrorTextDisplay(errorText.name)" class="field-validation-error">{{ errorText.name }}</div>
                            </div>
                            <div>
                                <input v-on:keyup="testCreditCard(newCardForCreate.number)" v-on:blur="testCreditCard(newCardForCreate.number);" v-model="newCardForCreate.number" type="text" class="form-control" placeholder="Card Number" style="width: calc(100% - 50px); display: inline-block;">
                                <img v-bind:src="\'' . $app->objCustomPlatform->getFullPortalDomain() . '/_ez/images/financials/cc_small_\' + creditCartType + \'.png\'" style="position: relative; top: -3px; right: -5px;"/>
                                <div v-if="checkForErrorTextDisplay(errorText.number)" class="field-validation-error">{{ errorText.number }}</div>
                            </div>
                            <div class="inlineBlock">
                                <select v-on:blur="clearError(\'expMonth\')" v-model="newCardForCreate.expMonth" class="form-control" style="width:100px;">
                                    <option value="">Month</option>
                                    <option value="01">01</option>
                                    <option value="02">02</option>
                                    <option value="03">03</option>
                                    <option value="04">04</option>
                                    <option value="05">05</option>
                                    <option value="06">06</option>
                                    <option value="07">07</option>
                                    <option value="08">08</option>
                                    <option value="09">09</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                </select>
                                <select v-on:blur="clearError(\'expYear\')" v-model="newCardForCreate.expYear" class="form-control" style="width:100px;margin-left: 5px;">
                                    <option value="">Year</option>
                                    <option value="2020">2020</option>
                                    <option value="2021">2021</option>
                                    <option value="2022">2022</option>
                                    <option value="2023">2023</option>
                                    <option value="2024">2024</option>
                                    <option value="2025">2025</option>
                                    <option value="2026">2026</option>
                                    <option value="2027">2027</option>
                                    <option value="2028">2028</option>
                                </select>
                                <div v-if="checkForErrorTextDisplay(errorText.expMonth)" class="field-validation-error">{{ errorText.expMonth }}</div>
                                <div v-if="checkForErrorTextDisplay(errorText.expYear)" class="field-validation-error">{{ errorText.expYear }}</div>
                            </div>
                            <div>
                                <input v-on:blur="clearError(\'cvc\')" v-model="newCardForCreate.cvc" type="text" class="form-control csv-number" placeholder="CSV Number">
                                <div v-if="checkForErrorTextDisplay(errorText.cvc)" class="field-validation-error">{{ errorText.cvc }}</div>
                            </div>
                        </div>'.'                        
                        <div class="create-new-payment-account-box">
                            <h5 class="billing-account-title"><span class="ico ico-home"></span> Billing Address</h5>
                            <div>
                                <input v-on:blur="clearError(\'line1\')" v-model="newCardForCreate.line1" type="text" class="form-control" placeholder="Address Line 1">
                                <div v-if="checkForErrorTextDisplay(errorText.line1)" class="field-validation-error">{{ errorText.line1 }}</div>
                            </div>
                            <div>
                                <input v-model="newCardForCreate.line2" type="text" class="form-control" placeholder="Address Line 2">
                            </div>
                            <div class="inlineBlock">
                                <div class="inlineBlockChild" style="width:calc(75% - 10px);padding-right:8px;">
                                    <input v-on:blur="clearError(\'city\')" v-model="newCardForCreate.city" type="text" class="form-control" placeholder="City">
                                    <div v-if="checkForErrorTextDisplay(errorText.city)" class="field-validation-error">{{ errorText.city }}</div>
                                </div>
                                <div class="inlineBlockChild" style="width:25%;padding-right:8px;">
                                    <input v-on:blur="clearError(\'state\')" v-model="newCardForCreate.state" type="text" class="form-control" placeholder="State">
                                    <div v-if="checkForErrorTextDisplay(errorText.state)" class="field-validation-error">{{ errorText.state }}</div>
                                </div>
                            </div>
                            <div>
                                <input v-on:blur="clearError(\'zip\')" v-model="newCardForCreate.zip" type="text" class="form-control" placeholder="Postal Code">
                                <div v-if="checkForErrorTextDisplay(errorText.zip)" class="field-validation-error">{{ errorText.zip }}</div>
                            </div>
                            <div>
                                <input v-on:blur="clearError(\'country\')" v-model="newCardForCreate.country" type="text" class="form-control" placeholder="Country">
                                <div v-if="checkForErrorTextDisplay(errorText.country)" class="field-validation-error">{{ errorText.country }}</div>
                            </div>
                            <div>
                                <button v-on:click="createNewCardForCustomer" class="buttonID23542445 btn btn-primary w-100"">Add New Payment Account</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <div>
        ';
    }
}