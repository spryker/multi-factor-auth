<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\MultiFactorAuth\Api\Storefront\Processor;

use Generated\Shared\Transfer\MultiFactorAuthCodeTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTransfer;

class MultiFactorAuthTypeDeactivateStorefrontProcessor extends AbstractMultiFactorAuthStorefrontProcessor
{
    /**
     * @param \Generated\Api\Storefront\MultiFactorAuthTypeDeactivateStorefrontResource $data
     *
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    protected function processPost(mixed $data): null
    {
        $type = $this->resolveType($data);
        $code = $this->getRequiredMultiFactorAuthCode();
        $customerTransfer = $this->getCustomer();

        $activeTypesCollectionTransfer = $this->getCustomerActiveTypes($customerTransfer);

        if (!$this->isTypeActivated($activeTypesCollectionTransfer, $type)) {
            throw $this->exceptionFactory->createMultiFactorAuthTypeNotFoundException();
        }

        $multiFactorAuthTransfer = (new MultiFactorAuthTransfer())
            ->setType($type)
            ->setCustomer($customerTransfer)
            ->setMultiFactorAuthCode((new MultiFactorAuthCodeTransfer())->setCode($code));

        if (!$this->isMultiFactorAuthCodeValid($code, $customerTransfer, $multiFactorAuthTransfer, expectedType: $type)) {
            throw $this->exceptionFactory->createMultiFactorAuthCodeInvalidException();
        }

        $this->multiFactorAuthClient->deactivateCustomerMultiFactorAuth($multiFactorAuthTransfer);

        return null;
    }
}
