<?php

namespace AcsNet\Api\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Data extends AbstractHelper
{
    const BASE_CONFIG_PATH = 'webapi/acsnet/';
    const BASE_VALUE_CLIENT_ID = '79735c7e-c2a8-4922-bce6-5271a902b014';
    const BASE_VALUE_CLIENT_SECRET = 'yn9v9CCTc7EyLhhBNf1WH0gc1yRkG7JYkeN2MNellJNKhdLLH7kTQDAIFHK7ie9bQWQdp7HAC7bsOwZ4';
    const BASE_VALUE_GRANT_TYPE = 'client_credentials';
    const BASE_VALUE_SCOPE = 'ecommerce-acsbr-ho';
    const BASE_VALUE_URL_TOKEN = 'https://login.sdasystems.org/connect/token';
    const BASE_VALUE_URL_ORDER = 'https://api-ecommerce-acs-br-staging.sdasystems.org/api/v1/order';

    protected $scopeConfig;

    public function __construct(
        Context              $context,
        ScopeConfigInterface $scopeConfig,
    )
    {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /*Método para abstrair, para não ficar repetindo muito o mesmo path*/
    /**
     * @param $value
     * @return string|null
     */
    private function getValue($value): ?string
    {
        return $this->scopeConfig->getValue(self::BASE_CONFIG_PATH . $value, ScopeInterface::SCOPE_STORE);
    }

    /*Métodos abaixo, para pegar informações do banco de dados,
      para posterior tratamento*/

    /**
     * @return string|null
     */
    private function getDefaultUrlToken(): ?string
    {
        return $this->getValue('token_url');
    }

    /**
     * @return string
     */
    public function getUrlToken(): string
    {
        /*Trazendo do admin*/
        $defaultTokenUrl = $this->getDefaultUrlToken();
        if ($defaultTokenUrl)
            return $defaultTokenUrl;

        /*Senão, pega desta constante, criada acima*/
        return self::BASE_VALUE_URL_TOKEN;
    }

    /**
     * @return string|null
     */
    private function getDefaultUrlOrder(): ?string
    {
        return $this->getValue('order_url');
    }

    /**
     * @return string
     */
    public function getUrlOrder(): string
    {
        /*Trazendo do admin*/
        $defaultOrderUrl = $this->getDefaultUrlOrder();
        if ($defaultOrderUrl)
            return $defaultOrderUrl;

        /*Senão, pega desta constante, criada acima*/
        return self::BASE_VALUE_URL_ORDER;
    }

    /**
     * @return string|null
     */
    private function getDefaultClientid(): ?string
    {
        return $this->getValue('client_id');
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        /*Trazendo do admin*/
        $defaultClientid = $this->getDefaultClientid();
        if ($defaultClientid)
            return $defaultClientid;

        /*Senão, pega desta constante, criada acima*/
        return self::BASE_VALUE_CLIENT_ID;
    }

    /**
     * @return string|null
     */
    private function getDefaultClientsecret(): ?string
    {
        return $this->getValue('client_secret');
    }

    /**
     * @return string
     */
    public function getClientSecret(): string
    {
        /*Trazendo do admin*/
        $defaultClientsecret = $this->getDefaultClientsecret();
        if ($defaultClientsecret)
            return $defaultClientsecret;

        /*Senão, pega desta constante, criada acima*/
        return self::BASE_VALUE_CLIENT_SECRET;
    }

    /**
     * @return string|null
     */
    private function getDefaultGrantType(): ?string
    {
        return $this->getValue('grant_type');
    }

    /**
     * @return string
     */
    public function getGrantType(): string
    {
        /*Trazendo do admin*/
        $DefaultGrantType = $this->getDefaultGrantType();
        if ($DefaultGrantType)
            return $DefaultGrantType;

        /*Senão, pega desta constante, criada acima*/
        return self::BASE_VALUE_GRANT_TYPE;
    }

    /**
     * @return string|null
     */
    private function getDefaultScope(): ?string
    {
        return $this->getValue('scope');
    }

    /**
     * @return string
     */
    public function getScope(): string
    {
        /*Trazendo do admin*/
        $DefaultScope = $this->getDefaultScope();
        if ($DefaultScope)
            return $DefaultScope;

        /*Senão, pega desta constante, criada acima*/
        return self::BASE_VALUE_SCOPE;
    }
}

