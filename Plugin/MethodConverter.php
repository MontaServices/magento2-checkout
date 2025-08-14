<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 14/08/2025 11:42
 */
namespace Monta\Checkout\Plugin;

use Magento\Quote\Api\Data\ShippingMethodInterfaceFactory;
use Magento\Quote\Model\Cart\ShippingMethod;
use Magento\Quote\Model\Cart\ShippingMethodConverter;
use Magento\Quote\Model\Quote\Address\Rate;
use Monta\Checkout\Model\Carrier\Monta;
use Monta\CheckoutApiWrapper\Objects\TimeFrame;

/**
 * @kudos Based on Andresa Martins:
 * https://magento.stackexchange.com/a/305907
 */
class MethodConverter
{
    /**
     * @param ShippingMethodInterfaceFactory $extensionFactory
     */
    public function __construct(
        protected readonly ShippingMethodInterfaceFactory $extensionFactory,
    )
    {
    }

    /**
     * @param ShippingMethodConverter $subject
     * @param ShippingMethod $result
     * @param Rate $rate
     * @return ShippingMethod
     */
    public function afterModelToDataObject(ShippingMethodConverter $subject, ShippingMethod $result, Rate $rate)
    {
        if ($rate->getMethod() == Monta::METHOD_CODE) {
            $ext = $result->getExtensionAttributes() ?? $this->extensionFactory->create();
            $ext->setData(TimeFrame::TIMEFRAMES_FIELD, $this->getTimeframes());
            $result->setExtensionAttributes($ext);
        }
        return $result;
    }

    /**
     * @return array[]
     */
    protected function getTimeframes(): array
    {
        return [
            ['code' => "postnl", "title" => "Pakjegemakt"],
            ['code' => "dhl", "title" => "DHL Tomorrow"],
        ];
    }
}
