<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 12/08/2025 11:47
 */
namespace Monta\Checkout\Controller\Adminhtml\Credentials;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Serialize\Serializer\Json;

class Validate extends Action implements HttpPostActionInterface
{
    public function __construct(
        Context $context,
        protected readonly Json $encoder,
    )
    {
        parent::__construct($context);
    }

    public function execute(): ResponseInterface
    {
        // TODO implement verification
        $response = [
            'success' => true,
            'message' => 'Credentials are valid',
        ];
        return $this->getResponse()
            ->representJson($this->encoder->serialize($response));
    }
}
