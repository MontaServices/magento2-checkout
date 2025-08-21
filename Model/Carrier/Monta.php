<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 06/08/2025 13:20
 */
namespace Monta\Checkout\Model\Carrier;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\ResultFactory;
use Psr\Log\LoggerInterface;

class Monta extends AbstractCarrier implements CarrierInterface
{
    /** @var string */
    public const string METHOD_CODE = 'monta';

    /** @var string $_code - Defined by parent */
    protected $_code = self::METHOD_CODE;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        protected readonly ResultFactory $rateResultFactory,
        protected readonly MethodFactory $rateMethodFactory,
        array $data = []
    )
    {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * @param RateRequest $request
     * @return Result|bool
     */
    public function collectRates(RateRequest $request): Result|bool
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }
        $result = $this->rateResultFactory->create();

        $result->append($this->createResultMethod());

        return $result;
    }

    /**
     * @return Method
     */
    protected function createResultMethod(): Method
    {
        $rateMethod = $this->rateMethodFactory->create();

        $rateMethod->setCarrier($this->_code);
        $rateMethod->setCarrierTitle($this->getConfigData('title'));

        $rateMethod->setMethod($this->_code);
        $rateMethod->setMethodTitle($this->getConfigData('name'));

        $rateMethod->setPrice($this->getHandlingFee());
        $rateMethod->setCost($this->getHandlingFee());

        return $rateMethod;
    }

    /**
     * @inheritDoc
     */
    public function getAllowedMethods(): array
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    /**
     * @return float
     */
    protected function getHandlingFee(): float
    {
        return (float)$this->getConfigData('handling_fee');
    }
}
