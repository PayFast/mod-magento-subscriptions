<?php
/**
 * Copyright (c) 2008 PayFast (Pty) Ltd
 * You (being anyone who is not PayFast (Pty) Ltd) may download and use this plugin / code in your own website in conjunction with a registered and active PayFast account. If your PayFast account is terminated for any reason, you may not use this plugin / code or part thereof.
 * Except as expressly indicated in this licence, you may not use, copy, modify or distribute this plugin / code or part thereof in any way.
 */
namespace Payfast\Payfast\Model\Totals;

use Magento\Catalog\Model\ProductRepository;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Payfast\Payfast\Model\Config\Source\SubscriptionType;
use Payfast\Payfast\Model\PayfastRecurringPayment;

/**
 * PayFast Module.
 *
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Custom extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{

    /**
     * @var ProductRepository
     */
    private $productRepository;

    private $priceCurrency;
    public function __construct(\Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency, ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;

        $this->priceCurrency = $priceCurrency;
    }


    /**
     * collect
     *
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return Custom
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ): Custom {
        parent::collect($quote, $shippingAssignment, $total);

        $discount = $this->evaluateDiscount($quote);
        if ($discount) {

            $baseDiscount = 10;

            $discount =  $this->priceCurrency->convert($discount);

            $total->addTotalAmount($this->getCode(), -$discount);
            $total->addBaseTotalAmount($this->getCode(), -$baseDiscount);
            $total->setBaseGrandTotal($total->getBaseGrandTotal() - $baseDiscount);
            $quote->setDiscount(-$discount);
        }


        return $this;
    }

    /**
     * How do I calculate discount in percentages?
     * 1. Subtract the final price from the original price.
     * 2. Divide this number by the original price.
     * 3. Finally, multiply the result by 100.
     * 4. You've obtained a discount in percentages. How awesome!
     *
     * @param Quote $quote
     * @return null|float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function evaluateDiscount(Quote $quote)
    {
        $baseDiscountAmount = 0;

        foreach ($quote->getAllVisibleItems() as $item) {
            if ($item->getIsPayfastRecurring()) {
                $product = $this->productRepository->getById($item->getProduct()->getId());
                if ((int) $product->getSubscriptionType() === SubscriptionType::RECURRING_SUBSCRIPTION && !is_null($product->getPfInitialAmount())) {

                    $discountPercentage = (($product->getPrice() - $product->getPfInitialAmount()) / $product->getPrice()) * 100;
                    $baseDiscountAmount = ($discountPercentage / 100) * $product->getPrice();
                }
            }
        }
        return $baseDiscountAmount;
    }

    public function fetch(
        Quote $quote,
        Total $total
    ) {

        $discount = $this->evaluateDiscount($quote);

        return [
            'code' => $this->getCode(),
            'title' => $this->getLabel(),
            'value' => -$discount  //You can change the reduced amount, or replace it with your own variable
        ];
    }
}