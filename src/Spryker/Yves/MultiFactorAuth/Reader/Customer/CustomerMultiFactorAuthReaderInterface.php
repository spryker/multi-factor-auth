<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Yves\MultiFactorAuth\Reader\Customer;

use Generated\Shared\Transfer\MultiFactorAuthCriteriaTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTypesCollectionTransfer;

interface CustomerMultiFactorAuthReaderInterface
{
    public function getAvailableCustomerMultiFactorAuthTypes(
        MultiFactorAuthCriteriaTransfer $multiFactorAuthCriteriaTransfer
    ): MultiFactorAuthTypesCollectionTransfer;

    public function isCustomerMultiFactorAuthPluginsAvailable(): bool;
}
