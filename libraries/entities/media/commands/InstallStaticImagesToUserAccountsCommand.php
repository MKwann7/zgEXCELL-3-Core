<?php

namespace Entities\Media\Commands;

use App\Utilities\Command\Command;
use App\Utilities\Database;
use App\Utilities\Http\Http;
use Entities\Cards\Classes\CardPage;
use Entities\Media\Classes\Images;

class InstallStaticImagesToUserAccountsCommand extends Command
{
    public $name = "Media.FixBrokenImages";
    public $description = "Installs missing images.";

    /**
     * Executes the command
     */
    public function Run()
    {
        $this->MigrateTabImages(10000,1);
    }

    public function MigrateTabImages($pageLimit,$pageOffset) : bool
    {
        $pageOffset = ($pageOffset-1) * $pageLimit;

        //$lstV2CardPages = (new CardPagesModule())->getWhere(null, [$pageOffset, $pageLimit]);
        $lstV2CardPages = (new CardPage())->getWhere(["card_tab_id" => 149460]);

        if ($lstV2CardPages->Result->Count === 0)
        {
            return false;
        }

        $intBatchCount = 0;

        $arFileTypes = [];
        $arAllFileTypes = [];
        $arFileTypeCounts = [];

        foreach($lstV2CardPages->Data as $intV2CardsIndex => $objCardPage)
        {
            $intUserId = $objCardPage->user_id;

            $strContent = Database::forceUtf8(base64_decode($objCardPage->content));

            $arImagesFromTab = [];
            preg_match_all('!(https?:)?//\S+\.(?:jpe?g|jpg|png|gif)!Ui', $strContent, $arImagesFromTab);

            if (empty($arImagesFromTab[0]) || !\is_array($arImagesFromTab[0]) ||  \count($arImagesFromTab[0]) === 0)
            {
                continue;
            }

            $intProcessedImages = 0;

            foreach($arImagesFromTab[0] as $strTabUrl)
            {
                $arFileSplitOnPeriod = explode(".", $strTabUrl);
                $arFilePath = explode("/", $strTabUrl);
                $strFileExtension = mb_strtolower(end($arFileSplitOnPeriod));
                $strFileNameWithExtension = end($arFilePath);
                $strFileName = ucwords(str_replace([".","-","_"], " ", str_replace(".". $strFileExtension,"", str_replace("." . $strFileExtension, "",$strFileNameWithExtension))));

                if (strpos($strFileExtension, "?") !== false)
                {
                    $arFileExtension = explode("?", $strFileExtension);
                    $strFileExtension = $arFileExtension[0];
                }

                if (strpos($strFileExtension, "&") !== false)
                {
                    $arFileExtension = explode("&", $strFileExtension);
                    $strFileExtension = $arFileExtension[0];
                }

                if( strpos($strTabUrl, "ezcard.com") === false || strpos($strTabUrl, "app.ezcardmedia.com") !== false)
                {
                    continue;
                }

                $strFileType = false;

                switch(strtolower($strFileExtension))
                {
                    case 'jpg':
                    case 'jpeg':
                    case 'png':
                    case 'bmp':
                    case 'gif':
                        $strFileTyp = "image";
                        break;
                    case 'doc':
                    case 'docx':
                    case 'pptx':
                    case 'pdf':
                        $strFileTyp = "file";
                        break;
                    case 'mp3':
                        $strFileTyp = "music";
                        break;
                    default:
                        continue 2;
                }

                // check to see if exists in media server
                $link = $this->UploadFileToImagesServer($strFileType, $strTabUrl, $intUserId, $strFileName, $intBatchCount);

                if ($link === "")
                {
                    continue;
                }

                $strContent = str_replace($strTabUrl, $link, $strContent);
                $intProcessedImages++;

                $arFileTypes[$strFileExtension][] = $strFileExtension;
                $arFileTypeCounts[$strFileExtension] = count($arFileTypes[$strFileExtension]);
            }

            if ($intProcessedImages > 0)
            {
                $objCardPage->content = base64_encode($strContent);
                $result = (new CardPage())->update($objCardPage);
            }
        }

        return true;
    }

    protected function UploadFileToImagesServer($strFileType, $strOldImagePath, $intUserId, $strImageTitle, &$intBatchCount)
    {
        $lstCardImages = (new Images())->getWhere([["entity_name" => "user", "user_id" => $intUserId, "image_class" =>"editor", "title" => $strImageTitle]]);

        if ($lstCardImages->Result->Count > 0)
        {
            return $lstCardImages->Data->First()->url;
        }

        $intBatchCount++;

        if ($intBatchCount === 1000)
        {
            die("Finished batch of {$intBatchCount} Cards.");
        }

        $this->refactorOldImagePath($strOldImagePath);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $strOldImagePath);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.1 Safari/537.11');
        $objMainImage = curl_exec($ch);
        $rescode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $arFilePath = explode(".", $strOldImagePath);
        $strFileExtension = end($arFilePath);
        $strTempFileNameAndPath = AppStorage . 'uploads/'. sha1(microtime()) . "." . $strFileExtension;

        if ($objMainImage === false)
        {
            die('Curl error: ' . curl_error($ch));
        }

        if(!file_put_contents($strTempFileNameAndPath, $objMainImage)) {
            dd("unable to save file");
            return "";
        }

        try {
            $strPostUrl = "https://app.ezcardmedia.com/upload-image/users/" . $intUserId;
            $objHttp = new Http();
            $objFileForCurl = curl_file_create($strTempFileNameAndPath);
            $objHttpRequest = $objHttp->newRawRequest(
                "post",
                $strPostUrl,
                [
                    "file" => $objFileForCurl,
                    "user_id" => $intUserId,
                    "image_class" => "editor"
                ]
            )
                ->setOption(CURLOPT_CAINFO, '/etc/ssl/ca-bundle.crt')
                ->setOption(CURLOPT_SSL_VERIFYPEER, false);

            $objHttpResponse = $objHttpRequest->send();
        } catch(\Exception $ex)
        {
            unlink($strTempFileNameAndPath);
            dd("fail");
        }

        unlink($strTempFileNameAndPath);
        $result = json_decode($objHttpResponse->body);

        return $result->link;
    }

    private function refactorOldImagePath(&$oldImagePath)
    {
        $oldImagePath = str_replace("ezcard.com", "app.ezcardmedia.com", $oldImagePath);
    }
}