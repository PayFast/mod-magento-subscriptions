<?php


namespace Payfast\Payfast\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class SubscriptionType extends AbstractSource implements \Magento\Framework\Data\OptionSourceInterface
{
    const RECURRING_SUBSCRIPTION = 1; // Recurring Subscription
    const RECURRING_ADHOC = 2; // Ad hoc
    const RECURRING_LABEL = [
        self::RECURRING_SUBSCRIPTION => 'Recurring',
        self::RECURRING_ADHOC => 'Adhoc'
    ];



    public function getAllOptions()
    {
        $this->_options = [
            ['value' => self::RECURRING_SUBSCRIPTION, 'label' => __(self::RECURRING_LABEL[self::RECURRING_SUBSCRIPTION])],
            ['value' => self::RECURRING_ADHOC, 'label' => __(self::RECURRING_LABEL[self::RECURRING_ADHOC])],
        ];

        return $this->_options;
    }


    public function toOptionArray()
    {
        return self::RECURRING_LABEL;
    }
}
