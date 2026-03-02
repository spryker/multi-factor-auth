<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace Spryker\Glue\MultiFactorAuth\Dependency\Facade;

use Generated\Shared\Transfer\MultiFactorAuthCodeCriteriaTransfer;
use Generated\Shared\Transfer\MultiFactorAuthCodeTransfer;
use Generated\Shared\Transfer\MultiFactorAuthCriteriaTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTypesCollectionTransfer;
use Generated\Shared\Transfer\MultiFactorAuthValidationRequestTransfer;
use Generated\Shared\Transfer\MultiFactorAuthValidationResponseTransfer;

interface MultiFactorAuthToMultiFactorAuthFacadeInterface
{
    public function validateUserCode(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthValidationResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\MultiFactorAuthValidationRequestTransfer $multiFactorAuthValidationRequestTransfer
     * @param array<int> $additionalStatuses
     *
     * @return \Generated\Shared\Transfer\MultiFactorAuthValidationResponseTransfer
     */
    public function validateUserMultiFactorAuthStatus(
        MultiFactorAuthValidationRequestTransfer $multiFactorAuthValidationRequestTransfer,
        array $additionalStatuses = []
    ): MultiFactorAuthValidationResponseTransfer;

    public function activateUserMultiFactorAuth(MultiFactorAuthTransfer $multiFactorAuthTransfer): void;

    public function deactivateUserMultiFactorAuth(MultiFactorAuthTransfer $multiFactorAuthTransfer): void;

    public function sendUserCode(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthTransfer;

    public function findUserMultiFactorAuthType(
        MultiFactorAuthCodeCriteriaTransfer $multiFactorAuthCodeCriteriaTransfer
    ): MultiFactorAuthCodeTransfer;

    public function getUserMultiFactorAuthTypes(MultiFactorAuthCriteriaTransfer $multiFactorAuthCriteriaTransfer): MultiFactorAuthTypesCollectionTransfer;
}
