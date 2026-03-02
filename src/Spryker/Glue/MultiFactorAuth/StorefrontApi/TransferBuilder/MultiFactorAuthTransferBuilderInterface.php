<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace Spryker\Glue\MultiFactorAuth\StorefrontApi\TransferBuilder;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\MultiFactorAuthCodeTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTransfer;

interface MultiFactorAuthTransferBuilderInterface
{
    public function buildMultiFactorAuthCodeTransfer(string $multiFactorAuthCode): MultiFactorAuthCodeTransfer;

    public function buildMultiFactorAuthTransfer(
        string $multiFactorAuthType,
        CustomerTransfer $customerTransfer,
        ?MultiFactorAuthCodeTransfer $multiFactorAuthCodeTransfer = null,
        ?int $status = null
    ): MultiFactorAuthTransfer;

    public function buildCustomerTransfer(GlueRequestTransfer $glueRequestTransfer): CustomerTransfer;
}
