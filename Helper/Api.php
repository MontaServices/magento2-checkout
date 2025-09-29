<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 12/08/2025 16:16
 */
namespace Monta\Checkout\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Monta\CheckoutApiWrapper\MontapackingShipping;
use Monta\CheckoutApiWrapper\Service\ApiFactory;

class Api extends AbstractHelper
{
    /**
     * @param Context $context
     * @param ApiFactory $apiFactory
     * @param Configuration $montaConfiguration
     * @param System $systemHelper
     */
    public function __construct(
        Context $context,
        protected readonly ApiFactory $apiFactory,
        protected readonly Configuration $montaConfiguration,
        protected readonly System $systemHelper,
    )
    {
        parent::__construct($context);
    }

    /**
     * @return \Monta\CheckoutApiWrapper\MontaPackingShipping
     */
    public function getApi(): MontaPackingShipping
    {
        return $this->apiFactory->createFromSettings(
            settings: [
                'origin' => $this->montaConfiguration->getOrigin(),
                'user' => $this->montaConfiguration->getUsername(),
                'password' => $this->montaConfiguration->getPassword(),
                'googleKey' => $this->montaConfiguration->getGoogleKey(),
            ],
            systemInfo: $this->systemHelper->getInfo());
    }

}