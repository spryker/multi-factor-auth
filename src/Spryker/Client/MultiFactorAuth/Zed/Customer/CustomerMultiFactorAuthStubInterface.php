<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\MultiFactorAuth\Zed\Customer;

use Generated\Shared\Transfer\MultiFactorAuthCodeCriteriaTransfer;
use Generated\Shared\Transfer\MultiFactorAuthCodeTransfer;
use Generated\Shared\Transfer\MultiFactorAuthCriteriaTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTypesCollectionTransfer;
use Generated\Shared\Transfer\MultiFactorAuthValidationRequestTransfer;
use Generated\Shared\Transfer\MultiFactorAuthValidationResponseTransfer;

interface CustomerMultiFactorAuthStubInterface
{
    public function getCustomerMultiFactorAuthTypes(MultiFactorAuthCriteriaTransfer $multiFactorAuthCriteriaTransfer): MultiFactorAuthTypesCollectionTransfer;

    public function sendCustomerCode(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthTransfer;

    public function validateCustomerMultiFactorAuthStatus(
        MultiFactorAuthValidationRequestTransfer $multiFactorAuthValidationRequestTransfer
    ): MultiFactorAuthValidationResponseTransfer;

    public function validateCustomerCode(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthValidationResponseTransfer;

    public function activateCustomerMultiFactorAuth(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthTransfer;

    public function deactivateCustomerMultiFactorAuth(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthTransfer;

    public function findCustomerMultiFactorAuthType(
        MultiFactorAuthCodeCriteriaTransfer $multiFactorAuthCodeCriteriaTransfer
    ): MultiFactorAuthCodeTransfer;

    public function invalidateCustomerCodes(MultiFactorAuthTransfer $multiFactorAuthTransfer): void;
}
