<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\MultiFactorAuth\Api\Storefront\Processor;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\MultiFactorAuthCodeCriteriaTransfer;
use Generated\Shared\Transfer\MultiFactorAuthCriteriaTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTypesCollectionTransfer;
use Generated\Shared\Transfer\MultiFactorAuthValidationRequestTransfer;
use Spryker\ApiPlatform\State\Processor\AbstractStorefrontProcessor;
use Spryker\Client\MultiFactorAuth\MultiFactorAuthClientInterface;
use Spryker\Glue\MultiFactorAuth\Api\Storefront\Exception\MultiFactorAuthExceptionFactory;
use Spryker\Glue\MultiFactorAuth\MultiFactorAuthConfig;
use Spryker\Shared\MultiFactorAuth\MultiFactorAuthConstants;

abstract class AbstractMultiFactorAuthStorefrontProcessor extends AbstractStorefrontProcessor
{
    public function __construct(
        protected MultiFactorAuthClientInterface $multiFactorAuthClient,
        protected MultiFactorAuthExceptionFactory $exceptionFactory = new MultiFactorAuthExceptionFactory(),
    ) {
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    protected function resolveType(mixed $data): string
    {
        $type = $data->type ?? null;

        if (!is_string($type) || $type === '') {
            throw $this->exceptionFactory->createMultiFactorAuthTypeMissingException();
        }

        return $type;
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    protected function getRequiredMultiFactorAuthCode(): string
    {
        $code = $this->getRequest()->headers->get(MultiFactorAuthConfig::HEADER_MULTI_FACTOR_AUTH_CODE);

        if ($code === null || $code === '') {
            throw $this->exceptionFactory->createMultiFactorAuthCodeMissingException();
        }

        return $code;
    }

    protected function getCustomerActiveTypes(CustomerTransfer $customerTransfer): MultiFactorAuthTypesCollectionTransfer
    {
        $criteriaTransfer = (new MultiFactorAuthCriteriaTransfer())->setCustomer($customerTransfer);

        return $this->multiFactorAuthClient->getCustomerMultiFactorAuthTypes($criteriaTransfer);
    }

    protected function isTypeActivated(MultiFactorAuthTypesCollectionTransfer $collection, string $type): bool
    {
        foreach ($collection->getMultiFactorAuthTypes() as $activatedType) {
            if ($activatedType->getTypeOrFail() === $type) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<int> $additionalStatuses
     */
    protected function isMultiFactorAuthCodeValid(
        string $code,
        CustomerTransfer $customerTransfer,
        MultiFactorAuthTransfer $multiFactorAuthTransfer,
        ?string $expectedType = null,
        array $additionalStatuses = [],
    ): bool {
        $criteriaTransfer = (new MultiFactorAuthCodeCriteriaTransfer())
            ->setCode($code)
            ->setCustomer($customerTransfer);

        if ($expectedType !== null) {
            $criteriaTransfer->setType($expectedType);
        }

        $codeWithTypeTransfer = $this->multiFactorAuthClient->findCustomerMultiFactorAuthType($criteriaTransfer);

        if ($codeWithTypeTransfer->getIdCode() === null) {
            return false;
        }

        if ($expectedType !== null && $codeWithTypeTransfer->getTypeOrFail() !== $expectedType) {
            return false;
        }

        if ($codeWithTypeTransfer->getStatusOrFail() === MultiFactorAuthConstants::STATUS_ACTIVE) {
            $validationRequestTransfer = (new MultiFactorAuthValidationRequestTransfer())
                ->setCustomer($customerTransfer)
                ->setAdditionalStatuses($additionalStatuses);

            $validationResponse = $this->multiFactorAuthClient->validateCustomerMultiFactorAuthStatus($validationRequestTransfer);

            return $validationResponse->getIsRequired() === false;
        }

        $validationResponse = $this->multiFactorAuthClient->validateCustomerCode($multiFactorAuthTransfer);

        return $validationResponse->getStatus() === MultiFactorAuthConstants::CODE_VERIFIED;
    }
}
