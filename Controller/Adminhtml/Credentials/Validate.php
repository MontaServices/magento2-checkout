<?php
/**
 * @created 12/08/2025 11:47
 */
namespace Monta\Checkout\Controller\Adminhtml\Credentials;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Monta\Checkout\Helper\Api;

class Validate extends Action implements HttpPostActionInterface
{
    /**
     * @param Context $context
     * @param Json $encoder
     * @param Api $apiHelper
     */
    public function __construct(
        Context $context,
        protected readonly Json $encoder,
        protected readonly Api $apiHelper,
    )
    {
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface
     */
    public function execute(): ResponseInterface
    {
        $api = $this->apiHelper->getApi();
        $response = $api->testConnection();
        $message = $response ? 'Credentials are valid!' : 'Credentials are not valid! Did you save before testing?';
        $result = [
            'success' => $response,
            'message' => __($message),
        ];
        return $this->getResponse()
            ->representJson($this->encoder->serialize($result));
    }
}
