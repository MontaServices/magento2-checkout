<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 9/29/2025 15:18
 */
namespace Monta\Checkout\Plugin\Quote;

use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Model\ShippingAddressManagement;

class ShippingAddress
{
    /**
     * @param ShippingAddressManagement $subject
     * @param int|null $cartId
     * @param AddressInterface $address
     * @return void
     */
    public function beforeAssign(
        ShippingAddressManagement $subject,
        ?int $cartId,
        AddressInterface $address
    ): void
    {
        // If selected shipping option was passed
        if (!empty($selectedOption = $address->getExtensionAttributes()->getShippingOption())) {
            // Keep field in address, saved later in 'assign'
            $address->setMontaShippingData($selectedOption);
        }
    }

}