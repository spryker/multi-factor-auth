<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace Spryker\Glue\MultiFactorAuth\Processor\TransferBuilder;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\MultiFactorAuthCodeTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;

class MultiFactorAuthTransferBuilder implements MultiFactorAuthTransferBuilderInterface
{
    public function buildMultiFactorAuthCodeTransfer(string $multiFactorAuthCode): MultiFactorAuthCodeTransfer
    {
        return (new MultiFactorAuthCodeTransfer())->setCode($multiFactorAuthCode);
    }

    public function buildMultiFactorAuthTransfer(
        string $multiFactorAuthType,
        CustomerTransfer $customerTransfer,
        ?MultiFactorAuthCodeTransfer $multiFactorAuthCodeTransfer = null,
        ?int $status = null
    ): MultiFactorAuthTransfer {
        $multiFactorAuthTransfer = (new MultiFactorAuthTransfer())
            ->setType($multiFactorAuthType)
            ->setCustomer($customerTransfer);

        if ($multiFactorAuthCodeTransfer !== null) {
            $multiFactorAuthTransfer->setMultiFactorAuthCode($multiFactorAuthCodeTransfer);
        }

        if ($status !== null) {
            $multiFactorAuthTransfer->setStatus($status);
        }

        return $multiFactorAuthTransfer;
    }

    public function buildCustomerTransfer(RestRequestInterface $restRequest): CustomerTransfer
    {
        return (new CustomerTransfer())
            ->setIdCustomer($restRequest->getRestUser()?->getSurrogateIdentifierOrFail());
    }
}
