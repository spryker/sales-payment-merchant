<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SalesPaymentMerchant\Business\Reader;

use Generated\Shared\Transfer\SalesPaymentMerchantPayoutReversalCollectionTransfer;
use Generated\Shared\Transfer\SalesPaymentMerchantPayoutReversalConditionsTransfer;
use Generated\Shared\Transfer\SalesPaymentMerchantPayoutReversalCriteriaTransfer;
use Spryker\Zed\SalesPaymentMerchant\Persistence\SalesPaymentMerchantRepositoryInterface;

class SalesPaymentMerchantPayoutReversalReader implements SalesPaymentMerchantPayoutReversalReaderInterface
{
    /**
     * @var \Spryker\Zed\SalesPaymentMerchant\Persistence\SalesPaymentMerchantRepositoryInterface
     */
    protected SalesPaymentMerchantRepositoryInterface $salesPaymentMerchantRepository;

    public function __construct(SalesPaymentMerchantRepositoryInterface $salesPaymentMerchantRepository)
    {
        $this->salesPaymentMerchantRepository = $salesPaymentMerchantRepository;
    }

    public function getSalesPaymentMerchantPayoutReversalCollectionByMerchantAndOrderReference(
        string $orderReference,
        string $merchantReference,
        bool $isSuccessful
    ): SalesPaymentMerchantPayoutReversalCollectionTransfer {
        $salesPaymentMerchantPayoutReversalConditionsTransfer = (new SalesPaymentMerchantPayoutReversalConditionsTransfer())
            ->addOrderReference($orderReference)
            ->addMerchantReference($merchantReference)
            ->setIsSuccessful($isSuccessful);

        $salesPaymentMerchantPayoutReversalCriteriaTransfer = (new SalesPaymentMerchantPayoutReversalCriteriaTransfer())
            ->setSalesPaymentMerchantPayoutReversalConditions($salesPaymentMerchantPayoutReversalConditionsTransfer);

        return $this->salesPaymentMerchantRepository->getSalesPaymentMerchantPayoutReversalCollection($salesPaymentMerchantPayoutReversalCriteriaTransfer);
    }
}
