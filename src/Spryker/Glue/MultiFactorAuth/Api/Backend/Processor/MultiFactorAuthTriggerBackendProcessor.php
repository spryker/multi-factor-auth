<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\MultiFactorAuth\Api\Backend\Processor;

use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Spryker\Shared\MultiFactorAuth\MultiFactorAuthConstants;

class MultiFactorAuthTriggerBackendProcessor extends AbstractMultiFactorAuthBackendProcessor
{
    protected function processPost(mixed $data): null
    {
        $type = $this->resolveType($data);
        $userTransfer = $this->getUser();

        if (!$this->codeValidator->isTypeActivated($this->codeValidator->getActiveTypes($userTransfer), $type)) {
            throw $this->exceptionFactory->createMultiFactorAuthTypeNotFoundForUserException();
        }

        $multiFactorAuthTransfer = (new MultiFactorAuthTransfer())
            ->setType($type)
            ->setUser($userTransfer)
            ->setStatus(MultiFactorAuthConstants::STATUS_ACTIVE);

        $this->sendUserCode($multiFactorAuthTransfer);

        return null;
    }
}
