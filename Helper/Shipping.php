<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 14/08/2025 13:43
 */
namespace Monta\Checkout\Helper;

use Magento\Quote\Api\Data\CartInterface;

class Shipping extends Api
{
    public const string MONTA_SHIPPING_OPTIONS = 'monta_shipping_options';

    /** Equivalent of old AbstractDeliveryOptions::generateApi
     * Get Shipping Options for this quote
     *
     * @param CartInterface $quote
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getOptions(CartInterface $quote): array
    {
        $api = $this->getApi();

        // Add Shipping address to quote
        $addr = $quote->getShippingAddress();
        $api->setAddress(
        // TODO perhaps use TIG\PostNL\Helper\AddressEnhancer? what if street is string, or 1 line, or 4 lines
        // TODO move that converting to CheckoutApiWrapper to use generic
            street: $addr->getStreetLine(1), // First line is usually
            houseNumber: $addr->getStreetLine(2), // By default Magento puts housenumber on line 2
            houseNumberAddition: $addr->getStreetLine(3), // Usually housenumber is on line 3
            postalCode: $addr->getPostcode(),
            city: $addr->getCity(),
            state: $addr->getRegionCode(),
            countryCode: (string)$addr->getCountryId(),
        );
        /* TODO
        foreach($quote->getAllItems() as $item) {
            $api->addProduct($item->getData());
        }
        */
        // Call the API, get formatted shipping options
        $shippingOptions = $api->getShippingOptions();
        /*
         if config.disableDeliverydays: unset $shippingOptions[DeliveryOptions]
        if shippingOptions.StoreLocation{
            set storeLocation and to PickupOptions
        }
        remove PickupOptions with code AFH
        recalculate PickupOptions[].distanceMeters
        */
        return $shippingOptions;
    }
}
