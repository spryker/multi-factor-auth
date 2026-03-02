<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Yves\MultiFactorAuth\Dependency\Client;

use Generated\Shared\Transfer\CustomerTransfer;

interface MultiFactorAuthToCustomerClientInterface
{
    public function isLoggedIn(): bool;

    public function getCustomer(): ?CustomerTransfer;

    public function getCustomerByEmail(CustomerTransfer $customerTransfer): CustomerTransfer;

    public function setCustomer(CustomerTransfer $customerTransfer): CustomerTransfer;
}
