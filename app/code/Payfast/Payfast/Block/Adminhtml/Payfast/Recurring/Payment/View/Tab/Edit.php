<?php
/**
 * Class Info
 *
 * PHP version 7
 *
 * @category Payfast
 * @package  Payfast_Payfast
 * @author   PayFast
 * @license  https://www.payfast.co.za  Open Software License (OSL 3.0)
 * @link     https://www.payfast.co.za
 */
namespace Payfast\Payfast\Block\Adminhtml\Payfast\Recurring\Payment\View\Tab;

use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Payfast\Payfast\Model\Config\Source\SubscriptionType;
use Payfast\Payfast\Model\Payment;

/**
 * Class Charge
 *
 * PHP version 7
 *
 * @category Payfast
 * @package  Payfast_Payfast
 * @author   PayFast
 * @license  https://www.payfast.co.za  Open Software License (OSL 3.0)
 * @link     https://www.payfast.co.za
 */
class Edit extends \Magento\Backend\Block\Widget implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $payment;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        Payment $payment,
        array $data = [],
        ?JsonHelper $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null
    ) {
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
        $this->payment = $payment;
    }

    public function _construct()
    {
        $this->_frontController = 'payfast_adminhtml_payfast_recurring_payment_charge';
        parent::_construct(); // TODO: Change the autogenerated stub
    }

    /**
     * GetTabLabel
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getTabLabel()
    {
        return __('Edit');
    }

    public function getMethodCode()
    {
        return 'payfast';
    }

    public function getChargeUrl()
    {
        return $this->getUrl('*/*/Edit', ['payment' => $this->payment->getId()]);
    }
    /**
     * GetTabTitle
     *
     * @return mixed
     */
    public function getTabTitle()
    {
        return $this->getLabel();
    }


    /**
     * CanShowTab
     *
     * @return bool
     */
    public function canShowTab()
    {
        $pre = __METHOD__ . ' : ';
        $this->_logger->debug($pre . 'bof');
        $this->_logger->debug($pre . ' looking at this id' .$this->payment->getId());
        return (int)$this->payment->getSubscriptionType() === SubscriptionType::RECURRING_SUBSCRIPTION;
    }

    /**
     * IsHidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
