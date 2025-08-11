<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 11/08/2025 13:54
 */
namespace Monta\Checkout\Block\Adminhtml;

use Magento\Backend\Block\Widget\Button;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\Element\BlockInterface;

class TestCredentials extends Field
{
    /** @var string $_template - Define template for this Block */
    protected $_template = 'Monta_Checkout::testcredentials.phtml';

    /** Override to alter behavior
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        // Remove scope comment, field has no label
        $element->unsScope();

        return parent::render($element);
    }

    /** Element HTML is block HTML
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->toHtml();
    }

    /**
     * @return BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getButton(): BlockInterface
    {
        return $this->getLayout()
            ->createBlock(Button::class)
            ->setData(
                [
                    'id' => 'test_credentials',
                    'label' => __('Test credentials'),
                ]
            );
    }
}
