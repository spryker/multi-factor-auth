<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MultiFactorAuth\Business\Sender;

use Generated\Shared\Transfer\MultiFactorAuthTransfer;

interface CodeSenderInterface
{
    public function sendCode(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthTransfer;
}
