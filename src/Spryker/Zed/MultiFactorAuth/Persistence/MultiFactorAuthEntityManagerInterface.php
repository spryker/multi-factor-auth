<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MultiFactorAuth\Persistence;

use Generated\Shared\Transfer\MultiFactorAuthCodeTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTransfer;

interface MultiFactorAuthEntityManagerInterface
{
    public function saveCustomerCode(MultiFactorAuthTransfer $multiFactorAuthTransfer): void;

    public function saveUserCode(MultiFactorAuthTransfer $multiFactorAuthTransfer): void;

    public function updateCustomerCode(MultiFactorAuthTransfer $multiFactorAuthTransfer): void;

    public function updateUserCode(MultiFactorAuthTransfer $multiFactorAuthTransfer): void;

    public function saveCustomerMultiFactorAuth(MultiFactorAuthTransfer $multiFactorAuthTransfer): void;

    public function deleteCustomerMultiFactorAuth(MultiFactorAuthTransfer $multiFactorAuthTransfer): void;

    public function saveUserMultiFactorAuth(MultiFactorAuthTransfer $multiFactorAuthTransfer): void;

    public function deleteUserMultiFactorAuth(MultiFactorAuthTransfer $multiFactorAuthTransfer): void;

    public function saveCustomerMultiFactorAuthCodeAttempt(MultiFactorAuthCodeTransfer $multiFactorAuthCodeTransfer): void;

    public function saveUserMultiFactorAuthCodeAttempt(MultiFactorAuthCodeTransfer $multiFactorAuthCodeTransfer): void;

    public function invalidateUserCodes(MultiFactorAuthTransfer $multiFactorAuthTransfer): void;

    public function invalidateCustomerCodes(MultiFactorAuthTransfer $multiFactorAuthTransfer): void;
}
