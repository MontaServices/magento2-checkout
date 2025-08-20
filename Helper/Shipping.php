<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 14/08/2025 13:43
 */
namespace Monta\Checkout\Helper;

use Magento\Quote\Api\Data\CartInterface;
use Monta\CheckoutApiWrapper\Objects\PickupPoint;

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
            TODO  if config.disableDeliverydays: unset $shippingOptions[DeliveryOptions]
            if shippingOptions.StoreLocation{
               TODO set storeLocation and to PickupOptions
            }
            */
            if (!empty($shippingOptions[PickupPoint::PICKUP_OPTIONS_KEY])) {
                foreach ($shippingOptions[PickupPoint::PICKUP_OPTIONS_KEY] as $pickup) {
                    /** @var PickupPoint $pickup */
                    // For every non-AFH pickup point, remove the imageName
                    if ($pickup->getCode() != 'AFH') {
                        // TODO is this whole thing needed? imageName has no references or is NULL already
                        $pickup->imageName = null;
                    }
                }
            }
        }
        return $shippingOptions;
    }
}
