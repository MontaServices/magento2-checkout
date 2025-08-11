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
        return $this->getShippingConfig('username');
    }

    public function getPassword(): string
    {
        return $this->encryptor->decrypt($this->getShippingConfig('password'));
    }

    protected function getCarrierConfig(string $field)
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_CARRIER . trim(string: $field, characters: "/"));
    }

    protected function getShippingConfig(string $field)
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_SHIPPING . trim(string: $field, characters: "/"));
    }
}
