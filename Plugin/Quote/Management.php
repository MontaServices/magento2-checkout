<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 10/2/2025 13:58
 */
namespace Monta\Checkout\Plugin\Quote;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Model\InfoInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteManagement;
use Magento\Sales\Api\OrderRepositoryInterface;
use Monta\Checkout\Helper\Quote;
use Monta\Checkout\Helper\Shipping;
use Monta\CheckoutApiWrapper\Objects\Option;
use Monta\CheckoutApiWrapper\Objects\PickupPoint;
use Monta\CheckoutApiWrapper\Objects\ShippingOption;

readonly class Management
{
    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param CartRepositoryInterface $quoteRepository
     * @param Shipping $shippingHelper
     * @param Quote $quoteHelper
     */
    public function __construct(
        protected OrderRepositoryInterface $orderRepository,
        protected CartRepositoryInterface $quoteRepository,
        protected Shipping $shippingHelper,
        protected Quote $quoteHelper,
    )
    {
    }

    /** Validate selected Option before placing order
     *
     * @param QuoteManagement $subject
     * @param int $cartId
     * @param InfoInterface|null $paymentMethod
     * @return void
     * @throws \Exception
     */
    public function beforePlaceOrder(
        QuoteManagement $subject,
        int $cartId,
        ?InfoInterface $paymentMethod = null,
    ): void
    {
        if(!$this->getShippingData($cartId)
            && $this->shippingHelper->isMonta($this->quoteRepository->get($cartId)->getShippingAddress()->getShippingMethod())) {
            throw new LocalizedException(
                __('Please select a shipping option before placing your order.')
            );
        }

        if ($montaShippingData = $this->getShippingData($cartId)) {
            $montaShippingData->validate();

            // In case of pickup, set Pickup address as Shipping address
            if ($montaShippingData->isPickup()) {
                /** @var \Monta\CheckoutApiWrapper\Objects\PickupPoint $montaShippingData */
                $shippingAddress = $this->quoteRepository->get($cartId)->getShippingAddress();

                // Update Shipping address, saved later in `placeOrder`
                $this->quoteHelper->convertShippingAddress(
                    $shippingAddress,
                    $montaShippingData,
                );
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
    public function afterPlaceOrder(
        QuoteManagement $subject,
        int $orderId,
        int $quoteId,
    ): int
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

    /** Get selected shipping option from Quote
     *
     * @param int $quoteId
     * @return Option|ShippingOption|PickupPoint|null - Selected Shipping option or NULL
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getShippingData(int $quoteId): null|Option|ShippingOption|PickupPoint
    {
        $option = null;
        $quote = $this->quoteRepository->get($quoteId);
        $shippingAddress = $quote->getShippingAddress();

        // Only if the method is active
        // TODO move 'isMonta' check to inside Quotehelper method, but there would be a circular dependency
        if ($this->shippingHelper->isMonta($shippingAddress->getShippingMethod())) {
            $option = $this->quoteHelper->getOptionFromQuote($quote);
        }
        return $option;
    }

//    private function isOwnShippingMethod(string $methodCode): bool
//    {
//        // Checks the name of the carrier_method to validate the method is Monta
//        return str_starts_with($methodCode, 'monta_');
//    }
}
