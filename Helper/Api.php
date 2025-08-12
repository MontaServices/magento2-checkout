<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 12/08/2025 16:16
 */
namespace Monta\Checkout\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Monta\Checkout\Model\Config\Provider\MontaConfiguration;
use Monta\CheckoutApiWrapper\ApiFactory;
use Monta\CheckoutApiWrapper\MontapackingShipping;

class Api extends AbstractHelper
{
    /**
     * @param Context $context
     * @param ApiFactory $apiFactory
     * @param MontaConfiguration $montaConfiguration
     */
    public function __construct(
        Context $context,
        protected readonly ApiFactory $apiFactory,
    )
    {
        parent::__construct($context);
    }

    /**
     * @return \Monta\CheckoutApiWrapper\MontaPackingShipping
     */
    public function getApi(): MontaPackingShipping
    {
        // TODO create with SystemInfo, copy pretty code from old module
        return $this->apiFactory->create();
    }

}
