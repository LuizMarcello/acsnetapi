<?php

namespace AcsNet\Api\Observer;

use Magento\Catalog\Api\ProductRepositoryInterface;
use AcsNet\Api\Helper\Data;
use AcsNet\Api\Helper\OrderHelp;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class OrderPlaceAfterObserver implements ObserverInterface
{
    protected $productRepository;

    protected $logger;
    protected $orderhelp;
    protected $dataService;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        LoggerInterface            $logger,
        Data                       $dataservice,
        OrderHelp                  $orderhelp
    )
    {
        $this->productRepository = $productRepository;
        $this->logger = $logger;
        $this->dataService = $dataservice;
        $this->orderhelp = $orderhelp;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        try {
            /*Obtendo o objeto do pedido*/
            $order = $observer->getEvent()->getOrder();


            /*Construindo o payload a partir do pedido*/
            /*formato array=[] - Converter para json*/

            $payload = [
                'OrderNumber' => $order->getIncrementId(),
                'OrderType' => $order->getType(),
            ];

            if ($order->gettype() === 3) {
                foreach ($order->getItems() as $item) {

                    $sku = $item->getSku();
                    $product = $this->productRepository->get($sku);

                    $payload [] = [
                        'SchoolInfo.SchoolDocNumber' => $product->getCustomAttribute('schooldocnumber'),
                        'SchoolInfo.SchoolSeries' => $product->getCustomAttribute('schoolseries'),
                        'SchoolInfo.SchoolYear' => $product->getCustomAttribute('schoolyear'),
                        'SchoolInfo.SchoolClass' => $product->getCustomAttribute('schoolclass'),
                        'SchoolInfo.StudentName' => $product->getCustomAttribute('studentname'),
                    ];
                }
            } else {
                $payload['Customer'][] = [
                    'DocNumber' => $order->getCustomerEmail()->getTaxId(),
                    'Name' => $order->getCustometName(),
                    'Email' => $order->getCustomerEmail(),
                    'PhoneNumber' => $order->getShippingAddress()->getTelephone(),
                ];

                $shippingAddress = $order->getShippingAddress();

                $payload['Customer']['Address'][] = [
                    'AddressType' => $order->getAddressType(),
                    'Country' => $order->getCountryId(),
                    'PostalCode' => $shippingAddress->getPostcode(),
                    'Address' => $shippingAddress->getStreetLine(1),
                    'Number' => $shippingAddress->getStreetLine(2),
                    'Complement' => $shippingAddress->getStreetLine(3),
                    'District' => $shippingAddress->getCity(),
                ];

                // Itera sobre os itens do pedido para adicionar ao payload
                foreach ($order->getItems() as $item) {
                    $payload['OrderProducts'][] = [
                        'Id' => $item->getIncrementId(),
                        'Quantity' => $item->getQtyOrdered(),
                        'StockId' => $item->getStockId(),
                        'TotalProduct' => $item->getGrandTotal(),
                        'DiscountItem' => $item->getShippingAddress()->getDiscount(),
                    ];
                }

                $payment = $order->getPayment();

                $payload['OrderPayments'][] = [
                    'PaymentType' => $order->getMethod(),
                    'ValuePay' => $payment->getAmountOrdered(),
                    'DocNumberOfCardOperator' => $payment->getAdditionalInformation('card_number'),
                    'LastFourDigitsOfCard"' => $payment->getAdditionalInformation('last4'),
                    'AuthorizationNumber' => $payment->getLastTransId(),
                    'NumberOfInstallments' => $payment->getAdditionalInformation('installments'),
                    'PaymentDate' => $payment->getCreatedAt(),
                ];

                $payload [] = [
                    'BuyerPresence' => 2,
                    'IntermediaryDocNumber' => $order->getIntermediaryDocNumber(),
                    'OrderRemarks' => $order->getCustomerNote() ?: 'Nenhuma observação',
                ];

                $shippingAddress = $order->getShippingAddress();

                $payload['Shipping'][] = [
                    'ShippingType' => 1,
                    'ShippingCompanyDocNumber' => $shippingAddress ? $shippingAddress->getCompany() : 'CNPJ_DA_TRANSPORTADORA',
                    'NetWeight' => array_sum(array_map(function ($item) {
                        return $item->getWeight() * $item->getQtyOrdered(); // Peso líquido total
                    }, $order->getItems())),
                    'GrossWeight' => array_sum(array_map(function ($item) {
                        return $item->getWeight() * $item->getQtyOrdered(); // Peso bruto total
                    }, $order->getItems())),
                    'Volume' => 0,
                    'ShippingValue' => null
                ];

                $invoiceCollection = $order->getInvoiceCollection();
                $invoice = $invoiceCollection->getFirstItem();

                if ($invoice) {
                    $payload [] = [
                        'InvoiceRemarks' => $invoice->getCommentText() ?: 'Nenhuma observação na fatura',
                    ];
                }

                $payload [] = [
                    'InvoiceDate' => $invoice ? $invoice->getCreatedAt() : 'Data não disponível',
                ];
            }

            $this->orderhelp->sendOrder($payload);

        } catch
        (\Exception $e) {
            /*Log para verificar se o evento está funcionando*/
            $this->logger->info($e->getMessage());
        }
    }
}
