<?php

namespace App\Core;

use App\Utilities\Excell\ExcellModel;

class AppModel extends ExcellModel
{
    protected string $EntityName = "";
    protected string $ModelName = "";
    protected int $PropertyCount = 0;
    protected array $filterColumns = [];
    protected array $displayColumns = [];
    protected array $renderColumns = [];
    protected string $defaultSortColumn = "last_updated";
    protected string $defaultSortOrder = "ASC";

    public function __construct($data = null, $force = false)
    {
        $this->Hydrate($data, $force);
    }

    public function getDefinitions(): array
    {
        return $this->Definitions;
    }

    public function getDefinitionEmptyFieldNames() : array
    {
        $arFields = [];

        if (!empty($this->Definitions)) {
            foreach ($this->Definitions as $currDefFieldName => $currDef) {
                $arFields[$currDefFieldName] = '';
            }
        }

        return $arFields;
    }

    public function getModelName() : string
    {
        return $this->ModelName;
    }

    public function clearField($image) : self
    {
        unset($this->Properties[$image]);
        return $this;
    }

    public function getId() : ?int
    {
        $primaryKey = array_key_first($this->Definitions);
        return $this->Properties[$primaryKey];
    }

    public function ToPublicArray($arProperties = null, $collectionKeys = false): array
    {
        return $this->ToArray($arProperties, $collectionKeys);
    }

    public function getRenderColumns() : array
    {
        return $this->renderColumns;
    }

    public function getFilterColumns() : array
    {
        return $this->filterColumns;
    }

    public function getDisplayColumns() : array
    {
        return $this->displayColumns;
    }

    public function setDisplayColumns(array $columns) : self
    {
        $this->displayColumns = $columns;
        return $this;
    }

    public function setFilterColumns(array $columns) : self
    {
        $this->filterColumns = $columns;
        return $this;
    }

    public function setRenderColumns(array $columns) : self
    {
        $this->renderColumns = $columns;
        return $this;
    }

    public function getDefaultSortColumn() : string
    {
        return $this->defaultSortColumn;
    }

    public function getDefaultSortOrder() : string
    {
        return $this->defaultSortOrder;
    }

    public function setDefaultSortColumn(string $column, $order = "ASC") : self
    {
        $this->defaultSortColumn = $column;
        $this->defaultSortOrder = $order;
        return $this;
    }

    public function renderJsEmptyModel() : string
    {
        return json_encode_advanced($this->getDefinitionEmptyFieldNames());
    }
}