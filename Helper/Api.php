<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 12/08/2025 16:16
 */
namespace Monta\Checkout\Helper;

use Monta\CheckoutApiWrapper\MontapackingShipping;
use Monta\CheckoutApiWrapper\Objects\Settings;
use Monta\CheckoutApiWrapper\Service\ApiFactory;

class Api
{
    /**
     * @param ApiFactory $apiFactory
     * @param Configuration $montaConfiguration
     * @param System $systemHelper
     */
    public function __construct(
        protected readonly ApiFactory $apiFactory,
        protected readonly Configuration $montaConfiguration,
        protected readonly System $systemHelper,
    )
    {
    }

    /**
     * @return \Monta\CheckoutApiWrapper\MontaPackingShipping
     */
    public function getApi(): MontaPackingShipping
    {
        return $this->apiFactory->create(
            settings: new Settings(
                origin: $this->montaConfiguration->getOrigin(),
                user: $this->montaConfiguration->getUsername(),
                password: $this->montaConfiguration->getPassword(),
                googleKey: $this->montaConfiguration->getGoogleKey(),
            ),
            systemInfo: $this->systemHelper->getInfo(),
        );
    }

}