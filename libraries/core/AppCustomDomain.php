<?php

namespace App\Core;

class AppCustomDomain
{
    protected string $domain;
    protected string $domainFull;
    protected string $name;
    protected string $type;
    protected bool $ssl;
    protected int $cardId;

    public function __construct(string $domain, string $domainFull, bool $ssl, string $name = "", string $type = "")
    {
        $this->domain = $domain;
        $this->domainFull = $domainFull;
        $this->ssl = $ssl;
        $this->name = $name;

        if (!empty($type)) {
            $this->type = $type;
        }
    }

    public function getDomain() : string
    {
        return $this->domain;
    }

    public function getDomainFull() : string
    {
        if ($this->getSsl()) {
            return $this->getDomainFullWithSsl();
        }
        return $this->domainFull;
    }

    public function getDomainFullWithSsl() : string
    {
        return str_replace("http://", "https://", $this->domainFull);
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getSsl() : bool
    {
        return $this->ssl;
    }

    public function getType() : string
    {
        return $this->type ?? "";
    }

    public function getCardId() : ?int
    {
        return $this->cardId ?? null;
    }

    public function setCardId(int $cardId) : self
    {
        $this->cardId = $cardId;
        return $this;
    }

    public function toJson() : string
    {
        return json_encode([
            "domain" => $this->getDomain(),
            "domainFull" => $this->getDomainFull(),
            "ssl" => $this->getSsl(),
            "type" => $this->getType(),
            "cardId" => $this->getCardId() ?? "null",
        ]);
    }
}