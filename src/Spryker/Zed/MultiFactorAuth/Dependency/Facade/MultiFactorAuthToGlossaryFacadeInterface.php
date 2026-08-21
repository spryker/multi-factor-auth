<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MultiFactorAuth\Dependency\Facade;

use Generated\Shared\Transfer\LocaleTransfer;

interface MultiFactorAuthToGlossaryFacadeInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function translate(string $keyName, array $data = [], ?LocaleTransfer $localeTransfer = null): string;
}
