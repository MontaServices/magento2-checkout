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
        $api->setAddressFromArray($quote->getShippingAddress()->getData());
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
