<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 10/6/2025 09:24
 */
namespace Monta\Checkout\Plugin\Order;

use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

readonly class RepositoryPlugin
{

    /**
     * @param OrderExtensionFactory $extensionFactory
     */
    public function __construct(
        protected OrderExtensionFactory $extensionFactory,
    )
    {
    }

    /**
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $order
     * @return OrderInterface
     */
    public function afterGet(OrderRepositoryInterface $subject, OrderInterface $order): OrderInterface
    {
        return $this->extendOrder($order);
    }

    /**
     * @param OrderRepositoryInterface $subject
     * @param OrderSearchResultInterface $searchResult
     * @return OrderSearchResultInterface
     */
    public function afterGetList(OrderRepositoryInterface $subject, OrderSearchResultInterface $searchResult): OrderSearchResultInterface
    {
        $orders = $searchResult->getItems();

        foreach ($orders as $order) {
            $this->extendOrder($order);
        }
        return $searchResult;
    }

    /**
     * @param OrderInterface $order
     * @return OrderInterface
     */
    protected function extendOrder(OrderInterface $order): OrderInterface
    {
        $montaShipData = $order->getMontaShippingData();

        $extensionAttributes = $order->getExtensionAttributes() ?? $this->extensionFactory->create();
        $extensionAttributes->setData('monta_shipping_data', $montaShipData);

        // Set extension attributes back to order in case they were just created
        $order->setExtensionAttributes($extensionAttributes);

        return $order;
    }

}