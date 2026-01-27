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
use Monta\Checkout\Helper\Quote;
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
     * @param Quote $quoteHelper
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        protected readonly ResultFactory $rateResultFactory,
        protected readonly MethodFactory $rateMethodFactory,
        protected readonly Quote $quoteHelper,
        array $data = [],
    )
    {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /** Function runs before loading shipping methods for checkout and after selecting one
     *
     * @param RateRequest $request
     * @return Result|bool
     */
    public function collectRates(RateRequest $request): Result|bool
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }
        $result = $this->rateResultFactory->create();

        $result->append($this->createResultMethod($request));

        return $result;
    }

    /**
     * @param RateRequest $request
     * @return Method
     */
    protected function createResultMethod(RateRequest $request): Method
    {
        $rateMethod = $this->rateMethodFactory->create();

        $rateMethod->setCarrier($this->_code);
        $rateMethod->setCarrierTitle($this->getConfigData('title'));

        $rateMethod->setMethod($this->_code);
        $rateMethod->setMethodTitle($this->getConfigData('name'));

        $rateMethod->setPrice($this->getHandlingFee());
        $rateMethod->setCost($this->getHandlingFee());

        // Get Quote via Items, loading CheckoutSession would cause an infinite loop
        $allItems = $request->getAllItems();

        if (!empty($allItems)) {
            /** @var \Magento\Quote\Model\Quote $quote */
            $quote = reset($allItems)->getQuote();
            if ($quote->getShippingAddress()->getShippingMethod()) {
                // If this method is the quote's selected method
                if (str_starts_with(haystack: $quote->getShippingAddress()->getShippingMethod(), needle: $this->_code)) {
                    // Get Option and calculate price
                    if ($option = $this->quoteHelper->getOptionFromQuote($quote)) {
                        // sometimes $option might be PickupPoint whose `getPrice` has no arguments
                        $rateMethod->setPrice($option->getPrice(true));
                        $rateMethod->setCost($option->getPrice());
                    }
                }
            }
        }

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
    public function getHandlingFee(): float
    {
        return (float)$this->getConfigData('handling_fee');
    }
}