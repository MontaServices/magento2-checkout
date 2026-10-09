<?php
/**
 * @created 11/08/2025 12:49
 */
namespace Monta\Checkout\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Monta\Checkout\Block\GoogleMaps;
use Monta\Checkout\Model\Carrier\Monta;

class Configuration
{
    /** @var string */
    public const string CONFIG_PATH_SHIPPING = 'shipping/monta/';

    /**
     * @param Monta $carrier
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param EncryptorInterface $encryptor
     * @param DeploymentConfig $deploymentConfig
     */
    public function __construct(
        protected readonly Monta $carrier,
        protected readonly ScopeConfigInterface $scopeConfig,
        protected readonly StoreManagerInterface $storeManager,
        protected readonly EncryptorInterface $encryptor,
        protected readonly DeploymentConfig $deploymentConfig,
    )
    {
    }

    /**
     * @return string
     */
    public function getOrigin(): string
    {
        return (string)$this->getShippingConfig('origin');
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return (string)$this->getShippingConfig(field: 'username');
    }

    /** Get decrypted password as string
     *
     * @return string
     */
    public function getPassword(): string
    {
        return (string)$this->getShippingConfig(field: 'password', decrypt: true);
    }

    /**
     * @return string
     */
    public function getGoogleKey(): string
    {
        return (string)$this->scopeConfig->getValue(GoogleMaps::GOOGLE_MAPS_API_KEY_PATH);
    }

    /**
     * @return bool
     */
    public function getPickupPointsEnabled(): bool
    {
        return (bool)$this->carrier->getConfigData('enablepickuppoints');
    }

    /**
     * @return int
     */
    public function getMaxPickupPoints(): int
    {
        return (int)$this->carrier->getConfigData('number_of_pickuppoints');
    }

    /** Configured carrier title
     *
     * @return string
     */
    public function getCarrierTitle(): string
    {
        return $this->carrier->getConfigData('title');
    }

    /**
     * @return float
     */
    public function getHandlingFee(): float
    {
        return $this->carrier->getHandlingFee();
    }

    /** Get the current store
     *
     * @return StoreInterface
     */
    public function getStore(): StoreInterface
    {
        return $this->storeManager->getStore();
    }

    /** Hide Timeframes without a day value
     * TODO API should never return those Timeframes in the first place, but as fallback this config can hide them from the frontend
     *
     * @return bool
     */
    public function getHideEmptyTimeframes(): bool
    {
        return (bool)$this->getShippingConfig('hide_empty_timeframes');
    }

    /**
     * @return string - Full path to image
     */
    public function getCollectLogo(): string
    {
        $imagePath = $this->getShippingConfig('collect_logo') ?? '';
        if ($imagePath) {
            try {
                // Create the whole image path
                $imagePath = $this->storeManager->getStore()
                        ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
                    // image is inside this folder
                    . "sales/monta/collect/"
                    . $imagePath;
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                // On failure to get the store, do not return a partial/invalid image path
                $imagePath = '';
            }
        }
        return $imagePath;
    }

    /**
     * @param string $field
     * @param bool $decrypt - If value is encrypted, decrypt it
     * @return mixed|string
     */
    protected function getShippingConfig(string $field, bool $decrypt = false): mixed
    {
        $path  = self::CONFIG_PATH_SHIPPING . trim($field, "/");
        $value = $this->scopeConfig->getValue($path);

        if ($decrypt && $value !== null && $value !== '') {
            // env.php (config:sensitive:set) staat al plaintext; alleen DB-waarden zijn encrypted
            $fromEnv = $this->deploymentConfig->get('system/default/' . $path) !== null;
            if (!$fromEnv) {
                $value = $this->encryptor->decrypt($value);
            }
        }
        return $value;
    }
}