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
        $shippingAddress = $quote->getShippingAddress();
        $shippingOptions = [];
        // Without address, don't bother loading API
        if ($shippingAddress->getStreetFull()) {
            // Instantiate API with configured credentials
            $api = $this->getApi();

            // Add Shipping address to quote
            $api->setAddressFromArray($shippingAddress->getData());

            foreach ($quote->getAllVisibleItems() as $item) {
                $api->addProductFromArray($item->getData());
            }

            // Call the API, get formatted shipping options
            $shippingOptions = $api->getShippingOptions(computeKm: true);
            /*
            TODO from old module: if config.disableDeliverydays: unset $shippingOptions[DeliveryOptions]
            if shippingOptions.StoreLocation{
               TODO set storeLocation and to PickupOptions
            }
            */
        }
        return $shippingOptions;
    }
}