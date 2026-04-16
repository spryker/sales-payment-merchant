<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SalesPaymentMerchant\Business\Merchant\Refund\Checker;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\SalesPaymentMerchantPayoutReversalCollectionTransfer;
use Generated\Shared\Transfer\SalesPaymentMerchantPayoutReversalTransfer;
use Spryker\Zed\SalesPaymentMerchant\Business\Reader\SalesPaymentMerchantPayoutReversalReaderInterface;
use Spryker\Zed\SalesPaymentMerchant\Business\Reader\TransferEndpointReaderInterface;
use Spryker\Zed\SalesPaymentMerchant\SalesPaymentMerchantConfig;
use Spryker\Zed\SalesPaymentMerchantExtension\Communication\Dependency\Plugin\MerchantPayoutTransmissionPluginInterface;

class PaymentMethodPayoutReverseChecker implements PaymentMethodPayoutReverseCheckerInterface
{
    /**
     * @var \Spryker\Zed\SalesPaymentMerchant\Business\Reader\TransferEndpointReaderInterface
     */
    protected TransferEndpointReaderInterface $transferEndpointReader;

    /**
     * @var \Spryker\Zed\SalesPaymentMerchant\Business\Reader\SalesPaymentMerchantPayoutReversalReaderInterface
     */
    protected SalesPaymentMerchantPayoutReversalReaderInterface $salesPaymentMerchantPayoutReversalReader;

    protected ?MerchantPayoutTransmissionPluginInterface $merchantPayoutTransmissionPlugin;

    public function __construct(
        TransferEndpointReaderInterface $transferEndpointReader,
        SalesPaymentMerchantPayoutReversalReaderInterface $salesPaymentMerchantPayoutReversalReader,
        ?MerchantPayoutTransmissionPluginInterface $merchantPayoutTransmissionPlugin = null
    ) {
        $this->transferEndpointReader = $transferEndpointReader;
        $this->salesPaymentMerchantPayoutReversalReader = $salesPaymentMerchantPayoutReversalReader;
        $this->merchantPayoutTransmissionPlugin = $merchantPayoutTransmissionPlugin;
    }

    public function isPayoutReversalSupportedForPaymentMethodUsedForOrder(
        ItemTransfer $salesOrderItemTransfer,
        OrderTransfer $orderTransfer
    ): bool {
        if (!$salesOrderItemTransfer->getMerchantReference()) {
            return true;
        }

        if ($this->merchantPayoutTransmissionPlugin === null) {
            $transferEndpointUrl = $this->transferEndpointReader->getTransferEndpointUrl($orderTransfer);

            if (!$transferEndpointUrl) {
                return true;
            }
        }

        $salesPaymentMerchantPayoutReversalCollectionTransfer = $this->salesPaymentMerchantPayoutReversalReader->getSalesPaymentMerchantPayoutReversalCollectionByMerchantAndOrderReference(
            $orderTransfer->getOrderReferenceOrFail(),
            $salesOrderItemTransfer->getMerchantReferenceOrFail(),
            true,
        );

        $orderItemReferenceMapForMerchant = $this->createOrderItemReferenceMap($salesPaymentMerchantPayoutReversalCollectionTransfer);

        return isset($orderItemReferenceMapForMerchant[$salesOrderItemTransfer->getOrderItemReference()]);
    }

    /**
     * @param \Generated\Shared\Transfer\SalesPaymentMerchantPayoutReversalCollectionTransfer $salesPaymentMerchantPayoutReversalCollectionTransfer
     *
     * @return array<string, string>
     */
    protected function createOrderItemReferenceMap(
        SalesPaymentMerchantPayoutReversalCollectionTransfer $salesPaymentMerchantPayoutReversalCollectionTransfer
    ): array {
        $orderItemReferenceMap = [];

        foreach ($salesPaymentMerchantPayoutReversalCollectionTransfer->getSalesPaymentMerchantPayoutReversals() as $salesPaymentMerchantPayoutReversalTransfer) {
            $orderItemReferenceMap = $this->addOrderItemReferencesToMap($salesPaymentMerchantPayoutReversalTransfer, $orderItemReferenceMap);
        }

        return $orderItemReferenceMap;
    }

    /**
     * @param \Generated\Shared\Transfer\SalesPaymentMerchantPayoutReversalTransfer $salesPaymentMerchantPayoutReversalTransfer
     * @param array<string, string> $orderItemReferenceMap
     *
     * @return array<string, string>
     */
    protected function addOrderItemReferencesToMap(
        SalesPaymentMerchantPayoutReversalTransfer $salesPaymentMerchantPayoutReversalTransfer,
        array $orderItemReferenceMap
    ): array {
        $orderItemReferences = explode(
            SalesPaymentMerchantConfig::ITEM_REFERENCE_SEPARATOR,
            $salesPaymentMerchantPayoutReversalTransfer->getItemReferencesOrFail(),
        );
        foreach ($orderItemReferences as $orderItemReference) {
            $orderItemReferenceMap[$orderItemReference] = $orderItemReference;
        }

        return $orderItemReferenceMap;
    }
}
