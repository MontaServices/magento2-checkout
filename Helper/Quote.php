<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 1/7/2026 11:46
 */
namespace Monta\Checkout\Helper;

use Magento\Checkout\Model\Session\Proxy as Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote\Address;
use Monta\CheckoutApiWrapper\Objects\Option;
use Monta\CheckoutApiWrapper\Objects\PickupPoint;
use Monta\CheckoutApiWrapper\Objects\ShippingOption;

readonly class Quote
{
    /**
     * @param Session $checkoutSession
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        protected Session $checkoutSession,
        protected CartRepositoryInterface $quoteRepository,
    )
    {
    }

    /** Set a selected delivery option to Quote
     * Basically what Monta\Checkout\Plugin\Quote\Management::getShippingData did except not via ExtensionAttributes,
     * which was set by
     *
     * To be used by Hyva companion module
     *
     * @param string $optionJson
     * @param bool $return - Whether to return the converted option
     * @return ?Option
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function setOption(string $optionJson, bool $return = false): ?Option
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->checkoutSession->getQuote();
        $option = null;

        if ($quote && $quote->getId()) {
            $address = $quote->getShippingAddress();
            $address->setMontaShippingData($optionJson);

            $this->quoteRepository->save($quote);

            // Return the converted object if requested
            if ($return) {
                $option = $this->getOptionFromQuote($quote);
            }
        }
        return $option;
    }

    /** Set ShipperOptions to selected ShippingOption
     * Similar to frontend `updateQuote` method
     *
     * @param array $shipperOptions
     * @return void
     * @deprecated - Currently not in use anywhere
     */
    public function setShipperOptions(array $shipperOptions): void
    {
        $this->updateOption(function (Option $option) use ($shipperOptions) {
            // Shipper options not valid for Pickup, do nothing then
            if (!$option->isPickup()) {
                // Frontend sets this field the same way in delivery.js
                $option->setSelectedShipperOptions($shipperOptions);
            }
        });
    }

    /** Update Postnumber in selected Pickup option in Quote
     *
     * @param string $selectedPostnumber - Customer's Postnumber for packstation
     * @return void
     */
    public function updatePackstation(string $selectedPostnumber): void
    {
        $this->updateOption(function (Option $option) use ($selectedPostnumber) {
            // Only Pickup points can have Postnumber
            if ($option->isPickup()) {
                $option->setPostnumber($selectedPostnumber);
            }
        });
    }

    /** Generic method for updating Option in Quote
     *
     * @param callable $callback - Mutate the Option in quote
     * @return void
     */
    protected function updateOption(callable $callback): void
    {
        // Can only update data if Option is set in quote
        if ($option = $this->getOptionFromQuote()) {
            // Perform callback on option
            $callback($option);

            // Then save it
            try {
                $this->setOption(json_encode($option));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                // Catch and ignore TODO write log somewhere
            }
        }
    }

    /** Retrieve the current selected Shipping option from Quote
     *
     * @param \Magento\Quote\Model\Quote|null $quote - Pass quote because getting from session can cause loops (e.g. from Carrier)
     * @return Option|null
     */
    public function getOptionFromQuote(\Magento\Quote\Model\Quote|int|null $quote = null,
    ): null|Option|ShippingOption|PickupPoint
    {
        $option = null;
        try {
            $quote = $this->getQuote($quote);
            if ($montaData = $quote->getShippingAddress()->getMontaShippingData()) {
                // Check if data is valid (every option always must have a code)
                if (!empty(json_decode($montaData, true)['code'])) {
                    // Decode JSON into object
                    $option = Option::constructFromJson($montaData);
                }
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException|LocalizedException $exception) {
            // Ignore exceptions, return null
        }
        return $option;
    }

    /** Get Shipping address converted for Pickup
     *
     * @param Address|null $shippingAddress
     * @param Option|null $option
     * @param int|null $cartId
     * @return ?Address
     */
    public function convertShippingAddress(
        Address $shippingAddress = null,
        Option $option = null,
        ?int $cartId = null,
    ): ?Address
    {
        $quote = $this->getQuote($cartId);
        // When not passed, load from repository
        if (!$shippingAddress) {
            $shippingAddress = $quote->getShippingAddress();
        }
        // Same for option
        if (!$option) {
            $option = $this->getOptionFromQuote();
        }
        // If all are found and it is a Pickup point
        if ($shippingAddress && $option && $option->isPickup()) {
            // Update Shipping address, saved later in `placeOrder`
            $shippingAddress->setStreet($option->getStreet());
            $shippingAddress->setCompany($option->getCompany());
            $shippingAddress->setPostcode($option->getPostalCode());
            $shippingAddress->setCity($option->getCity());
            $shippingAddress->setCountryId($option->getCountryCode());
        }
        return $shippingAddress;
    }

    /** Load the Quote from arguments or from session
     *
     * @param \Magento\Quote\Model\Quote|int|null $quote - Quote object, or ID, or null
     * @return \Magento\Quote\Model\Quote - The resolved Quote object
     * @throws NoSuchEntityException
     */
    protected function getQuote(\Magento\Quote\Model\Quote|int|null $quote = null): \Magento\Quote\Model\Quote
    {
        // When cartId is passed, load from repository
        if (is_int($quote)) {
            $quote = $this->quoteRepository->get($quote);
        }
        // When not found from arguments, get from checkoutSession (could lead to circular errors)
        if (!$quote) {
            $quote = $this->checkoutSession->getQuote();
        }
        return $quote;
    }

}