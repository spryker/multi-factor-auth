<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MultiFactorAuth\Business\Validator;

use DateTime;
use Generated\Shared\Transfer\MultiFactorAuthCodeTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTypesCollectionTransfer;
use Generated\Shared\Transfer\MultiFactorAuthValidationRequestTransfer;
use Generated\Shared\Transfer\MultiFactorAuthValidationResponseTransfer;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Shared\MultiFactorAuth\MultiFactorAuthConstants;

abstract class AbstractMultiFactorAuthStatusValidator implements MultiFactorAuthStatusValidatorInterface
{
    /**
     * @param \Generated\Shared\Transfer\MultiFactorAuthValidationRequestTransfer $multiFactorAuthValidationRequestTransfer
     * @param array<int> $statuses
     * @param \DateTime|null $currentDateTime
     *
     * @return \Generated\Shared\Transfer\MultiFactorAuthValidationResponseTransfer
     */
    public function validate(
        MultiFactorAuthValidationRequestTransfer $multiFactorAuthValidationRequestTransfer,
        array $statuses = [],
        ?DateTime $currentDateTime = null
    ): MultiFactorAuthValidationResponseTransfer {
        $entityTransfer = $this->extractEntity($multiFactorAuthValidationRequestTransfer);
        $multiFactorAuthTypesCollectionTransfer = $this->getMultiFactorAuthTypesCollectionTransfer($entityTransfer, $statuses);

        if ($multiFactorAuthTypesCollectionTransfer->getMultiFactorAuthTypes()->count() === 0 && $multiFactorAuthValidationRequestTransfer->getIsActivation() !== true) {
            return $this->createMultiFactorAuthValidationResponseTransfer();
        }

        $multiFactorAuthCodeTransfer = $this->getCode(
            $this->buildMultiFactorAuthTransfer($entityTransfer),
        );
        $currentDateTime = $currentDateTime ?? new DateTime();

        if (
            $multiFactorAuthCodeTransfer->getCode() === null ||
            $multiFactorAuthCodeTransfer->getStatus() !== MultiFactorAuthConstants::CODE_VERIFIED ||
            new DateTime($multiFactorAuthCodeTransfer->getExpirationDateOrFail()) < $currentDateTime ||
            $multiFactorAuthValidationRequestTransfer->getIsLogin() === true
        ) {
            return $this->createMultiFactorAuthValidationResponseTransfer(true, $multiFactorAuthCodeTransfer->getStatus());
        }

        if ($multiFactorAuthValidationRequestTransfer->getType() !== null && $multiFactorAuthValidationRequestTransfer->getType() !== $multiFactorAuthCodeTransfer->getType() && $multiFactorAuthValidationRequestTransfer->getIsDeactivation() !== true) {
            return $this->createMultiFactorAuthValidationResponseTransfer(true, MultiFactorAuthConstants::CODE_BLOCKED);
        }

        return $this->createMultiFactorAuthValidationResponseTransfer();
    }

    protected function createMultiFactorAuthValidationResponseTransfer(
        bool $isRequired = false,
        ?int $status = MultiFactorAuthConstants::CODE_VERIFIED
    ): MultiFactorAuthValidationResponseTransfer {
        return (new MultiFactorAuthValidationResponseTransfer())
            ->setStatus($status)
            ->setIsRequired($isRequired);
    }

    abstract protected function extractEntity(MultiFactorAuthValidationRequestTransfer $multiFactorAuthValidationRequestTransfer): AbstractTransfer;

    /**
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer $entityTransfer
     * @param array<int> $statuses
     *
     * @return \Generated\Shared\Transfer\MultiFactorAuthTypesCollectionTransfer
     */
    abstract protected function getMultiFactorAuthTypesCollectionTransfer(
        AbstractTransfer $entityTransfer,
        array $statuses = []
    ): MultiFactorAuthTypesCollectionTransfer;

    abstract protected function getCode(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthCodeTransfer;

    abstract protected function buildMultiFactorAuthTransfer(AbstractTransfer $entityTransfer): MultiFactorAuthTransfer;
}
