<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 11/08/2025 13:54
 */
namespace Monta\Checkout\Block\Adminhtml;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\UrlInterface;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\Helper\SecureHtmlRenderer;

class TestCredentials extends Field
{
    public const string BUTTON_ID = 'validate_credentials';

    /** @var string $_template - Define template for this Block */
    protected $_template = 'Monta_Checkout::testcredentials.phtml';

    /**
     * @param Context $context
     * @param UrlInterface $urlBuilder
     * @param array $data
     * @param SecureHtmlRenderer|null $secureRenderer
     */
    public function __construct(
        Context $context,
        protected readonly UrlInterface $urlBuilder,
        array $data = [],
        ?SecureHtmlRenderer $secureRenderer = null,
    )
    {
        parent::__construct($context, $data, $secureRenderer);
    }

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

    /** Everything the component needs to function
     *
     * @return array
     */
    public function getComponentData(): array
    {
        return [
            'formKey' => $this->getFormKey(),
            'validateUrl' => $this->getRoute(),
        ];
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

    /** Get the route for the credentials validation
     * @return string
     */
    public function getRoute(): string
    {
        // Backend URL builder automatically adds secret key
        return $this->urlBuilder->getUrl(routePath: 'monta/credentials/validate');
    }
}
