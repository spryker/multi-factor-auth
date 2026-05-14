<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\MultiFactorAuth\Api\Storefront\Processor;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\MultiFactorAuthCodeTransfer;
use Generated\Shared\Transfer\MultiFactorAuthCriteriaTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Spryker\Shared\MultiFactorAuth\MultiFactorAuthConstants;

class MultiFactorAuthTypeVerifyStorefrontProcessor extends AbstractMultiFactorAuthStorefrontProcessor
{
    /**
     * @param \Generated\Api\Storefront\MultiFactorAuthTypeVerifyStorefrontResource $data
     *
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    protected function processPost(mixed $data): null
    {
        $type = $this->resolveType($data);
        $code = $this->getRequiredMultiFactorAuthCode();
        $customerTransfer = $this->getCustomer();

        $activeTypesCollectionTransfer = $this->getCustomerActiveTypes($customerTransfer);

        if ($this->isTypeActivated($activeTypesCollectionTransfer, $type)) {
            throw $this->exceptionFactory->createMultiFactorAuthDeactivationFailedException();
        }

        if (!$this->isPendingActivation($customerTransfer, $type)) {
            throw $this->exceptionFactory->createMultiFactorAuthTypeNotFoundException();
        }

        $multiFactorAuthTransfer = (new MultiFactorAuthTransfer())
            ->setType($type)
            ->setCustomer($customerTransfer)
            ->setMultiFactorAuthCode((new MultiFactorAuthCodeTransfer())->setCode($code))
            ->setStatus(MultiFactorAuthConstants::STATUS_ACTIVE);

        $isCodeValid = $this->isMultiFactorAuthCodeValid(
            $code,
            $customerTransfer,
            $multiFactorAuthTransfer,
            additionalStatuses: [MultiFactorAuthConstants::STATUS_PENDING_ACTIVATION],
        );

        if (!$isCodeValid) {
            throw $this->exceptionFactory->createMultiFactorAuthCodeInvalidException();
        }

        $this->multiFactorAuthClient->activateCustomerMultiFactorAuth($multiFactorAuthTransfer);

        return null;
    }

    protected function isPendingActivation(CustomerTransfer $customerTransfer, string $type): bool
    {
        $criteriaTransfer = (new MultiFactorAuthCriteriaTransfer())
            ->setCustomer($customerTransfer)
            ->setStatuses([MultiFactorAuthConstants::STATUS_PENDING_ACTIVATION]);

        $pendingTypesCollection = $this->multiFactorAuthClient->getCustomerMultiFactorAuthTypes($criteriaTransfer);

        foreach ($pendingTypesCollection->getMultiFactorAuthTypes() as $pendingType) {
            if ($pendingType->getType() === $type) {
                return true;
            }
        }

        return false;
    }
}
