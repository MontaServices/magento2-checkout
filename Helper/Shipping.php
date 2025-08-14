<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 14/08/2025 13:43
 */
namespace Monta\Checkout\Helper;

class Shipping extends Api
{
    /** Equivalent of old AbstractDeliveryOptions::generateApi
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getOptions(): array
    {
        $api = $this->getApi();
        /* TODO
        $api->setAddress(quote.getShippingAddress());
        foreach($quote->getAllItems() as $item) {
            $api->addProduct($item->getData());
        }
        */
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
