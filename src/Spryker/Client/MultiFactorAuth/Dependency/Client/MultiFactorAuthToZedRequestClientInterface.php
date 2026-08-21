<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\MultiFactorAuth\Dependency\Client;

use Spryker\Shared\Kernel\Transfer\TransferInterface;

interface MultiFactorAuthToZedRequestClientInterface
{
    /**
     * @param array<mixed>|int|null $requestOptions Deprecated: Do not use "int" anymore, please use an array for requestOptions.
     */
    public function call(string $url, TransferInterface $object, $requestOptions = null): TransferInterface;
}
