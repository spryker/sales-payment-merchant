<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SalesPaymentMerchant\Persistence;

use Orm\Zed\SalesPaymentMerchant\Persistence\SpySalesPaymentMerchantPayoutQuery;
use Orm\Zed\SalesPaymentMerchant\Persistence\SpySalesPaymentMerchantPayoutReversalQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;
use Spryker\Zed\SalesPaymentMerchant\Persistence\Propel\Mapper\SalesPaymentMerchantPayoutMapper;
use Spryker\Zed\SalesPaymentMerchant\Persistence\Propel\Mapper\SalesPaymentMerchantPayoutReversalMapper;

/**
 * @method \Spryker\Zed\SalesPaymentMerchant\SalesPaymentMerchantConfig getConfig()
 * @method \Spryker\Zed\SalesPaymentMerchant\Persistence\SalesPaymentMerchantRepositoryInterface getRepository()
 * @method \Spryker\Zed\SalesPaymentMerchant\Persistence\SalesPaymentMerchantEntityManagerInterface getEntityManager()
 */
class SalesPaymentMerchantPersistenceFactory extends AbstractPersistenceFactory
{
    public function getSalesPaymentMerchantPayoutQuery(): SpySalesPaymentMerchantPayoutQuery
    {
        return SpySalesPaymentMerchantPayoutQuery::create();
    }

    public function getSalesPaymentMerchantPayoutReversalQuery(): SpySalesPaymentMerchantPayoutReversalQuery
    {
        return SpySalesPaymentMerchantPayoutReversalQuery::create();
    }

    public function createSalesPaymentMerchantPayoutMapper(): SalesPaymentMerchantPayoutMapper
    {
        return new SalesPaymentMerchantPayoutMapper();
    }

    public function createSalesPaymentMerchantPayoutReversalMapper(): SalesPaymentMerchantPayoutReversalMapper
    {
        return new SalesPaymentMerchantPayoutReversalMapper();
    }
}
