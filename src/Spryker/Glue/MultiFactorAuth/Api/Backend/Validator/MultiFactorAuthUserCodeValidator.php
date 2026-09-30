<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\MultiFactorAuth\Api\Backend\Validator;

use Generated\Shared\Transfer\MultiFactorAuthCodeCriteriaTransfer;
use Generated\Shared\Transfer\MultiFactorAuthCodeTransfer;
use Generated\Shared\Transfer\MultiFactorAuthCriteriaTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTypesCollectionTransfer;
use Generated\Shared\Transfer\MultiFactorAuthValidationRequestTransfer;
use Generated\Shared\Transfer\UserTransfer;
use Spryker\Shared\MultiFactorAuth\MultiFactorAuthConstants;
use Spryker\Zed\MultiFactorAuth\Business\MultiFactorAuthFacadeInterface;

/**
 * Code and type checks shared by the MFA resources and the protected-resource request check.
 */
class MultiFactorAuthUserCodeValidator
{
    public function __construct(
        protected MultiFactorAuthFacadeInterface $multiFactorAuthFacade,
    ) {
    }

    public function getActiveTypes(UserTransfer $userTransfer): MultiFactorAuthTypesCollectionTransfer
    {
        $multiFactorAuthCriteriaTransfer = (new MultiFactorAuthCriteriaTransfer())->setUser($userTransfer);

        return $this->multiFactorAuthFacade->getUserMultiFactorAuthTypes($multiFactorAuthCriteriaTransfer);
    }

    public function isTypeActivated(MultiFactorAuthTypesCollectionTransfer $multiFactorAuthTypesCollectionTransfer, string $type): bool
    {
        foreach ($multiFactorAuthTypesCollectionTransfer->getMultiFactorAuthTypes() as $activatedMultiFactorAuthTransfer) {
            if ($activatedMultiFactorAuthTransfer->getTypeOrFail() === $type) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the stored code matching the given value; the transfer is empty when there is none.
     */
    public function findCode(string $code, UserTransfer $userTransfer, ?string $expectedType = null): MultiFactorAuthCodeTransfer
    {
        $multiFactorAuthCodeCriteriaTransfer = (new MultiFactorAuthCodeCriteriaTransfer())
            ->setCode($code)
            ->setUser($userTransfer)
            ->setType($expectedType);

        return $this->multiFactorAuthFacade->findUserMultiFactorAuthType($multiFactorAuthCodeCriteriaTransfer);
    }

    /**
     * @param array<int> $additionalStatuses
     */
    public function isCodeValid(
        string $code,
        UserTransfer $userTransfer,
        MultiFactorAuthTransfer $multiFactorAuthTransfer,
        ?string $expectedType = null,
        array $additionalStatuses = [],
    ): bool {
        $multiFactorAuthCodeTransfer = $this->findCode($code, $userTransfer, $expectedType);

        if ($multiFactorAuthCodeTransfer->getIdCode() === null) {
            return false;
        }

        if ($multiFactorAuthCodeTransfer->getTypeOrFail() !== $multiFactorAuthTransfer->getTypeOrFail()) {
            return false;
        }

        if ($multiFactorAuthCodeTransfer->getStatusOrFail() === MultiFactorAuthConstants::STATUS_ACTIVE) {
            $multiFactorAuthValidationRequestTransfer = (new MultiFactorAuthValidationRequestTransfer())
                ->setUser($userTransfer)
                ->setAdditionalStatuses($additionalStatuses);

            $multiFactorAuthValidationResponseTransfer = $this->multiFactorAuthFacade->validateUserMultiFactorAuthStatus(
                $multiFactorAuthValidationRequestTransfer,
            );

            return $multiFactorAuthValidationResponseTransfer->getIsRequired() === false;
        }

        $multiFactorAuthValidationResponseTransfer = $this->multiFactorAuthFacade->validateUserCode($multiFactorAuthTransfer);

        return $multiFactorAuthValidationResponseTransfer->getStatus() === MultiFactorAuthConstants::CODE_VERIFIED;
    }
}
