<?php

namespace AcsNet\Api\Controller\Api;

use Magento\Framework\App\Action\HttpGetActionInterface;
use AcsNet\Api\Helper\Data;
use AcsNet\Api\Service\Token;

class Integration implements HttpGetActionInterface
{
    private $dataservice;
    private $tokenservice;

    /**
     * @param Data $dataservice
     * @param Token $tokenservice
     */
    public function __construct(Data $dataservice, Token $tokenservice)
    {
        $this->dataservice = $dataservice;
        $this->tokenservice = $tokenservice;
    }

    /**
     * @return string:
     */
    public function execute(): string
    {
        $clientId = $this->dataservice->getClientId();
        $clientSecret = $this->dataservice->getClientSecret();
        $grantType = $this->dataservice->getGrantType();
        $scope = $this->dataservice->getScope();
        $tokenurl = $this->dataservice->getUrlToken();

        /*Tudo, dentro dos parênteses, está sendo retornado em formato "json".*/
        /*Convertendo de "json" para array*/
        $result = json_decode($this->tokenservice->generate(
            $clientId,
            $clientSecret,
            $grantType,
            $scope,
            $tokenurl));

        var_dump($result->access_token);
//        var_dump($result->expires_in);
//        var_dump($result->token_type);
//        die();

        /*A resposta(um array[]), tem 3 chave=>valor: access_token, expires_in
          e token_type. Aqui está pegando só a chave access_token */
        return $result->access_token;
    }
}
