<?php
/**
 * Class UpdatePayment
 *
 * PHP version 7
 *
 * @category Payfast
 * @package  Payfast_Payfast
 * @license  https://www.payfast.co.za  Open Software License (OSL 3.0)
 * @link     https://www.payfast.co.za
 */
namespace Payfast\Payfast\Controller\Adminhtml\Payfast\Recurring\Payment;

use \Magento\Framework\Exception\LocalizedException as LocalizedException;
use Magento\Customer\Controller\RegistryConstants;

/**
 * Class UpdatePayment
 *
 * @category Payfast
 * @package  Payfast_Payfast
 * @license  https://www.payfast.co.za  Open Software License (OSL 3.0)
 * @link     https://www.payfast.co.za
 */
class UpdatePayment extends Index
{
    /**
     * LoggerInterface
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * UpdatePayment constructor.
     *
     * @param \Magento\Backend\App\Action\Context              $context           context
     * @param \Magento\Framework\Registry                      $coreRegistry      coreRegistry
     * @param \Psr\Log\LoggerInterface                         $logger            logger
     * @param \Magento\Framework\View\Result\PageFactory       $resultPageFactory resultPageFactory
     * @param \Payfast\Payfast\Model\Payment $paymentModel      paymentModel
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Payfast\Payfast\Model\Payment $paymentModel
    ) {
        $this->logger = $logger;
        parent::__construct($context, $coreRegistry, $logger, $resultPageFactory, $paymentModel);
    }

    /**
     * Execute
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page|void
     */
    public function execute()
    {
        $payment = null;
        try {
            $payment = $this->_initPayment();
            $payment->fetchUpdate();
            if ($payment->hasDataChanges()) {
                $payment->save();
                $this->messageManager->addSuccessMessage(__('Payment status has been updated.'));
            } else {
                $this->messageManager->addNoticeMessage(__('There is no change in payment status.'));
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('We could not update the payment.'));
            $this->logger->err($e);
        }
        if ($payment) {
            $this->_redirect('sales/*/view', [self::PARAM_PAYMENT => $payment->getId()]);
        } else {
            $this->_redirect('sales/*/');
        }
    }

    /**
     * IsAllowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Payfast_Payfast::payfast_recurring_payment');
    }
}
