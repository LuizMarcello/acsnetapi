<?php
/*Esta classe vai gerar e enviar o token*/

namespace AcsNet\Api\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class Token
{
    /**
     * @param $clientId
     * @param $clientSecret
     * @param $grantType
     * @param $scope
     * @param $tokenUrl
     * @return mixed
     */
    public function generate($clientId, $clientSecret, $grantType, $scope, $tokenUrl): mixed
    {
        $client = new Client();

        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];

        // Define as opções do request com parâmetros dinâmicos
        $options = [
            'form_params' => [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'grant_type' => $grantType,
                'scope' => $scope,
                'token_url' => $tokenUrl
            ]];

        $request = new Request('POST', $tokenUrl, $headers);

        /*sendAsync(): Asynchronously send an HTTP request.*/
        /*Este "$res"(instância da classe ResponseInterface), é a resposta do sendAsync(),
          porque vai ser retornado alguma coisa, alguma informação, pedido recebido, algo assim. */
        $res = $client->sendAsync($request, $options)->wait();
        /*Estes são métodos do "Guzzle"*/
        /*Está sendo retornando o conteúdo da resposta HTTP completa da requisição feita(sendAsync)*/
        return $res->getBody()->getContents();
    }
}

