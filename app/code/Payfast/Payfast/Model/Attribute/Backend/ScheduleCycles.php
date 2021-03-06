<?php
/**
 * Copyright (c) 2008 PayFast (Pty) Ltd
 * You (being anyone who is not PayFast (Pty) Ltd) may download and use this plugin / code in your own website in conjunction with a registered and active PayFast account. If your PayFast account is terminated for any reason, you may not use this plugin / code or part thereof.
 * Except as expressly indicated in this licence, you may not use, copy, modify or distribute this plugin / code or part thereof in any way.
 */
namespace Payfast\Payfast\Model\Attribute\Backend;

use Payfast\Payfast\Model\Config\Source\SubscriptionType;

/**
 * Class ScheduleDescription
 *
 * @category Payfast
 * @package  Payfast_Payfast
 * @license  https://www.payfast.co.za
 * @link     https://www.payfast.co.za
 */
class ScheduleCycles extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * Method validate
     *
     * @param \Magento\Framework\DataObject $object object
     *
     * @return boolean
     */
    public function validate($object)
    {
        $attribute_code = $this->getAttribute()->getAttributeCode();
        $value = $object->getData($attribute_code);

        $parent_attribute_value = $object->getData('pf_billing_period_max_cycles');
        if ($parent_attribute_value && trim($value) == '') {
            throw new \Magento\Framework\Exception\LocalizedException(__("Enter a value for PayFast Cycles payment."));
        }


        if ((int) $object->getData('subscription_type') === SubscriptionType::RECURRING_SUBSCRIPTION && !is_numeric($value)) {
            throw new \Magento\Framework\Exception\LocalizedException(__("PayFast Recurring Payment - Cycles has to be a numeric value."));
        }

        return true;
    }
}
