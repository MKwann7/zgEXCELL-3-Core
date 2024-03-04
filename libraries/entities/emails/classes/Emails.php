<?php

namespace Entities\Emails\Classes;

use App\Core\AppEntity;
use App\Utilities\Http\Http;
use Entities\Emails\Models\EmailModel;

class Emails extends AppEntity
{
    public string $strEntityName       = "Emails";
    public $strDatabaseTable    = "email";
    public $strDatabaseName     = "Communication";
    public $strMainModelName    = EmailModel::class;
    public $strMainModelPrimary = "email_id";
    public $isPrimaryModule = true;

    const MAIL_GUN_URL = "https://api.mailgun.net/v3/";

    protected $strMainGunUrl    = "";
    protected $strMainGunDomain    = "mg.ezdigital.com";
    protected $strMainGunKey    = "";

    public function __construct ()
    {
        parent::__construct();
        $this->strMainGunKey = $this->app->objCustomPlatform->getCompanySettings()->FindEntityByValue("label","mailgun_key")->value ?? $this->app->getEnv("MAIL_GUN_KEY");
        $this->strMainGunUrl = self::MAIL_GUN_URL . $this->app->objCustomPlatform->getCompanySettings()->FindEntityByValue("label","mailgun_domain")->value ?? $this->strMainGunDomain;
    }

    public function SendEmail($arFrom, array $arTo, $strTitle, $strMessage, $attachments = [], $strEncoding = "text") : void
    {
        $objHttp = new Http();
        $msgArray = [];

        $header = [
            "from" => $arFrom,
            "to" => implode(",", $arTo),
            "subject" => $strTitle,
            "html" => $strMessage,
            "attachment" => $msgArray
        ];

        if (count($attachments) > 0) {
            foreach($attachments as $currAttachment) {
                $header["attachment"] = curl_file_create($currAttachment);
            }
        }

        try {

            $objHttpRequest = $objHttp->newRawRequest(
                "post",
                $this->strMainGunUrl . "/messages",
                $header
            )
                ->setOption(CURLOPT_USERPWD, 'api:' . $this->strMainGunKey)
                ->setOption(CURLOPT_SSL_VERIFYPEER, false);

            if (count($attachments) > 0) {
                $objHttpRequest->setHeader("Content-Type","multipart/form-data");
            }

            $objHttpResponse = $objHttpRequest->send();

        } catch(\Exception $ex)
        {
            dd($ex->getMessage());
        }
    }
}