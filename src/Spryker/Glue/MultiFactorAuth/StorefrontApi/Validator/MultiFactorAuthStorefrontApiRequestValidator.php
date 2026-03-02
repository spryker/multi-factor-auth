<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace Spryker\Glue\MultiFactorAuth\StorefrontApi\Validator;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\GlueErrorTransfer;
use Generated\Shared\Transfer\GlueRequestTransfer;
use Generated\Shared\Transfer\GlueRequestValidationTransfer;
use Generated\Shared\Transfer\MultiFactorAuthCodeCriteriaTransfer;
use Generated\Shared\Transfer\MultiFactorAuthCodeTransfer;
use Generated\Shared\Transfer\MultiFactorAuthCriteriaTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTypesCollectionTransfer;
use Generated\Shared\Transfer\MultiFactorAuthValidationRequestTransfer;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceInterface;
use Spryker\Glue\MultiFactorAuth\Dependency\Client\MultiFactorAuthToCustomerClientInterface;
use Spryker\Glue\MultiFactorAuth\Dependency\Client\MultiFactorAuthToMultiFactorAuthClientInterface;
use Spryker\Glue\MultiFactorAuth\MultiFactorAuthConfig;
use Spryker\Glue\MultiFactorAuth\StorefrontApi\TransferBuilder\MultiFactorAuthTransferBuilderInterface;
use Spryker\Shared\MultiFactorAuth\MultiFactorAuthConstants;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MultiFactorAuthStorefrontApiRequestValidator implements MultiFactorAuthStorefrontApiRequestValidatorInterface
{
    public function __construct(
        protected MultiFactorAuthToMultiFactorAuthClientInterface $multiFactorAuthClient,
        protected MultiFactorAuthToCustomerClientInterface $customerClient,
        protected MultiFactorAuthTransferBuilderInterface $multiFactorAuthTransferBuilder,
        protected MultiFactorAuthConfig $multiFactorAuthConfig
    ) {
    }

    public function validate(GlueRequestTransfer $glueRequestTransfer, ResourceInterface $resource): GlueRequestValidationTransfer
    {
        $glueRequestValidationTransfer = (new GlueRequestValidationTransfer())->setIsValid(true);
        if ($this->shouldSkipValidation($glueRequestTransfer)) {
            return $glueRequestValidationTransfer;
        }
        $customerTransfer = $this->customerClient->getCustomerById((int)$glueRequestTransfer->getRequestCustomer()?->getSurrogateIdentifierOrFail());
        $multiFactorAuthCriteriaTransfer = (new MultiFactorAuthCriteriaTransfer())->setCustomer($customerTransfer);

        $multiFactorAuthTypesCollectionTransfer = $this->multiFactorAuthClient->getCustomerMultiFactorAuthTypes($multiFactorAuthCriteriaTransfer);

        if ($multiFactorAuthTypesCollectionTransfer->getMultiFactorAuthTypes()->count() === 0) {
            return $glueRequestValidationTransfer;
        }

        if (!$this->hasMultiFactorAuthCodeHeader($glueRequestTransfer)) {
            return $this->createMissingMultiFactorAuthCodeError($glueRequestValidationTransfer);
        }

        $multiFactorAuthCode = $glueRequestTransfer->getMeta()[strtolower(MultiFactorAuthConfig::HEADER_MULTI_FACTOR_AUTH_CODE)][0];

        $multiFactorAuthCodeCriteriaTransfer = (new MultiFactorAuthCodeCriteriaTransfer())
            ->setCode($multiFactorAuthCode)->setCustomer($customerTransfer);

        $multiFactorAuthCodeWithTypeTransfer = $this->multiFactorAuthClient
            ->findCustomerMultiFactorAuthType($multiFactorAuthCodeCriteriaTransfer);

        if (
            $multiFactorAuthCodeWithTypeTransfer->getType() === null ||
            $this->isActivatedMultiFactorAuthType($multiFactorAuthTypesCollectionTransfer, $multiFactorAuthCodeWithTypeTransfer->getTypeOrFail()) === false
        ) {
            return $this->createInvalidMultiFactorAuthCodeError($glueRequestValidationTransfer);
        }

        if ($this->isMultiFactorAuthCodeValid($multiFactorAuthCodeWithTypeTransfer, $customerTransfer) === false) {
            return $this->createInvalidMultiFactorAuthCodeError($glueRequestValidationTransfer);
        }

        return $glueRequestValidationTransfer;
    }

    protected function isActivatedMultiFactorAuthType(
        MultiFactorAuthTypesCollectionTransfer $multiFactorAuthTypesCollectionTransfer,
        string $multiFactorAuthType
    ): bool {
        foreach ($multiFactorAuthTypesCollectionTransfer->getMultiFactorAuthTypes() as $activatedMultiFactorAuthType) {
            if ($activatedMultiFactorAuthType->getTypeOrFail() === $multiFactorAuthType) {
                return true;
            }
        }

        return false;
    }

    protected function shouldSkipValidation(GlueRequestTransfer $glueRequestTransfer): bool
    {
        $resourceType = $glueRequestTransfer->getResourceOrFail()->getType();

        return $resourceType === null
            || !$glueRequestTransfer->getRequestCustomer()
            || $glueRequestTransfer->getMethod() === Request::METHOD_OPTIONS
            || $glueRequestTransfer->getMethod() === Request::METHOD_GET
            || !$this->isRestApiMultiFactorAuthProtectedResource($resourceType);
    }

    protected function isRestApiMultiFactorAuthProtectedResource(string $resourceType): bool
    {
        return in_array($resourceType, $this->multiFactorAuthConfig->getMultiFactorAuthProtectedStorefrontResources(), true);
    }

    protected function hasMultiFactorAuthCodeHeader(GlueRequestTransfer $glueRequestTransfer): bool
    {
        return array_key_exists(strtolower(MultiFactorAuthConfig::HEADER_MULTI_FACTOR_AUTH_CODE), $glueRequestTransfer->getMeta()) &&
            $glueRequestTransfer->getMeta()[strtolower(MultiFactorAuthConfig::HEADER_MULTI_FACTOR_AUTH_CODE)][0] !== null &&
            $glueRequestTransfer->getMeta()[strtolower(MultiFactorAuthConfig::HEADER_MULTI_FACTOR_AUTH_CODE)][0] !== '';
    }

    protected function createMissingMultiFactorAuthCodeError(GlueRequestValidationTransfer $glueRequestValidationTransfer): GlueRequestValidationTransfer
    {
        $glueErrorTransfer = new GlueErrorTransfer();
        $glueErrorTransfer
            ->setStatus(Response::HTTP_FORBIDDEN)
            ->setCode(MultiFactorAuthConfig::ERROR_CODE_MULTI_FACTOR_AUTH_CODE_MISSING)
            ->setMessage(MultiFactorAuthConfig::ERROR_MESSAGE_MULTI_FACTOR_AUTH_CODE_MISSING);

        return $glueRequestValidationTransfer
            ->setIsValid(false)
            ->addError($glueErrorTransfer)
            ->setStatus(Response::HTTP_FORBIDDEN);
    }

    protected function createInvalidMultiFactorAuthCodeError(GlueRequestValidationTransfer $glueRequestValidationTransfer): GlueRequestValidationTransfer
    {
        $glueErrorTransfer = new GlueErrorTransfer();
        $glueErrorTransfer
            ->setStatus(Response::HTTP_FORBIDDEN)
            ->setCode(MultiFactorAuthConfig::ERROR_CODE_MULTI_FACTOR_AUTH_CODE_INVALID)
            ->setMessage(MultiFactorAuthConfig::ERROR_MESSAGE_MULTI_FACTOR_AUTH_CODE_INVALID);

        return $glueRequestValidationTransfer
            ->setIsValid(false)
            ->addError($glueErrorTransfer)
            ->setStatus(Response::HTTP_FORBIDDEN);
    }

    protected function buildMultiFactorAuthTransfer(
        CustomerTransfer $customerTransfer,
        MultiFactorAuthCodeTransfer $multiFactorAuthCodeTransfer
    ): MultiFactorAuthTransfer {
        return (new MultiFactorAuthTransfer())
            ->setType($multiFactorAuthCodeTransfer->getTypeOrFail())
            ->setCustomer($customerTransfer)
            ->setMultiFactorAuthCode($multiFactorAuthCodeTransfer);
    }

    protected function isMultiFactorAuthCodeValid(
        MultiFactorAuthCodeTransfer $multiFactorAuthCodeWithTypeTransfer,
        CustomerTransfer $customerTransfer
    ): bool {
        $multiFactorAuthTransfer = $this->buildMultiFactorAuthTransfer(
            $customerTransfer,
            $multiFactorAuthCodeWithTypeTransfer,
        );
        if ($multiFactorAuthCodeWithTypeTransfer->getIdCode() === null) {
            return false;
        }

        if ($multiFactorAuthCodeWithTypeTransfer->getStatusOrFail() === MultiFactorAuthConstants::STATUS_ACTIVE) {
            $multiFactorAuthValidationRequestTransfer = (new MultiFactorAuthValidationRequestTransfer())->setCustomer($customerTransfer);
            $multiFactorAuthValidationResponseTransfer = $this->multiFactorAuthClient->validateCustomerMultiFactorAuthStatus(
                $multiFactorAuthValidationRequestTransfer,
            );

            return $multiFactorAuthValidationResponseTransfer->getIsRequired() === false;
        }

        return $this->isMultiFactorAuthCodeVerified($multiFactorAuthTransfer);
    }

    protected function isMultiFactorAuthCodeVerified(MultiFactorAuthTransfer $multiFactorAuthTransfer): bool
    {
        $validationResponse = $this->multiFactorAuthClient->validateCustomerCode($multiFactorAuthTransfer);

        return $validationResponse->getStatus() === MultiFactorAuthConstants::CODE_VERIFIED;
    }
}
