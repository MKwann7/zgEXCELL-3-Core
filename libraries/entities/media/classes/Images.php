<?php

namespace Entities\Media\Classes;

use App\Core\AppEntity;
use App\Utilities\Excell\ExcellCollection;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Media\Models\ImageModel;
use Slim;

class Images extends AppEntity
{
    public string $strEntityName       = "Media";
    public $strDatabaseTable    = "image";
    public $strDatabaseName     = "Media";
    public $strMainModelName    = ImageModel::class;
    public $strMainModelPrimary = "image_id";

    public function uploadBase64ImageToMediaServer(
        string $base64,
        string $uuid,
        int $userId,
        int $entityId,
        string $entityName,
        string $imageClass = "images",
        ?ImageModel $parentImage = null
    ) : ExcellTransaction
    {
        $imageResult = $this->decodeBase64ImageStringToLocalFile($base64);

        if ($imageResult->result->Success === false) {
            return $imageResult;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file(
            $finfo,
            $imageResult
                ->getData()
                ->first()
                ->getFullFileName()
        );

        if (
            !in_array(strtolower($imageResult->getData()->first()->getFileExtension()), ["gif", "jpeg", "jpg", "png","svg"])
            && ($mime != "image/gif")
            && ($mime != "image/jpeg")
            && ($mime != "image/pjpeg")
            && ($mime != "image/x-png")
            && ($mime != "image/svg+xml")
            && ($mime != "image/png")
        )
        {
            return new ExcellTransaction(false, "You can only upload an image file: "  . $mime);
        }

        /** @var LocalFile $localFile */
        $localFile = $imageResult->getData()->first();
        $tempFilePathAndName = $localFile->getFullFileName();
        $userId = $this->app->getActiveLoggedInUser()->sys_row_id ?? "73a0d8b4-57e9-11ea-b088-42010a522005";
        $link = null;
        $result = null;

        require APP_VENDORS . "slim/main/v4.5.1/process/slim" . XT;

        $slim = new Slim($this->app, $userId);

        $fileName = $parentImage?->title ?? $localFile->getFileName();

        $imageFromServer = $slim->postFileToMediaServer($tempFilePathAndName, $fileName, $entityName, $entityId, $userId, $imageClass, $parentImage->image_id);

        if (!$imageFromServer) {
            new ExcellTransaction(false,"Unable to save the image", ["error" => $result]);
        }

        return new ExcellTransaction(true, "Upload Successful", (new ExcellCollection())->Add($imageFromServer));
    }

    public function decodeBase64ImageStringToLocalFile(string $data) : ExcellTransaction
    {
        $type = "txt";

        if (preg_match('/^data:image\/(\w+);base64,/', $data, $type))
        {
            $data = substr($data, strpos($data, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, gif

            if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png', 'svg' ]))
            {
                return new ExcellTransaction(false, 'invalid image type');
            }

            $data = base64_decode($data);

            if ($data === false)
            {
                return new ExcellTransaction(false, 'base64_decode failed');
            }
        }
        else
        {
            return new ExcellTransaction(false, 'did not match data URI with image data: ' . $data);
        }

        $localFile = new LocalFile(getGuid() . ".{$type}" );
        $localFile->addFileData($data);

        $colImageData = new ExcellCollection();
        $colImageData->Add($localFile);

        return new ExcellTransaction(true, 'base64_decode decoded', $colImageData);
    }

    public function buildImageBatchWhereClause($filterIdField = null, $filterEntity = null, int $typeId = 1) : string
    {
        $objWhereClause = $this->cardListPrimaryDataForDisplay($filterIdField, $filterEntity);

        if ($filterEntity !== null)
        {
            $objWhereClause .= "usr.{$filterIdField} = {$filterEntity} AND "; // 9 = card affiliate
        }

        $objWhereClause .= "img.image_class = 'images' GROUP BY(img.image_id) ORDER BY img.image_id DESC";

        return $objWhereClause;
    }

    public function buildLogoBatchWhereClause($filterIdField = null, $filterEntity = null, int $typeId = 1) : string
    {
        $objWhereClause = $this->cardListPrimaryDataForDisplay($filterIdField, $filterEntity);

        if ($filterEntity !== null)
        {
            $objWhereClause .= "usr.{$filterIdField} = {$filterEntity} AND "; // 9 = card affiliate
        }

        $objWhereClause .= "img.image_class = 'logos' GROUP BY(img.image_id) ORDER BY img.image_id DESC";

        return $objWhereClause;
    }

    private function cardListPrimaryDataForDisplay($filterIdField = null, $filterEntity = null)
    {
        $objWhereClause = "SELECT img.* FROM excell_media.image img ";

        if ($filterEntity !== null)
        {
            $objWhereClause .= "LEFT JOIN excell_main.user usr ON usr.user_id = img.user_id ";
        }

        //$objWhereClause .= "WHERE card.company_id = {$this->app->objCustomPlatform->getCompanyId()} AND card.status != 'Deleted' ";
        $objWhereClause .= "WHERE ";

        return $objWhereClause;
    }
}