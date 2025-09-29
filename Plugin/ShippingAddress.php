<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 9/29/2025 15:18
 */
namespace Monta\Checkout\Plugin;

use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\ShippingAddressManagement;

class ShippingAddress
{
    /**
     * @param ShippingAddressManagement $subject
     * @param $cartId
     * @param AddressInterface $address
     * @return void
     */
    public function beforeAssign(
        ShippingAddressManagement $subject,
        $cartId,
        AddressInterface $address
    )
    {

        dd($address->getExtensionAttributes()['shipping_option']);;
    }

}