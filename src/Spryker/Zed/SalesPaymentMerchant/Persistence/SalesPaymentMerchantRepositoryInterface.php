<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SalesPaymentMerchant\Persistence;

use Generated\Shared\Transfer\SalesPaymentMerchantPayoutCollectionTransfer;
use Generated\Shared\Transfer\SalesPaymentMerchantPayoutCriteriaTransfer;
use Generated\Shared\Transfer\SalesPaymentMerchantPayoutReversalCollectionTransfer;
use Generated\Shared\Transfer\SalesPaymentMerchantPayoutReversalCriteriaTransfer;

interface SalesPaymentMerchantRepositoryInterface
{
    public function getSalesPaymentMerchantPayoutCollection(
        SalesPaymentMerchantPayoutCriteriaTransfer $salesPaymentMerchantPayoutCriteriaTransfer
    ): SalesPaymentMerchantPayoutCollectionTransfer;

    public function getSalesPaymentMerchantPayoutReversalCollection(
        SalesPaymentMerchantPayoutReversalCriteriaTransfer $salesPaymentMerchantPayoutReversalCriteriaTransfer
    ): SalesPaymentMerchantPayoutReversalCollectionTransfer;
}
