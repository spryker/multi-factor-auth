<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace Spryker\Zed\MultiFactorAuth\Persistence;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\MultiFactorAuthCodeCriteriaTransfer;
use Generated\Shared\Transfer\MultiFactorAuthCodeTransfer;
use Generated\Shared\Transfer\MultiFactorAuthCriteriaTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTypesCollectionTransfer;
use Generated\Shared\Transfer\UserTransfer;

interface MultiFactorAuthRepositoryInterface
{
    public function getCustomerCode(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthCodeTransfer;

    public function getCustomerMultiFactorAuthTypes(MultiFactorAuthCriteriaTransfer $multiFactorAuthCriteriaTransfer): MultiFactorAuthTypesCollectionTransfer;

    public function getUserCode(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthCodeTransfer;

    public function getUserMultiFactorAuthTypes(MultiFactorAuthCriteriaTransfer $multiFactorAuthCriteriaTransfer): MultiFactorAuthTypesCollectionTransfer;

    public function getVerifiedCustomerMultiFactorAuthType(CustomerTransfer $customerTransfer): ?string;

    public function getVerifiedUserMultiFactorAuthType(UserTransfer $userTransfer): ?string;

    public function getCustomerCodeEnteringAttemptsCount(MultiFactorAuthCodeTransfer $multiFactorAuthCodeTransfer): int;

    public function getUserCodeEnteringAttemptsCount(MultiFactorAuthCodeTransfer $multiFactorAuthCodeTransfer): int;

    public function findCustomerMultiFactorAuthCodeByCriteria(
        MultiFactorAuthCodeCriteriaTransfer $multiFactorAuthCodeCriteriaTransfer
    ): MultiFactorAuthCodeTransfer;

    public function findUserMultiFactorAuthCodeByCriteria(
        MultiFactorAuthCodeCriteriaTransfer $multiFactorAuthCodeCriteriaTransfer
    ): MultiFactorAuthCodeTransfer;
}
