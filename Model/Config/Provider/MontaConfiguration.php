<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 11/08/2025 12:49
 */
namespace Monta\Checkout\Model\Config\Provider;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class MontaConfiguration
{
    public const string CONFIG_PATH_SHIPPING = 'shipping/monta/';

    public const string CONFIG_PATH_CARRIER = 'carriers/monta/';

    public function __construct(
        protected readonly ScopeConfigInterface $scopeConfig,
        protected readonly EncryptorInterface $encryptor,
    )
    {
    }

    public function getWebshop(): string
    {
        return $this->getShippingConfig('webshop');
    }

    public function getUsername(): string
    {
        return $this->getShippingConfig(field: 'username');
    }

    public function getPassword(): string
    {
        return $this->getShippingConfig(field: 'password', decrypt: true);
    }

    public function getGoogleKey(): string
    {
        return $this->getShippingConfig(field: 'googleapikey', decrypt: true);
    }

    protected function getCarrierConfig(string $field)
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_CARRIER . trim(string: $field, characters: "/"));
    }

    protected function getShippingConfig(string $field, bool $decrypt = false)
    {
        $value = $this->scopeConfig->getValue(self::CONFIG_PATH_SHIPPING . trim(string: $field, characters: "/"));
        if ($decrypt) {
            $value = $this->encryptor->decrypt($value);
        }
        return $value;
    }
}
