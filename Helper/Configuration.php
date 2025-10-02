<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 11/08/2025 12:49
 */
namespace Monta\Checkout\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class Configuration
{
    /** @var string */
    public const string CONFIG_PATH_SHIPPING = 'shipping/monta/';

    /** @var string */
    public const string CONFIG_PATH_CARRIER = 'carriers/monta/';

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        protected readonly ScopeConfigInterface $scopeConfig,
        protected readonly EncryptorInterface $encryptor,
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
        return (string)$this->getShippingConfig(field: 'googleapikey', decrypt: true);
    }

    /**
     * @param string $field
     * @return mixed
     */
    protected function getCarrierConfig(string $field)
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_CARRIER . trim(string: $field, characters: "/"));
    }

    /**
     * @param string $field
     * @param bool $decrypt - If value is encrypted, decrypt it
     * @return mixed|string
     */
    protected function getShippingConfig(string $field, bool $decrypt = false)
    {
        $value = $this->scopeConfig->getValue(self::CONFIG_PATH_SHIPPING . trim(string: $field, characters: "/"));
        if ($decrypt) {
            // TODO when value is set in config file (env.php) instead of DB then the value is doubly encrypted here, goes wrong
            $value = $this->encryptor->decrypt($value);
        }
        return $value;
    }
}