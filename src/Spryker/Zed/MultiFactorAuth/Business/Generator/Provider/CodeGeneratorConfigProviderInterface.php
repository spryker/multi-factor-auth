<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MultiFactorAuth\Business\Generator\Provider;

interface CodeGeneratorConfigProviderInterface
{
    public function getCodeLength(): int;

    public function getCodeValidityTtl(): int;
}
