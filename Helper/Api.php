<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 12/08/2025 16:16
 */
namespace Monta\Checkout\Helper;

use Magento\Framework\Locale\ResolverInterface;
use Monta\CheckoutApiWrapper\MontapackingShipping;
use Monta\CheckoutApiWrapper\Objects\Settings;
use Monta\CheckoutApiWrapper\Service\ApiFactory;

class Api
{
    /**
     * @param ApiFactory $apiFactory
     * @param Configuration $montaConfiguration
     * @param System $systemHelper
     * @param ResolverInterface $localeResolver
     */
    public function __construct(
        protected readonly ApiFactory $apiFactory,
        protected readonly Configuration $montaConfiguration,
        protected readonly System $systemHelper,
        protected readonly ResolverInterface $localeResolver,
    )
    {
    }

    /**
     * @return \Monta\CheckoutApiWrapper\MontaPackingShipping
     */
    public function getApi(): MontaPackingShipping
    {
        return $this->apiFactory->create(
            settings: $this->getSettings(),
            systemInfo: $this->systemHelper->getInfo(),
        );
    }

    /**
     * @return Settings
     */
    protected function getSettings(): Settings
    {
        return new Settings(
            origin: $this->montaConfiguration->getOrigin(),
            user: $this->montaConfiguration->getUsername(),
            password: $this->montaConfiguration->getPassword(),
            pickupPointsEnabled: $this->montaConfiguration->getPickupPointsEnabled(),
            maxPickupPoints: $this->montaConfiguration->getMaxPickupPoints(),
            googleKey: $this->montaConfiguration->getGoogleKey(),
            defaultCosts: $this->montaConfiguration->getHandlingFee(),
            webshopLanguage: $this->localeResolver->getLocale(),
            currency: $this->montaConfiguration->getStore()->getCurrentCurrency()->getCurrencySymbol(),
            storeCollectLogo: $this->montaConfiguration->getCollectLogo(),
        );
    }

}