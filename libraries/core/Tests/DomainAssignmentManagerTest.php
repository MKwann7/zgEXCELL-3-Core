<?php

namespace App\Core\Tests;

use App\Core\App;
use App\Core\DomainAssignmentManager;
use App\Utilities\Excell\ExcellCollection;
use App\Utilities\Transaction\ExcellTransaction;
use Entities\Cards\Classes\CardDomains;
use Entities\Cards\Classes\Cards;
use Entities\Cards\Models\CardDomainModel;
use Entities\Cards\Models\CardModel;
use Entities\Companies\Classes\Companies;
use Entities\Companies\Classes\CompanySettings;
use Entities\Companies\Models\CompanyModel;
use PHPUnit\Framework\TestCase;

class DomainAssignmentManagerTest extends TestCase
{
    const HTTPS_PROTOCOL = "https://";
    const HTTP_PROTOCOL = "http://";

    public function testLoadWhiteLabelFromDb_Success() : void
    {
        $appMock = $this->getMockBuilder(App::class)
            ->disableOriginalConstructor()
            ->getMock();
        $companiesMock = $this->getMockBuilder(Companies::class)
            ->disableOriginalConstructor()
            ->getMock();
        $companiesSettingsMock = $this->getMockBuilder(CompanySettings::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cardDomains = $this->getMockBuilder(CardDomains::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cards = $this->getMockBuilder(Cards::class)
            ->disableOriginalConstructor()
            ->getMock();
        $companyTransaction = $this->getMockBuilder(ExcellTransaction::class)
            ->disableOriginalConstructor()
            ->getMock();
        $companyCollection = $this->getMockBuilder(ExcellCollection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $companyModelMock = new CompanyModel($this->getCompanyMock());

        $companyCollection->method("first")->willReturn($companyModelMock);
        $companyTransaction->method("getData")->willReturn($companyCollection);
        $companiesMock->method("getWhere")->willReturn($companyTransaction);
        $serverMock = $this->getMockServer();

        $domainAssignmentManager = new DomainAssignmentManager($appMock, $companiesMock, $companiesSettingsMock, $cardDomains, $cards, $serverMock);
        $result = $domainAssignmentManager->assignCustomPlatform();

        $this->assertEquals(true, $result);
        $this->assertEquals($this->getCompanyMock()["company_id"], $domainAssignmentManager->getCustomPlatform()->getCompanyId());
        $this->assertEquals($this->getCompanyMock()["domain_public"], $domainAssignmentManager->getPublicDomain()->getDomain());
        $this->assertEquals($this->getCompanyMock()["domain_public_name"], $domainAssignmentManager->getPublicDomain()->getName());
        $this->assertEquals($this->getCompanyMock()["domain_portal"], $domainAssignmentManager->getPortalDomain()->getDomain());
        $this->assertEquals($this->getCompanyMock()["domain_portal_name"], $domainAssignmentManager->getPortalDomain()->getName());
    }

    public function testLoadWhiteLabelFromSession_Success() : void
    {
        $appMock = $this->getMockBuilder(App::class)
            ->disableOriginalConstructor()
            ->getMock();
        $appMock->method("getAppSession")->willReturn($this->loadAppSession());

        $companiesMock = $this->getMockBuilder(Companies::class)
            ->disableOriginalConstructor()
            ->getMock();
        $companiesSettingsMock = $this->getMockBuilder(CompanySettings::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cardDomains = $this->getMockBuilder(CardDomains::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cards = $this->getMockBuilder(Cards::class)
            ->disableOriginalConstructor()
            ->getMock();

        $serverMock = $this->getMockServer();

        $domainAssignmentManager = new DomainAssignmentManager($appMock, $companiesMock, $companiesSettingsMock, $cardDomains, $cards, $serverMock);
        $result = $domainAssignmentManager->assignCustomPlatform();

        $this->assertEquals(true, $result);
        $this->assertEquals($this->getCompanyMock()["company_id"], $domainAssignmentManager->getCustomPlatform()->getCompanyId());
        $this->assertEquals($this->getCompanyMock()["domain_public"], $domainAssignmentManager->getPublicDomain()->getDomain());
        $this->assertEquals(self::HTTPS_PROTOCOL . $this->getCompanyMock()["domain_public"], $domainAssignmentManager->getPublicDomain()->getDomainFull());
        $this->assertEquals($this->getCompanyMock()["domain_public_name"], $domainAssignmentManager->getPublicDomain()->getName());
        $this->assertEquals($this->getCompanyMock()["domain_portal"], $domainAssignmentManager->getPortalDomain()->getDomain());
        $this->assertEquals(self::HTTPS_PROTOCOL . $this->getCompanyMock()["domain_portal"], $domainAssignmentManager->getPortalDomain()->getDomainFull());
        $this->assertEquals($this->getCompanyMock()["domain_portal_name"], $domainAssignmentManager->getPortalDomain()->getName());
        $this->assertEquals($this->loadAppSession()["Core"]["App"]["DomainType"], $domainAssignmentManager->getPublicDomain()->getType());
    }

    public function testLoadCustomDomainFromSession_Success() : void
    {
        $appMock = $this->getMockBuilder(App::class)
            ->disableOriginalConstructor()
            ->getMock();
        $appMock->method("getAppSession")->willReturn($this->loadAppSessionWithCard());

        $companiesMock = $this->getMockBuilder(Companies::class)
            ->disableOriginalConstructor()
            ->getMock();
        $companiesSettingsMock = $this->getMockBuilder(CompanySettings::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cardDomains = $this->getMockBuilder(CardDomains::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cards = $this->getMockBuilder(Cards::class)
            ->disableOriginalConstructor()
            ->getMock();

        $serverMock = $this->getMockServer();

        $domainAssignmentManager = new DomainAssignmentManager($appMock, $companiesMock, $companiesSettingsMock, $cardDomains, $cards, $serverMock);
        $result = $domainAssignmentManager->assignDomainName();

        $this->assertEquals(true, $result);
        $this->assertEquals($this->getCompanyMock()["company_id"], $domainAssignmentManager->getCustomPlatform()->getCompanyId());
        $this->assertEquals($this->getCardDomainMock()["domain_name"], $domainAssignmentManager->getPublicDomain()->getDomain());
        $this->assertEquals(self::HTTPS_PROTOCOL . $this->getCardDomainMock()["domain_name"], $domainAssignmentManager->getPublicDomain()->getDomainFull());
        $this->assertEquals($this->getCardDomainMock()["card"]["card_name"], $domainAssignmentManager->getPublicDomain()->getName());
        $this->assertEquals($this->getCompanyMock()["domain_portal"], $domainAssignmentManager->getPortalDomain()->getDomain());
        $this->assertEquals(self::HTTPS_PROTOCOL . $this->getCompanyMock()["domain_portal"], $domainAssignmentManager->getPortalDomain()->getDomainFull());
        $this->assertEquals($this->getCompanyMock()["domain_portal_name"], $domainAssignmentManager->getPortalDomain()->getName());
        $this->assertEquals($this->loadAppSessionWithCard()["Core"]["App"]["DomainType"], $domainAssignmentManager->getPublicDomain()->getType());
    }

    public function testLoadCustomDomainFromDb_Success() : void
    {
        $appMock = $this->getMockBuilder(App::class)
            ->disableOriginalConstructor()
            ->getMock();
        $companiesMock = $this->getMockBuilder(Companies::class)
            ->disableOriginalConstructor()
            ->getMock();
        $companiesSettingsMock = $this->getMockBuilder(CompanySettings::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cardDomains = $this->getMockBuilder(CardDomains::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cards = $this->getMockBuilder(Cards::class)
            ->disableOriginalConstructor()
            ->getMock();
        $companyTransaction = $this->getMockBuilder(ExcellTransaction::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cardDomainTransaction = $this->getMockBuilder(ExcellTransaction::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cardTransaction = $this->getMockBuilder(ExcellTransaction::class)
            ->disableOriginalConstructor()
            ->getMock();
        $companyCollection = $this->getMockBuilder(ExcellCollection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cardDomainCollection = $this->getMockBuilder(ExcellCollection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cardCollection = $this->getMockBuilder(ExcellCollection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $companyModelMock = new CompanyModel($this->getCompanyMock());
        $companyCollection->method("first")->willReturn($companyModelMock);
        $companyTransaction->method("getData")->willReturn($companyCollection);
        $companiesMock->method("getById")->willReturn($companyTransaction);

        $cardDomainModelMock = new CardDomainModel($this->getCardDomainMock());
        $cardModelMock = new CardModel($this->getCardMock());
        $cardDomainCollection->method("first")->willReturn($cardDomainModelMock);
        $cardCollection->method("first")->willReturn($cardModelMock);
        $cardDomainTransaction->method("getData")->willReturn($cardDomainCollection);
        $cardTransaction->method("getData")->willReturn($cardCollection);
        $cardDomains->method("getWhere")->willReturn($cardDomainTransaction);
        $cards->method("getById")->willReturn($cardTransaction);
        $serverMock = $this->getMockServer();

        $domainAssignmentManager = new DomainAssignmentManager($appMock, $companiesMock, $companiesSettingsMock, $cardDomains, $cards, $serverMock);
        $result = $domainAssignmentManager->assignDomainName();

        $this->assertEquals(true, $result);
        $this->assertEquals($this->getCompanyMock()["company_id"], $domainAssignmentManager->getCustomPlatform()->getCompanyId());
        $this->assertEquals($this->getCardMock()["card_name"], $domainAssignmentManager->getPublicDomain()->getName());
        $this->assertEquals($this->getCompanyMock()["domain_portal"], $domainAssignmentManager->getPortalDomain()->getDomain());
        $this->assertEquals($this->getCompanyMock()["domain_portal_name"], $domainAssignmentManager->getPortalDomain()->getName());
        $this->assertEquals($this->getCardDomainMock()["domain_name"], $domainAssignmentManager->getPublicDomain()->getDomain());
        $this->assertEquals($this->getCardDomainMock()["ssl"], $domainAssignmentManager->getPublicDomain()->getSsl());
        $this->assertEquals($this->getCardDomainMock()["type"], $domainAssignmentManager->getPublicDomain()->getType());
        $this->assertEquals($this->getCardDomainMock()["card_id"], $domainAssignmentManager->getPublicDomain()->getCardId());
    }

    protected function getMockServer() : array
    {
        return [
            "HTTP_HOST" => "excell.test"
        ];
    }

    protected function getCompanyMock() : array
    {
        return [
            "company_id" => 12345,
            "domain_public" => "excell.test",
            "domain_public_ssl" => true,
            "domain_public_name" => "Excell Core",
            "domain_portal" => "admin.excell.test",
            "domain_portal_ssl" => true,
            "domain_portal_name" => "Excell Admin"
        ];
    }

    protected function getCardDomainMock() : array
    {
        return [
            "card_domain_id" => 12345,
            "card_id" => 12345,
            "domain_name" => "myself.test",
            "ssl" => true,
            "type" => "card",
            "card" => $this->getCardMock()
        ];
    }

    protected function getCardMock() : array
    {
        return [
            "card_id" => 12345,
            "card_owner" => 12345,
            "card_user_id" => 12345,
            "company_id" => 0,
            "card_type_id" => 1,
            "status" => "active",
            "template_card" => 0,
            "template_id" => 1,
            "card_vanity_url" => "myself",
            "card_num" => 2345,
            "card_name" => "myself.test"
        ];
    }

    protected function loadAppSessionWithCard() : array
    {
        $defaultSession = $this->loadAppSession();
        $defaultSession["Core"]["App"]["Domain"]["Web"] = "myself.test";
        $defaultSession["Core"]["App"]["DomainType"] = "card";
        $defaultSession["Core"]["App"]["CardDomain"] = $this->getCardDomainSession();

        return $defaultSession;
    }

    protected function loadAppSession() : array
    {
        return [
            "Core" => [
                "App" => [
                    "Domain" => [
                        "Web" => "excell.test",
                        "Portal" => "admin.excell.test",
                        "Web_SSL" => true,
                        "Portal_SSL" => true,
                    ],
                    "WhiteLabel" => $this->getCompanyMock(),
                    "DomainType" => "app",
                ]
            ]
        ];
    }

    protected function getCardDomainSession() : array
    {
        return [
            "card_domain_id" => 12345,
            "card_id" => 12345,
            "ssl" => true,
            "type" => "card",
            "domain_name" => "myself.test",
            "card" => $this->getCardMock()
        ];
    }
}