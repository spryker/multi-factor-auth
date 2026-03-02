<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\MultiFactorAuth\Zed\Agent;

use Generated\Shared\Transfer\MultiFactorAuthCriteriaTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTypesCollectionTransfer;
use Generated\Shared\Transfer\MultiFactorAuthValidationRequestTransfer;
use Generated\Shared\Transfer\MultiFactorAuthValidationResponseTransfer;

interface AgentMultiFactorAuthStubInterface
{
    public function getAgentMultiFactorAuthTypes(MultiFactorAuthCriteriaTransfer $multiFactorAuthCriteriaTransfer): MultiFactorAuthTypesCollectionTransfer;

    public function validateAgentMultiFactorAuthStatus(
        MultiFactorAuthValidationRequestTransfer $multiFactorAuthValidationRequestTransfer
    ): MultiFactorAuthValidationResponseTransfer;

    public function sendAgentCode(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthTransfer;

    public function validateAgentCode(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthValidationResponseTransfer;

    public function activateAgentMultiFactorAuth(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthTransfer;

    public function deactivateAgentMultiFactorAuth(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthTransfer;

    public function invalidateAgentCodes(MultiFactorAuthTransfer $multiFactorAuthTransfer): void;
}
