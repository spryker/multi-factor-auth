<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\MultiFactorAuth\Api\Backend\Processor;

use Generated\Shared\Transfer\MultiFactorAuthCodeTransfer;
use Generated\Shared\Transfer\MultiFactorAuthCriteriaTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Generated\Shared\Transfer\UserTransfer;
use Spryker\Shared\MultiFactorAuth\MultiFactorAuthConstants;

class MultiFactorAuthTypeVerifyBackendProcessor extends AbstractMultiFactorAuthBackendProcessor
{
    protected function processPost(mixed $data): null
    {
        $type = $this->resolveType($data);
        $code = $this->getRequiredMultiFactorAuthCode();
        $userTransfer = $this->getUser();

        if ($this->codeValidator->isTypeActivated($this->codeValidator->getActiveTypes($userTransfer), $type)) {
            throw $this->exceptionFactory->createMultiFactorAuthTypeAlreadyActivatedException();
        }

        if (!$this->isPendingActivation($userTransfer, $type)) {
            throw $this->exceptionFactory->createMultiFactorAuthTypeNotFoundForUserException();
        }

        $multiFactorAuthTransfer = (new MultiFactorAuthTransfer())
            ->setType($type)
            ->setUser($userTransfer)
            ->setMultiFactorAuthCode((new MultiFactorAuthCodeTransfer())->setCode($code))
            ->setStatus(MultiFactorAuthConstants::STATUS_ACTIVE);

        $isCodeValid = $this->codeValidator->isCodeValid(
            $code,
            $userTransfer,
            $multiFactorAuthTransfer,
            additionalStatuses: [MultiFactorAuthConstants::STATUS_PENDING_ACTIVATION],
        );

        if (!$isCodeValid) {
            throw $this->exceptionFactory->createMultiFactorAuthCodeInvalidException();
        }

        $this->multiFactorAuthFacade->activateUserMultiFactorAuth($multiFactorAuthTransfer);

        return null;
    }

    protected function isPendingActivation(UserTransfer $userTransfer, string $type): bool
    {
        $multiFactorAuthCriteriaTransfer = (new MultiFactorAuthCriteriaTransfer())
            ->setUser($userTransfer)
            ->setStatuses([MultiFactorAuthConstants::STATUS_PENDING_ACTIVATION]);

        $pendingTypesCollectionTransfer = $this->multiFactorAuthFacade->getUserMultiFactorAuthTypes($multiFactorAuthCriteriaTransfer);

        foreach ($pendingTypesCollectionTransfer->getMultiFactorAuthTypes() as $pendingMultiFactorAuthTransfer) {
            if ($pendingMultiFactorAuthTransfer->getType() === $type) {
                return true;
            }
        }

        return false;
    }
}
