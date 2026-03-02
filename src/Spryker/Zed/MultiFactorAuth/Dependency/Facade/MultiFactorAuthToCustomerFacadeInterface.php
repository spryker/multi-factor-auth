<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MultiFactorAuth\Dependency\Facade;

use Generated\Shared\Transfer\CustomerCriteriaTransfer;
use Generated\Shared\Transfer\CustomerResponseTransfer;

interface MultiFactorAuthToCustomerFacadeInterface
{
    public function getCustomerByCriteria(CustomerCriteriaTransfer $customerCriteriaTransfer): CustomerResponseTransfer;
}
