<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SalesPaymentMerchant\Business\Merchant\Refund;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PaymentTransmissionResponseTransfer;
use Spryker\Zed\SalesPaymentMerchant\Business\Merchant\AbstractMerchantTransfer;

class MerchantPayoutReverse extends AbstractMerchantTransfer implements MerchantPayoutReverseInterface
{
    /**
     * @param list<\Generated\Shared\Transfer\ItemTransfer> $salesOrderItemTransfers
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return void
     */
    public function reversePayoutMerchants(array $salesOrderItemTransfers, OrderTransfer $orderTransfer): void
    {
        $transferEndpointUrl = null;

        if ($this->merchantPayoutTransmissionPlugin === null) {
            $transferEndpointUrl = $this->transferEndpointReader->getTransferEndpointUrl($orderTransfer);

            if (!$transferEndpointUrl) {
                return;
            }
        }

        $orderItemPaymentTransmissionItemTransfers = $this->getOrderItemsForTransfer($salesOrderItemTransfers, $orderTransfer);

        if (count($orderItemPaymentTransmissionItemTransfers) === 0) {
            return;
        }

        $orderItemPaymentTransmissionItemTransfers = $this->paymentTransmissionItemExpander
            ->expandPaymentTransmissionItemsWithTransferId($orderItemPaymentTransmissionItemTransfers, $orderTransfer);
        $this->executeGroupedPayoutTransmissionTransaction($orderItemPaymentTransmissionItemTransfers, $orderTransfer, $transferEndpointUrl);

        if (!$this->salesPaymentMerchantConfig->isOrderExpenseIncludedInPaymentProcess()) {
            return;
        }

        $orderExpensePaymentTransmissionItemTransfers = $this->orderExpenseReader->getOrderExpensesForTransfer($orderTransfer, $orderItemPaymentTransmissionItemTransfers);

        if (count($orderExpensePaymentTransmissionItemTransfers) === 0) {
            return;
        }

        $orderExpensePaymentTransmissionItemTransfers = $this->paymentTransmissionItemExpander
            ->expandPaymentTransmissionItemsWithTransferId($orderExpensePaymentTransmissionItemTransfers, $orderTransfer);
        $this->executePayoutTransmissionTransaction($orderExpensePaymentTransmissionItemTransfers, $orderTransfer, $transferEndpointUrl);
    }

    /**
     * @param list<\Generated\Shared\Transfer\PaymentTransmissionItemTransfer> $orderItemPaymentTransmissionItemTransfers
     */
    public function executeGroupedPayoutTransmissionTransaction(
        array $orderItemPaymentTransmissionItemTransfers,
        OrderTransfer $orderTransfer,
        ?string $transferEndpointUrl = null
    ): void {
        $groupedPaymentTransmissionItemsByTransferId = $this->groupPaymentTransmissionItemsByTransferId($orderItemPaymentTransmissionItemTransfers);

        foreach ($groupedPaymentTransmissionItemsByTransferId as $paymentTransmissionItemTransfers) {
            $this->executePayoutTransmissionTransaction($paymentTransmissionItemTransfers, $orderTransfer, $transferEndpointUrl);
        }
    }

    protected function savePaymentTransmissionResponse(
        PaymentTransmissionResponseTransfer $paymentTransmissionResponseTransfer
    ): void {
        $this->salesPaymentMerchantEntityManager->saveSalesPaymentMerchantPayoutReversal($paymentTransmissionResponseTransfer);
    }

    protected function calculatePayoutAmount(ItemTransfer $itemTransfer, OrderTransfer $orderTransfer): int
    {
        return $this->merchantPayoutCalculator->calculatePayoutAmount($itemTransfer, $orderTransfer) * -1;
    }

    /**
     * @param list<\Generated\Shared\Transfer\PaymentTransmissionItemTransfer> $orderItemPaymentTransmissionItemTransfers
     *
     * @return array<string, list<\Generated\Shared\Transfer\PaymentTransmissionItemTransfer>>
     */
    protected function groupPaymentTransmissionItemsByTransferId(array $orderItemPaymentTransmissionItemTransfers): array
    {
        $groupedPaymentTransmissionItems = [];
        foreach ($orderItemPaymentTransmissionItemTransfers as $paymentTransmissionItemTransfer) {
            $groupedPaymentTransmissionItems[$paymentTransmissionItemTransfer->getTransferIdOrFail()][] = $paymentTransmissionItemTransfer;
        }

        return $groupedPaymentTransmissionItems;
    }
}
