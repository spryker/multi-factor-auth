<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MultiFactorAuth\Business\Generator;

use Generated\Shared\Transfer\MultiFactorAuthTransfer;

interface CodeGeneratorInterface
{
    public function generateCode(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthTransfer;
}
