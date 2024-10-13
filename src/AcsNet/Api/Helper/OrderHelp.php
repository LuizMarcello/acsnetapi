<?php

namespace AcsNet\Api\Helper;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use AcsNet\Api\Controller\Api\Integration;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class OrderHelp extends AbstractHelper
{
    protected $logger;
    private $integration;
    private $dataservice;


    /**
     * @param LoggerInterface $logger
     * @param Context $context
     * @param Integration $integration
     * @param Data $dataservice
     */
    public function __construct(
        LoggerInterface $logger,
        Context         $context,
        Integration     $integration,
        Data            $dataservice
    )
    {
        $this->logger = $logger;
        $this->integration = $integration;
        $this->dataservice = $dataservice;
        parent::__construct($context);
    }

    /**
     * @param $payload
     * @return mixed
     * @throws LocalizedException
     */
    public function sendOrder($payload): mixed
    {
        try {
            $client = new Client();

            $token = $this->integration->execute();

            $headers = [
                'Authorization' => 'Bearer' . $token,
                'Content-Type' => 'application/json',
            ];

            $orderurl = $this->dataservice->getUrlOrder();

            /*Recebe os valores e retorna uma representação dos mesmos em json*/
            $jsonData = json_encode($payload);

            /*Logando o payload da integração*/
            $this->logger->info('Integração da API de pedidos - Enviando o payload: ' . json_encode($payload));

            /*Enviando o payload para o endpoint*/
            $request = new Request('POST', $orderurl, $headers, $jsonData);

            $response = $client->request('GET', $orderurl);
            $statusCode = $response->getStatusCode();

            /*Log do status de resposta da API*/
            $this->logger->info('Integração da API de pedidos - resposta da API: ',
                ['status_code' => $request->$statusCode()]);

            /*sendAsync(): Asynchronously send an HTTP request.*/
            /*Este "$res"(instância da classe ResponseInterface), é a resposta do sendAsync(),
              porque vai ser retornado alguma coisa, alguma informação, pedido recebido, algo assim. */
            $res = $client->sendAsync($request)->wait();

            /*Estes são métodos do "Guzzle"*/
            /*Está sendo retornando o conteúdo da resposta HTTP completa da requisição feita(sendAsync)*/
            return $res->getBody()->getContents();

        } catch (GuzzleException $e) {
            /*Em caso de erro na integração, logue a exceção*/
            $this->logger->error('Integração da API de pedidos - Error: ' . $e->getMessage());
        }
    }

}
