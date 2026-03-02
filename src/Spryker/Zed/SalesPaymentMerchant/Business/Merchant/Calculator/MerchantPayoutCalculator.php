<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SalesPaymentMerchant\Business\Merchant\Calculator;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Spryker\Zed\SalesPaymentMerchantExtension\Communication\Dependency\Plugin\MerchantPayoutCalculatorPluginInterface;

class MerchantPayoutCalculator implements MerchantPayoutCalculatorInterface
{
    /**
     * @var \Spryker\Zed\SalesPaymentMerchantExtension\Communication\Dependency\Plugin\MerchantPayoutCalculatorPluginInterface|null
     */
    protected ?MerchantPayoutCalculatorPluginInterface $amountCalculatorPlugin;

    /**
     * @var \Spryker\Zed\SalesPaymentMerchantExtension\Communication\Dependency\Plugin\MerchantPayoutCalculatorPluginInterface
     */
    protected MerchantPayoutCalculatorPluginInterface $amountCalculatorFallback;

    public function __construct(
        MerchantPayoutCalculatorPluginInterface $amountCalculatorFallback,
        ?MerchantPayoutCalculatorPluginInterface $amountCalculatorPlugin
    ) {
        $this->amountCalculatorFallback = $amountCalculatorFallback;
        $this->amountCalculatorPlugin = $amountCalculatorPlugin;
    }

    public function calculatePayoutAmount(ItemTransfer $itemTransfer, OrderTransfer $orderTransfer): int
    {
        if ($this->amountCalculatorPlugin !== null) {
            return $this->amountCalculatorPlugin->calculatePayoutAmount($itemTransfer, $orderTransfer);
        }

        return $this->amountCalculatorFallback->calculatePayoutAmount($itemTransfer, $orderTransfer);
    }
}
