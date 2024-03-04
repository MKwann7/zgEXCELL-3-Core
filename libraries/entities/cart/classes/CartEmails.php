<?php

namespace Entities\Cart\Classes;

use App\Core\App;
use App\Utilities\Http\Http;
use Entities\Products\Classes\ProductProcessor;
use Entities\Users\Classes\Users;
use Entities\Users\Models\UserModel;

class CartEmails
{
    private ProductProcessor $processor;
    private App $app;
    private ?UserModel $user;
    private ?UserModel $userSponsor;
    private string $customPlatformProductNotificationEmail;
    private string $customPlatformCompanyName;
    private string $customPlatformName;
    private string $companyName;
    private string $stripeAccountType;
    private string $customPlatformAdminUrl;
    private string $customerServiceEmail;
    private string $customerServicePhone;
    private int $customerServiceUserId;
    private UserModel $customerServiceUser;
    private \stdClass $processorData;
    private string $customPlatformProductNotificationEmailTitle;

    public function __construct ()
    {
        global $app;
        $this->app = $app;
    }

    public function loadProductProcessor(ProductProcessor $productProcessor) : void
    {
        $this->processor = $productProcessor;
        $this->user = $this->processor->user;
        $this->customerServiceUser = (new Users())->getFks(["user_phone", "user_email"])->getById($this->customerServiceUserId)->getData()->first();
        $this->userSponsor = (new Users())->getFks(["user_phone", "user_email"])->getById($this->user->sponsor_id)->getData()->first();
        $this->parsePurchase();
    }

    private function parsePurchase(): void
    {
        $cardTransaction = $this->processor->getCartProcessTransaction();
        $fee = (($cardTransaction->totalCartValue) *  0.0298662) + .3;

        $this->processorData = new \stdClass();
        $this->processorData->order_id = $cardTransaction->order->getId();
        $this->processorData->order_stripe_fee = number_format($fee, 2);
        $this->processorData->order_taxes = number_format(0.00,2);
        $this->processorData->order_subtotal = number_format($cardTransaction->totalCartValue, 2);
        $this->processorData->order_total = number_format(($cardTransaction->totalCartValue + $fee), 2);
    }

    private function instantiateCustomPlatformData(): void
    {
        $this->customPlatformCompanyName = $this->app->objCustomPlatform->getCompany()->company_name;
        $this->customPlatformName = $this->app->objCustomPlatform->getCompany()->platform_name;
        $this->stripeAccountType = $this->app->objCustomPlatform->getCompanySettings()->FindEntityByValue("label", "stripe_account_type")->value ?? "connected";
        $this->customPlatformProductNotificationEmail = $this->app->objCustomPlatform->getCompanySettings()->FindEntityByValue("label", "product_notification_email")->value ?? "noreply@ezdigital.com";
        $this->customPlatformProductNotificationEmailTitle = $this->app->objCustomPlatform->getCompanySettings()->FindEntityByValue("label", "product_notification_email_title")->value ?? "EZ Digital Product Support";
        $this->customPlatformAdminUrl = $this->app->objCustomPlatform->getFullPublicDomainName();
        $this->customerServiceEmail = $this->app->objCustomPlatform->getCompanySettings()->FindEntityByValue("label", "customer_support_email")?->value ?? "";
        $this->customerServicePhone = $this->app->objCustomPlatform->getCompanySettings()->FindEntityByValue("label", "customer_support_phone")?->value ?? "";
        $this->customerServiceUserId = $this->app->objCustomPlatform->getCompanySettings()->FindEntityByValue("label", "customer_support_user_id")->value ?? $this->app->getActiveLoggedInUser()->user_id;
    }

    public function sendEmails(): void
    {
        $this->instantiateCustomPlatformData();
        switch($this->stripeAccountType)
        {
            case "customer":
                $this->processEmailForCustomerCart();
                break;
           case "connected":
                $this->processEmailForConnectedCart();
                break;
        }
    }

    private function processEmailForCustomerCart() : void
    {
        $this->sendCustomerServiceEmail();
    }

    public function processEmailForConnectedCart() : void
    {
        $this->sendPurchaserWelcomeEmail();
        $this->sendCustomPlatformEmail();
//        $this->sendCustomerServiceEmail();
//        $this->sendFinancialEmail();
    }

    private function processCarots($string, UserModel $user, $processor) : string
    {
        $dateNow = date("F j, Y");
        $renewalDate = date("F j, Y", strtotime("+1 year"));

        return str_replace(
            ["[COMPANY_NAME]", "[CUSTOM_PLATFORM_PORTAL_URL]","[USER_ID]","[CLIENT_NAME]","[USER_NAME]","[FIRST_NAME]","[LAST_NAME]","[USER_EMAIL]","[USER_MOBILE]","[SPONSOR_ID]","[SPONSOR_FULL_NAME]","[ORDER_ID]","[ORDER_SUBTOTAL]","[ORDER_STRIPE_FEE]","[ORDER_TAXES]","[ORDER_TOTAL]","[CUSTOMER_SERVICE_USER_FULLNAME]","[CUSTOMER_SERVICE_EMAIL]","[CUSTOMER_SERVICE_MOBILE]","[EZDIGITAL_NOW_DATE]","[RENEWAL_DATE]"],
            [$this->customPlatformName, $this->customPlatformAdminUrl, $user->user_id, $user->first_name . " " . $user->last_name, $user->username, $user->first_name, $user->last_name, $user->user_email, $user->user_phone, $this->userSponsor->user_id, $this->userSponsor->first_name . " " . $this->userSponsor->last_name, $processor->order_id, $processor->order_subtotal, $processor->order_stripe_fee, $processor->order_taxes, $processor->order_total, ($this->customerServiceUser->first_name . " " . $this->customerServiceUser->last_name), $this->customerServiceEmail, $this->customerServicePhone, $dateNow, $renewalDate],
            $string);
    }

    private function sendPurchaserWelcomeEmail() : void
    {
        $objHttp = new Http();

        $deliveryEmail = $this->customPlatformProductNotificationEmailTitle . " <" . $this->customPlatformProductNotificationEmail . ">";
        $strTitle = $this->processCarots("Welcome to [COMPANY_NAME]", $this->user, $this->processorData);
        $strMessage = $this->processCarots($this->welcomeEmailString(), $this->user, $this->processorData);
        $destinationEmail = $this->user->first_name . " " . $this->user->last_name . " <" . $this->user->user_email . ">";
        $bcc_emails = $this->customerServiceUser->first_name . " " . $this->customerServiceUser->last_name . " <" . $this->customPlatformProductNotificationEmail . ">";

        try {

            $objHttpRequest = $objHttp->newRequest(
                "post",
                env("BL_API_IP") . "/api/v1/emails/send-cart-emails",
                [
                    "delivery_email" => base64_encode($deliveryEmail),
                    "destination_emails" => base64_encode($destinationEmail),
                    "bcc_email" => base64_encode($bcc_emails),
                    "subject_for_email" => base64_encode($strTitle),
                    "text_for_email" => base64_encode($strMessage)
                ]
            )
                ->setOption(CURLOPT_USERPWD, 'api:' . "mail_gun_key")
                ->setOption(CURLOPT_SSL_VERIFYPEER, false);

            $objHttpResponse = $objHttpRequest->send();

        } catch(\Exception $ex)
        {

        }
    }

    private function sendCustomPlatformEmail() : void
    {
        $objHttp = new Http();

        $strTitle = "This is the Custom Platform Email";
        $strMessage = "We can do this one as well, Jerry.";

        try {

            $objHttpRequest = $objHttp->newRequest(
                "post",
                env("BL_API_IP") . "/api/v1/emails/send-cart-emails",
                [
                    "subject_for_email" => base64_encode($strTitle),
                    "text_for_email" => base64_encode($strMessage)
                ]
            )
                ->setOption(CURLOPT_USERPWD, 'api:' . "mail_gun_key")
                ->setOption(CURLOPT_SSL_VERIFYPEER, false);

            $objHttpResponse = $objHttpRequest->send();

        } catch(\Exception $ex)
        {

        }
    }

    private function sendCustomerServiceEmail() : void
    {
        $objHttp = new Http();

        $strTitle = "This is the Customer Service Email";
        $strMessage = "Just what the doctor ordered, Jerry.";

        try {

            $objHttpRequest = $objHttp->newRequest(
                "post",
                env("BL_API_IP") . "/api/v1/emails/send-cart-emails",
                [
                    "subject_for_email" => base64_encode($strTitle),
                    "text_for_email" => base64_encode($strMessage)
                ]
            )
                ->setOption(CURLOPT_USERPWD, 'api:' . "mail_gun_key")
                ->setOption(CURLOPT_SSL_VERIFYPEER, false);

            $objHttpResponse = $objHttpRequest->send();

        } catch(\Exception $ex)
        {

        }
    }

    private function sendFinancialEmail() : void
    {
        $objHttp = new Http();

        $strTitle = "This is the Financial Email";
        $strMessage = "We need lots of finanices, Jerry.";

        try {

            $objHttpRequest = $objHttp->newRequest(
                "post",
                env("BL_API_IP") . "/api/v1/emails/send-cart-emails",
                [
                    "subject_for_email" => base64_encode($strTitle),
                    "text_for_email" => base64_encode($strMessage)
                ]
            )
                ->setOption(CURLOPT_USERPWD, 'api:' . "mail_gun_key")
                ->setOption(CURLOPT_SSL_VERIFYPEER, false);

            $objHttpResponse = $objHttpRequest->send();

        } catch(\Exception $ex)
        {

        }
    }

    private function welcomeEmailString() : string
    {
        switch($this->app->objCustomPlatform->getCompanyId())
        {
            case 4:
                return "Dear [CLIENT_NAME],<br><br>Thank you for joining [COMPANY_NAME]! You've made a great decision! You have also made your life a whole lot easier! To get you up and running here is your next steps.<br><ol><li>Please email <a href=\"mailto:[CUSTOMER_SERVICE_EMAIL]\"><span style='color:#1155CC'>[CUSTOMER_SERVICE_EMAIL]</a>\ and request a 15-minute appointment. We would love to get to know you better, and discover your goals for using your Max Card.</li><br><li>If you can, make a list of the things you want to add to your Max Card, and organize them by \"pages\" or \"sections,\" such as:<br><br><ul><li><u>MAIN HEADER IMAGE</u>(at the top of your Max Card when it opens)<br>Please Include Things Like the Following: <br>EXAMPLE (of your instructions to us)<br>\"Go to our website, which is <a href=\"https://ourwebsite.com\"><span style='color:#1155CC'>https://ourwebsite.com</a>, and use our company logo and colors, and please create a header image similar to the \"hero image\" you see on our website. I can tell you more during our 15-min appointment.\"<br><br></li><li><u>CONTACT BUTTON </u>(the 4 \"quick contact\" buttons below the header image)<br>Instructions: Tell us what you want connected to each button, such as ...<br>Button 1: Text<br>Button 2: Call<br>Button 3: Email<br>Button 4: Facebook<br>NOTE: These can be just about anything you like, and if you can, please provide the specific phone numbers, emails, and web links that are attached to each.<br><br></li><li><u>PAGE 1</u>(this is the first horizontal \"bar\" below the 4 contact buttons; when you click it, there is a \"drop down\" page that opens, with a Title on the bar itself, and content on the page which opens)<br>TITLE: About Us<br>CONTENT: You could say, \"Please use the content we have on our website in the About Us section,\" or \"Please include the paragraph of content that I put in this email, along with the picture of me / our staff that I have attached.\"<br><br></li><li><u>PAGE 2</u><br>TITLE: Make an Appointment<br>CONTENT: This is just an example, and can be anything you want. Whatever you need, just tell us.<br><br></li><li><u>PAGE 3</u><br>TITLE: Our Products &amp; Services<br>CONTENT: Whatever you need, just tell us.<br><br><li><u>DEFAULT PAGES</u><br>(Everybody also gets these 2 pages or \"horizontal bars\". You can move them around wherever you want and put them on the bottom, or on the top, or wherever you wish.)<br><br>Default Page 1: SHARE or SAVE this MAXCARD<br>Default Page 2: Get Your Own MAXCARD<br><br></li><li><u>PAGES 4, 5, 6, etc.</u><br>If you purchased the Bronze [COMPANY_NAME], you only purchased the 3 custom pages you see listed above the Default Pages. If you wish to purchase additional pages, those are available for purchase. Just let us know. If you purchased the Silver or Gold [COMPANY_NAME], you will have additional pages that are included in your plan. Please also start working on the Titles and Content that you wish to have on these additional pages. Thanks!<br><br></ul><li>You can log in to your account at [CUSTOM_PLATFORM_PORTAL_URL]/login<br>Username: [USER_NAME]</li></ol><br><br>Below are your account details. <br><br><br>Thanks again for your purchase! We look forward to meeting with you soon!<br><br>[CUSTOMER_SERVICE_USER_FULLNAME]<br>[CUSTOMER_SERVICE_EMAIL]<br>[CUSTOMER_SERVICE_MOBILE]<br><br>Date of purchase: [EZDIGITAL_NOW_DATE]<br>Next Renwal Date: [RENEWAL_DATE]<br>User Id: [USER_ID]<br>First Name: [FIRST_NAME]<br>Last Name: [LAST_NAME]<br>Email: [USER_EMAIL]<br>Mobile: [USER_MOBILE]<br><br><br>Order Number: [ORDER_ID]<br>[QUANTITY] [PRODUCT] [PRODUCT_PRICE] [ORDER_LINE_TOTAL]<br>Subtotal: [ORDER_SUBTOTAL]<br>Processing: [ORDER_STRIPE_FEE]<br>Tax: [ORDER_TAXES]<br>Order Total: [ORDER_TOTAL]<br>";
            case 6:
                return 'Welcome [CLIENT_NAME],<br><br>Thank you for your purchase today. To get you up and running here are your next steps:<br><br>step 1 Email <a href="mailto:info@akbranding.com" target="_blank">info@akbranding.com</a> your contact info and website domain address.<br>step 2 Email <a href="mailto:info@akbranding.com" target="_blank">info@akbranding.com</a> your business name | business info | logo in a .jpg format.<br>step 3 Email <a href="mailto:info@akbranding.com" target="_blank">info@akbranding.com</a> any content | photos | bios you would like added to your app.<br><br><br>Thank you,<br><br>[COMPANY_NAME]<br><br><br>Date of purchase: [EZDIGITAL_NOW_DATE]<br><br>User Id: [USER_ID]<br>First: [FIRST_NAME]<br>Last: [LAST_NAME]<br>Email: [user&#39;s email]<br>Mobile: [USER_EMAIL]<br><br>Originator Name: [SPONSOR_FULL_NAME]<br>Originator User Id: [SPONSOR_ID]<br><br>Order Number: [ORDER_ID]<br><br> Order line (n): [quantity] [order_line_id]|| [discount code] Product: [product title] || [coupon title] <br> Line total: [price] Renewal:[cycle_type] Renewal Total[<br><br> Order line (n): [order_line_id] ||[discount code] Product: [product title] || [coupon title] <br> Line total: [price] Renewal:[cycle_type] Renewal Cost: [renewal fee]<br><br><br>GROSS SALE: [ORDER_TOTAL]<br>STRIPE FEES: [ORDER_STRIPE_FEE]<br>TAX: [ORDER_TAXES]<br>';
        }

        return 'Welcome [CLIENT_NAME],<br><br>Thank you for your purchase today. To get you up and running here are your next steps:<br><br>step 1 Email <a href="mailto:info@akbranding.com" target="_blank">info@akbranding.com</a> your contact info and website domain address.<br>step 2 Email <a href="mailto:info@akbranding.com" target="_blank">info@akbranding.com</a> your business name | business info | logo in a .jpg format.<br>step 3 Email <a href="mailto:info@akbranding.com" target="_blank">info@akbranding.com</a> any content | photos | bios you would like added to your app.<br><br><br>Thank you,<br><br>[COMPANY_NAME]<br><br><br>Date of purchase: [EZDIGITAL_NOW_DATE]<br><br>User Id: [USER_ID]<br>First: [FIRST_NAME]<br>Last: [LAST_NAME]<br>Email: [user&#39;s email]<br>Mobile: [USER_EMAIL]<br><br>Originator Name: [SPONSOR_FULL_NAME]<br>Originator User Id: [SPONSOR_ID]<br><br>Order Number: [ORDER_ID]<br><br> Order line (n): [quantity] [order_line_id]|| [discount code] Product: [product title] || [coupon title] <br> Line total: [price] Renewal:[cycle_type] Renewal Total[<br><br> Order line (n): [order_line_id] ||[discount code] Product: [product title] || [coupon title] <br> Line total: [price] Renewal:[cycle_type] Renewal Cost: [renewal fee]<br><br><br>GROSS SALE: [ORDER_TOTAL]<br>STRIPE FEES: [ORDER_STRIPE_FEE]<br>TAX: [ORDER_TAXES]<br>';;
    }
}