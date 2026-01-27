<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 1/7/2026 11:46
 */
namespace Monta\Checkout\Helper;

use Magento\Checkout\Model\Session;
use Magento\Quote\Api\CartRepositoryInterface;
use Monta\CheckoutApiWrapper\Objects\Option;

readonly class Quote
{
    /**
     * @param Session $checkoutSession
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        protected Session $checkoutSession,
        protected CartRepositoryInterface $quoteRepository,
    )
    {
    }

    /** Set a selected delivery option to Quote
     * Basically what Monta\Checkout\Plugin\Quote\Management::getShippingData did except not via ExtensionAttributes,
     * which was set by
     *
     * To be used by Hyva companion module
     *
     * @param string $optionJson
     * @param bool $return - Whether to return the converted option
     * @return ?Option
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function setOption(string $optionJson, bool $return = false): ?Option
    {
        $quote = $this->checkoutSession->getQuote();
        $option = null;

        if ($quote && $quote->getId()) {
            $address = $quote->getShippingAddress();
            $address->setMontaShippingData($optionJson);

            $this->quoteRepository->save($quote);

            // Return the converted object if requested
            if ($return) {
                $option = $this->getOptionFromQuote($quote);
            }
        }
        return $option;
    }

    /** Retrieve the current selected Shipping option from Quote
     *
     * @param \Magento\Quote\Model\Quote|null $quote - Pass quote because getting from session can cause loops (e.g. from Carrier)
     * @return Option|null
     */
    public function getOptionFromQuote(?\Magento\Quote\Model\Quote $quote = null): ?Option
    {
        $option = null;
        try {
            if (!$quote) {
                $quote = $this->checkoutSession->getQuote();
            }
            if ($montaData = $quote->getShippingAddress()->getMontaShippingData()) {
                // Decode JSON into object
                $option = Option::constructFromJson($montaData);
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
        } catch (\Magento\Framework\Exception\LocalizedException $exception) {
            // Ignore exceptions, return null
        }
        return $option;
    }
}