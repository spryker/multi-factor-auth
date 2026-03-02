<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace Spryker\Glue\MultiFactorAuth\BackendApi\TransferBuilder;

use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\MultiFactorAuthCodeTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Generated\Shared\Transfer\UserConditionsTransfer;
use Generated\Shared\Transfer\UserCriteriaTransfer;
use Generated\Shared\Transfer\UserTransfer;

class MultiFactorAuthTransferBuilder implements MultiFactorAuthTransferBuilderInterface
{
    public function buildMultiFactorAuthCodeTransfer(string $multiFactorAuthCode): MultiFactorAuthCodeTransfer
    {
        return (new MultiFactorAuthCodeTransfer())->setCode($multiFactorAuthCode);
    }

    public function buildMultiFactorAuthTransfer(
        string $multiFactorAuthType,
        UserTransfer $userTransfer,
        ?MultiFactorAuthCodeTransfer $multiFactorAuthCodeTransfer = null,
        ?int $status = null
    ): MultiFactorAuthTransfer {
        $multiFactorAuthTransfer = (new MultiFactorAuthTransfer())
            ->setType($multiFactorAuthType)
            ->setUser($userTransfer);

        if ($multiFactorAuthCodeTransfer !== null) {
            $multiFactorAuthTransfer->setMultiFactorAuthCode($multiFactorAuthCodeTransfer);
        }

        if ($status !== null) {
            $multiFactorAuthTransfer->setStatus($status);
        }

        return $multiFactorAuthTransfer;
    }

    public function buildUserTransfer(GlueRequestTransfer $glueRequestTransfer): UserTransfer
    {
        return (new UserTransfer())
            ->setIdUser($glueRequestTransfer->getRequestUserOrFail()->getSurrogateIdentifierOrFail());
    }

    /**
     * @param array<int> $userUuids
     *
     * @return \Generated\Shared\Transfer\UserCriteriaTransfer
     */
    public function createUserCriteriaTransfer(array $userUuids): UserCriteriaTransfer
    {
        $userConditionsTransfer = (new UserConditionsTransfer())->setUserIds($userUuids);

        return (new UserCriteriaTransfer())->setUserConditions($userConditionsTransfer);
    }
}
