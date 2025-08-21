<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 14/08/2025 11:42
 */
namespace Monta\Checkout\Plugin;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Quote\Api\Data\ShippingMethodInterfaceFactory;
use Magento\Quote\Model\Cart\ShippingMethod;
use Magento\Quote\Model\Cart\ShippingMethodConverter;
use Magento\Quote\Model\Quote\Address\Rate;
use Monta\Checkout\Helper\Shipping;
use Monta\Checkout\Model\Carrier\Monta;

/**
 * @kudos Based on Andresa Martins:
 * https://magento.stackexchange.com/a/305907
 */
class MethodConverter
{
    /**
     * @param ShippingMethodInterfaceFactory $extensionFactory
     * @param Shipping $shippingHelper
     * @param Json $serializer
     */
    public function __construct(
        protected readonly ShippingMethodInterfaceFactory $extensionFactory,
        protected readonly Shipping $shippingHelper,
        protected readonly Json $serializer,
    )
    {
    }

    /**
     * @param ShippingMethodConverter $subject
     * @param ShippingMethod $result
     * @param Rate $rate
     * @return ShippingMethod
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function afterModelToDataObject(ShippingMethodConverter $subject, ShippingMethod $result, Rate $rate): ShippingMethod
    {
        if ($rate->getMethod() == Monta::METHOD_CODE) {
            $ext = $result->getExtensionAttributes() ?? $this->extensionFactory->create();
            // Insert extension attribute based on Quote
            $ext->setData(Shipping::MONTA_SHIPPING_OPTIONS,
                // Encode JSON to pass to frontend
                $this->serializer->serialize(
                    $this->shippingHelper->getOptions($rate->getAddress()->getQuote())
                )
            );
            $result->setExtensionAttributes($ext);
        }
        return $result;
    }
}
