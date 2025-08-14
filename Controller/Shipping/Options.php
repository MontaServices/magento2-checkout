<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 13/08/2025 16:33
 */
namespace Monta\Checkout\Controller\Shipping;

use Magento\Framework\App\Action\HttpGetActionInterface;

class Options implements HttpGetActionInterface
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        // TODO get quote, call Api, return ShippingOptions
    }
}
