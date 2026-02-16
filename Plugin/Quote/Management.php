<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 10/2/2025 13:58
 */
namespace Monta\Checkout\Plugin\Quote;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteManagement;
use Magento\Sales\Api\OrderRepositoryInterface;
use Monta\Checkout\Helper\Shipping;
use Monta\CheckoutApiWrapper\Objects\Option;

readonly class Management
{
    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param CartRepositoryInterface $quoteRepository
     * @param Shipping $shippingHelper
     */
    public function __construct(
        protected OrderRepositoryInterface $orderRepository,
        protected CartRepositoryInterface $quoteRepository,
        protected Shipping $shippingHelper,
    )
    {
    }

    /** Validate selected Option before placing order
     *
     * @param QuoteManagement $subject
     * @param int $cartId
     * @param \Magento\Payment\Model\InfoInterface|null $paymentMethod
     * @return void
     * @throws \Exception
     */
    public function beforePlaceOrder(
        QuoteManagement $subject,
        int $cartId,
        ?\Magento\Payment\Model\InfoInterface $paymentMethod = null,
    ): void
    {
        if ($montaShippingData = $this->getShippingData($cartId)) {
            $montaShippingData->validate();

            // In case of pickup, set Pickup address as Shipping address
            if ($montaShippingData->isPickup()) {
                /** @var \Monta\CheckoutApiWrapper\Objects\PickupPoint $montaShippingData */
                $shippingAddress = $this->quoteRepository->get($cartId)->getShippingAddress();

                // Update Shipping address, saved later in `placeOrder`
                $shippingAddress->setStreet($montaShippingData->getStreet());
                $shippingAddress->setCompany($montaShippingData->getCompany());
                $shippingAddress->setPostcode($montaShippingData->getPostalCode());
                $shippingAddress->setCity($montaShippingData->getCity());
                $shippingAddress->setCountryId($montaShippingData->getCountryCode());
            }
        }
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
        $shippingAddress = $quote->getShippingAddress();

        // Only if the method is active
        if ($this->shippingHelper->isMonta($shippingAddress->getShippingMethod())) {
            if ($montaShippingData = $shippingAddress->getMontaShippingData()) {
                // Convert JSON to object
                $option = Option::constructFromJson($montaShippingData);
            }
        }
        return $option;
    }

}