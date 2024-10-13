<?php

namespace AcsNet\Api\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Psr\Log\LoggerInterface;

class TransferCustomColumnObserver implements ObserverInterface
{
    protected $checkoutSession;
    protected $logger;

    /**
     * @param CheckoutSession $checkoutSession
     * @param LoggerInterface $logger
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        LoggerInterface $logger,
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->logger = $logger;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        /*Obter a cotação(quote)*/
        try {
            $quote = $this->checkoutSession->getQuote(); // Entidade Quote

            if ($quote) {
                /*Obter o pedido(sales_order)*/
                $order = $observer->getEvent()->getOrder(); // Entidade Order

                /*Transferir os dados da coluna schooldocnumber da quote para o pedido(sales_order)*/
                $order->setData('schooldocnumber', $quote->getData('schooldocnumber'));

                /*Transferir os dados da coluna schoolseries da quote para o pedido(sales_order)*/
                $order->setData('schoolseries', $quote->getData('schoolseries'));

                /*Transferir os dados da coluna schoolyear da quote para o pedido(sales_order)*/
                $order->setData('schoolyear', $quote->getData('schoolyear'));

                /*Transferir os dados da coluna schoolclass da quote para o pedido(sales_order)*/
                $order->setData('schoolclass', $quote->getData('schoolclass'));

                /*Transferir os dados da coluna studentname da quote para o pedido(sales_order)*/
                $order->setData('studentname', $quote->getData('studentname'));


                /*Transferir os dados das colunas dos itens da cotação(quote_item) para os itens do pedido(sales_order_item)*/
                foreach ($quote->getAllItems() as $quoteItem) {
                    $orderItem = $order->getItemByQuoteItemId($quoteItem->getId());
                    if ($orderItem) {
                        $orderItem->setData('schooldocnumber', $quoteItem->getData('schooldocnumber'));
                        $orderItem->setData('schoolseries', $quoteItem->getData('schoolseries'));
                        $orderItem->setData('schoolyear', $quoteItem->getData('schoolyear'));
                        $orderItem->setData('schoolclass', $quoteItem->getData('schoolclass'));
                        $orderItem->setData('studentname', $quoteItem->getData('studentname'));
                    }
                }
            }

        } catch (\Exception $e) {
            /*Registra o erro no log para futura análise*/
            $this->logger->error('Erro ao obter o Quote: ' . $e->getMessage());
        }
    }
}

