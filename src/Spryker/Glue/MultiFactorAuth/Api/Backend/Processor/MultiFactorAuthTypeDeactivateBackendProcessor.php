<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\MultiFactorAuth\Api\Backend\Processor;

use Generated\Shared\Transfer\MultiFactorAuthCodeTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTransfer;

class MultiFactorAuthTypeDeactivateBackendProcessor extends AbstractMultiFactorAuthBackendProcessor
{
    protected function processPost(mixed $data): null
    {
        $type = $this->resolveType($data);
        $code = $this->getRequiredMultiFactorAuthCode();
        $userTransfer = $this->getUser();

        if (!$this->codeValidator->isTypeActivated($this->codeValidator->getActiveTypes($userTransfer), $type)) {
            throw $this->exceptionFactory->createMultiFactorAuthTypeNotFoundForUserException();
        }

        $multiFactorAuthTransfer = (new MultiFactorAuthTransfer())
            ->setType($type)
            ->setUser($userTransfer)
            ->setMultiFactorAuthCode((new MultiFactorAuthCodeTransfer())->setCode($code));

        if (!$this->codeValidator->isCodeValid($code, $userTransfer, $multiFactorAuthTransfer, expectedType: $type)) {
            throw $this->exceptionFactory->createMultiFactorAuthCodeInvalidException();
        }

        $this->multiFactorAuthFacade->deactivateUserMultiFactorAuth($multiFactorAuthTransfer);

        return null;
    }
}
