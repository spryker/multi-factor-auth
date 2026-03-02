<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MultiFactorAuth\Business\Generator\Provider\User;

use Spryker\Zed\MultiFactorAuth\Business\Generator\Provider\CodeGeneratorConfigProviderInterface;
use Spryker\Zed\MultiFactorAuth\MultiFactorAuthConfig;

class UserCodeGeneratorConfigProvider implements CodeGeneratorConfigProviderInterface
{
    public function __construct(protected MultiFactorAuthConfig $config)
    {
    }

    public function getCodeLength(): int
    {
        return $this->config->getUserCodeLength();
    }

    public function getCodeValidityTtl(): int
    {
        return $this->config->getUserCodeValidityTtl();
    }
}
