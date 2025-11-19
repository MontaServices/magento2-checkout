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
        // Quote is inactive after placement
        /** @var \Magento\Quote\Model\Quote $quote - Inactive quote after placement */
        $quote = $this->quoteRepository->get($quoteId);

        // Get monta shipping data from Quote
        if ($montaData = $quote->getShippingAddress()->getMontaShippingData()) {
            // Convert JSON to object
            $convertedObject = Option::constructFromJson($montaData);

            $order = $this->orderRepository->get($orderId);

            /** Save converted JSON in proper JSON structure for export */
            $order->setMontaShippingData($convertedObject->toJson());
            $this->orderRepository->save($order);
        }

        return $orderId;
    }

}