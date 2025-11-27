<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 10/2/2025 13:58
 */
namespace Monta\Checkout\Plugin\Quote;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteManagement;
use Magento\Sales\Api\OrderRepositoryInterface;
use Monta\CheckoutApiWrapper\Objects\Option;

readonly class Management
{
    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        protected OrderRepositoryInterface $orderRepository,
        protected CartRepositoryInterface $quoteRepository,
    )
    {
    }

    /**
     * @param QuoteManagement $subject
     * @param int $orderId
     * @param int $quoteId
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterPlaceOrder(QuoteManagement $subject, int $orderId, int $quoteId): int
    {
        // Get monta shipping data from Quote
        if ($convertedObject = $this->getShippingData($quoteId)) {
            $order = $this->orderRepository->get($orderId);

            /** Save converted JSON back to JSON, in proper format for export */
            $order->setMontaShippingData($convertedObject->toJson());
            $this->orderRepository->save($order);
        }

        return $orderId;
    }

    /** Get Selected shipping option from Quote
     *
     * @param int $quoteId
     * @return Option|null - Selected shipping option or NULL
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getShippingData(int $quoteId): ?Option
    {
        $option = null;
        $quote = $this->quoteRepository->get($quoteId);
        // TODO only when Monta was selected as carrier
        if ($montaShippingData = $quote->getShippingAddress()->getMontaShippingData()) {
            // Convert JSON to object
            $option = Option::constructFromJson($montaShippingData);
        }
        return $option;
    }

}