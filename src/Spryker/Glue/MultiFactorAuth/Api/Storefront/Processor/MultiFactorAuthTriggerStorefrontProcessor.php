<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\MultiFactorAuth\Api\Storefront\Processor;

use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Spryker\Shared\MultiFactorAuth\MultiFactorAuthConstants;
use Throwable;

class MultiFactorAuthTriggerStorefrontProcessor extends AbstractMultiFactorAuthStorefrontProcessor
{
    /**
     * @param \Generated\Api\Storefront\MultiFactorAuthTriggerStorefrontResource $data
     *
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    protected function processPost(mixed $data): null
    {
        $type = $this->resolveType($data);
        $customerTransfer = $this->getCustomer();
        $activeTypesCollectionTransfer = $this->getCustomerActiveTypes($customerTransfer);

        if (!$this->isTypeActivated($activeTypesCollectionTransfer, $type)) {
            throw $this->exceptionFactory->createMultiFactorAuthTypeNotFoundException();
        }

        $multiFactorAuthTransfer = (new MultiFactorAuthTransfer())
            ->setType($type)
            ->setCustomer($customerTransfer)
            ->setStatus(MultiFactorAuthConstants::STATUS_ACTIVE);

        try {
            $this->multiFactorAuthClient->sendCustomerCode($multiFactorAuthTransfer);
        } catch (Throwable) {
            throw $this->exceptionFactory->createSendingCodeErrorException();
        }

        return null;
    }
}
