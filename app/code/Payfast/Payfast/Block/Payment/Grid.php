<?php
/**
 * Copyright (c) 2008 PayFast (Pty) Ltd
 * You (being anyone who is not PayFast (Pty) Ltd) may download and use this plugin / code in your own website in conjunction with a registered and active PayFast account. If your PayFast account is terminated for any reason, you may not use this plugin / code or part thereof.
 * Except as expressly indicated in this licence, you may not use, copy, modify or distribute this plugin / code or part thereof in any way.
 */
namespace Payfast\Payfast\Block\Payment;

use Magento\Customer\Controller\RegistryConstants;
use Payfast\Payfast\Block\Payments;
use Payfast\Payfast\Model\Payment;

/**
 * Class Grid
 *
 * @category Payfast
 * @package  Payfast_Payfast
 * @license  https://www.payfast.co.za
 * @link     https://www.payfast.co.za
 */
class Grid extends Payments
{
    /**
     * Registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * Payment
     *
     * @var \Payfast\Payfast\Model\Payfast
     */
    protected $_paymentModel;

    /**
     * Payments
     *
     * @var null
     */
    protected $_payments = null;

    /**
     * Fields
     *
     * @var \Payfast\Payfast\Block\Fields
     */
    protected $_fields;

    /**
     * Session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Grid constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context         context
     * @param \Magento\Framework\Registry                      $registry        registry
     * @param \Payfast\Payfast\Model\Payment $paymentModel    paymentModel
     * @param \Payfast\Payfast\Block\Fields  $fields          fields
     * @param \Magento\Customer\Model\Session                  $customerSession customerSession
     * @param array                                            $data            data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Payfast\Payfast\Model\Payment $paymentModel,
        \Payfast\Payfast\Block\Fields $fields,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        $this->_paymentModel = $paymentModel;
        $this->_registry = $registry;
        $this->_fields = $fields;
        $this->_customerSession = $customerSession;

        parent::__construct($context, $data);
    }

    /**
     * GetPayfastRecurringPayments
     *
     * @param string $fields fields
     *
     * @return bool|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    protected function getPayfastRecurringPayments($fields = '*')
    {
        $customerId = $this->_customerSession->getCustomerId();

        if (!$customerId) {
            return false;
        }

        if (!$this->_payments) {
            $this->_payments = $this->_paymentModel->getCollection()->addFieldToSelect(
                $fields
            )->addFieldToFilter(
                'customer_id',
                $customerId
            )->setOrder(
                'payment_id',
                'desc'
            );
        }
        return $this->_payments;
    }

    /**
     * PrepareLayout
     *
     * @return $this|\Payfast\RecurringPayment\Block\Payments
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getPayfastRecurringPayments(['reference_id', 'schedule_description', 'state', 'recurring_payment_start_date', 'method_code'])) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'sales.payfast_recurring_payment.payments.grid.pager'
            )->setCollection(
                $this->_payments
            );
            $this->setChild('pager', $pager);
            $this->_payments->load();
        }

        $this->setGridColumns(
            [
                new \Magento\Framework\DataObject(
                    [
                        'index' => 'reference_id',
                        'title' => $this->_fields->getFieldLabel('reference_id'),
                        'is_nobr' => true
                    ]
                ),
                new \Magento\Framework\DataObject(
                    [
                        'index' => 'state',
                        'title' => $this->_fields->getFieldLabel('state')
                    ]
                ),
                new \Magento\Framework\DataObject(
                    [
                        'index' => 'schedule_description',
                        'title' => $this->_fields->getFieldLabel('schedule_description'),
                        'is_nobr' => true
                    ]
                ),
                new \Magento\Framework\DataObject(
                    [
                        'index' => 'recurring_payment_start_date',
                        'title' => $this->_fields->getFieldLabel('recurring_payment_start_date'),
                        'is_nobr' => true
                    ]
                ),
                new \Magento\Framework\DataObject(
                    [
                        'index' => 'method_code',
                        'title' => $this->_fields->getFieldLabel('method_code'),
                        'is_nobr' => true
                    ]
                )
            ]
        );

        $payments = [];
        $store = $this->_storeManager->getStore();
        foreach ($this->_payments as $payment) {
            $payment->setStore($store);
            $payments[] = new \Magento\Framework\DataObject(
                [
                    'reference_id' => $payment->getReferenceId(),
                    'state' => $payment->renderData('state'),
                    'schedule_description' => $payment->renderData('schedule_description'),
                    'recurring_payment_start_date' => $payment->getData('recurring_payment_start_date') ? $this->formatDate($payment->getData('recurring_payment_start_date')) : '',
                    'method_code' => $payment->renderData('method_code'),
                    'reference_id_link_url' => $this->getUrl('sales/payfast_recurring_payment/view/', ['payment' => $payment->getId()])
                ]
            );
        }
        if ($payments) {
            $this->setGridElements($payments);
        }

        return $this;
    }

    /**
     * GetPagerHtml
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
