<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Yves\MultiFactorAuth\Activator\Agent;

use Generated\Shared\Transfer\UserTransfer;
use Symfony\Component\HttpFoundation\Request;

interface AgentMultiFactorAuthActivatorInterface
{
    public function activate(Request $request, UserTransfer $userTransfer): void;
}
