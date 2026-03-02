<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Yves\MultiFactorAuth\Dependency\Client;

use Generated\Shared\Transfer\UserTransfer;

interface MultiFactorAuthToAgentClientInterface
{
    public function isLoggedIn(): bool;

    public function getAgent(): UserTransfer;

    public function findAgentByUsername(UserTransfer $userTransfer): ?UserTransfer;
}
