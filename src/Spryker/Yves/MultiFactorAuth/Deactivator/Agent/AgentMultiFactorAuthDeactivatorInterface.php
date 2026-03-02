<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Yves\MultiFactorAuth\Deactivator\Agent;

use Generated\Shared\Transfer\UserTransfer;
use Symfony\Component\HttpFoundation\Request;

interface AgentMultiFactorAuthDeactivatorInterface
{
    public function deactivate(Request $request, UserTransfer $userTransfer): void;
}
