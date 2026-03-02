<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace Spryker\Glue\MultiFactorAuth\Processor\Activate;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\MultiFactorAuthCodeCriteriaTransfer;
use Generated\Shared\Transfer\MultiFactorAuthCriteriaTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTypesCollectionTransfer;
use Generated\Shared\Transfer\RestMultiFactorAuthAttributesTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;
use Spryker\Glue\MultiFactorAuth\Dependency\Client\MultiFactorAuthToCustomerClientInterface;
use Spryker\Glue\MultiFactorAuth\Dependency\Client\MultiFactorAuthToMultiFactorAuthClientInterface;
use Spryker\Glue\MultiFactorAuth\MultiFactorAuthConfig;
use Spryker\Glue\MultiFactorAuth\Processor\ResponseBuilder\MultiFactorAuthResponseBuilderInterface;
use Spryker\Glue\MultiFactorAuth\Processor\TransferBuilder\MultiFactorAuthTransferBuilderInterface;
use Spryker\Glue\MultiFactorAuth\Processor\Validator\MultiFactorAuthValidatorInterface;
use Spryker\Shared\MultiFactorAuth\MultiFactorAuthConstants;
use Throwable;

class MultiFactorAuthActivateProcessor implements MultiFactorAuthActivateProcessorInterface
{
    public function __construct(
        protected MultiFactorAuthToMultiFactorAuthClientInterface $multiFactorAuthClient,
        protected MultiFactorAuthToCustomerClientInterface $customerClient,
        protected MultiFactorAuthResponseBuilderInterface $multiFactorAuthResponseBuilder,
        protected MultiFactorAuthTransferBuilderInterface $multiFactorAuthTransferBuilder,
        protected MultiFactorAuthValidatorInterface $multiFactorAuthValidator
    ) {
    }

    public function activateMultiFactorAuth(
        RestRequestInterface $restRequest,
        RestMultiFactorAuthAttributesTransfer $restMultiFactorAuthAttributesTransfer
    ): RestResponseInterface {
        $errorResponse = $this->multiFactorAuthValidator->validateMultiFactorAuthType($restRequest, $restMultiFactorAuthAttributesTransfer);
        if ($errorResponse !== null) {
            return $errorResponse;
        }

        $multiFactorAuthType = $restMultiFactorAuthAttributesTransfer->getTypeOrFail();
        $customerTransfer = $this->customerClient->getCustomerById((int)$restRequest->getRestUser()?->getSurrogateIdentifierOrFail());
        $multiFactorAuthTypesCollectionTransfer = $this->multiFactorAuthClient
            ->getCustomerMultiFactorAuthTypes((new MultiFactorAuthCriteriaTransfer())->setCustomer($customerTransfer));

        if ($this->multiFactorAuthValidator->isActivatedMultiFactorAuthType($multiFactorAuthTypesCollectionTransfer, $multiFactorAuthType) === true) {
            return $this->multiFactorAuthResponseBuilder->createAlreadyActivatedMultiFactorAuthError();
        }

        if ($this->assertTheCodeIsMissing($restRequest, $multiFactorAuthTypesCollectionTransfer)) {
            return $this->multiFactorAuthResponseBuilder->createMissingMultiFactorAuthCodeError();
        }

        if ($this->assertTheProvidedCodeIsNotApplicable($restRequest, $multiFactorAuthTypesCollectionTransfer, $customerTransfer)) {
            return $this->multiFactorAuthResponseBuilder->createInvalidMultiFactorAuthCodeError();
        }

        $multiFactorAuthTransfer = $this->multiFactorAuthTransferBuilder->buildMultiFactorAuthTransfer(
            $multiFactorAuthType,
            $customerTransfer,
            null,
            MultiFactorAuthConstants::STATUS_PENDING_ACTIVATION,
        );

        $this->multiFactorAuthClient->activateCustomerMultiFactorAuth($multiFactorAuthTransfer);

        return $this->safelySendActivationCode($multiFactorAuthTransfer);
    }

    protected function assertTheCodeIsMissing(
        RestRequestInterface $restRequest,
        MultiFactorAuthTypesCollectionTransfer $multiFactorAuthTypesCollectionTransfer
    ): bool {
        return $this->hasExistingMultiFactorAuth($multiFactorAuthTypesCollectionTransfer) && $restRequest->getHttpRequest()->headers->get(MultiFactorAuthConfig::HEADER_MULTI_FACTOR_AUTH_CODE) === null;
    }

    protected function assertTheProvidedCodeIsNotApplicable(
        RestRequestInterface $restRequest,
        MultiFactorAuthTypesCollectionTransfer $multiFactorAuthTypesCollectionTransfer,
        CustomerTransfer $customerTransfer
    ): bool {
        $hasExistingMultiFactorAuth = $this->hasExistingMultiFactorAuth($multiFactorAuthTypesCollectionTransfer);

        if (!$hasExistingMultiFactorAuth && $restRequest->getHttpRequest()->headers->get(MultiFactorAuthConfig::HEADER_MULTI_FACTOR_AUTH_CODE) === null) {
            return false;
        }

        $isCodeValid = $this->isMultiFactorAuthActivationCodeValid($restRequest, $customerTransfer);

        if (!$hasExistingMultiFactorAuth && $isCodeValid) {
            return true;
        }

        return $hasExistingMultiFactorAuth && !$isCodeValid;
    }

    protected function hasExistingMultiFactorAuth(MultiFactorAuthTypesCollectionTransfer $multiFactorAuthTypesCollectionTransfer): bool
    {
        if (count($multiFactorAuthTypesCollectionTransfer->getMultiFactorAuthTypes()) === 0) {
            return false;
        }

        foreach ($multiFactorAuthTypesCollectionTransfer->getMultiFactorAuthTypes() as $multiFactorAuthType) {
            if ($multiFactorAuthType->getStatus() === MultiFactorAuthConstants::STATUS_ACTIVE) {
                return true;
            }
        }

        return false;
    }

    protected function isMultiFactorAuthActivationCodeValid(
        RestRequestInterface $restRequest,
        CustomerTransfer $customerTransfer
    ): bool {
        $multiFactorAuthCode = (string)$restRequest->getHttpRequest()->headers->get(MultiFactorAuthConfig::HEADER_MULTI_FACTOR_AUTH_CODE);
        $multiFactorAuthCodeTransfer = $this->multiFactorAuthTransferBuilder->buildMultiFactorAuthCodeTransfer($multiFactorAuthCode);

        $multiFactorAuthCodeCriteriaTransfer = (new MultiFactorAuthCodeCriteriaTransfer())
            ->setCode($multiFactorAuthCode)->setCustomer($customerTransfer);

        $multiFactorAuthCodeWithTypeTransfer = $this->multiFactorAuthClient
            ->findCustomerMultiFactorAuthType($multiFactorAuthCodeCriteriaTransfer);

        if ($multiFactorAuthCodeWithTypeTransfer->getType() === null) {
            return false;
        }

        $multiFactorAuthTransfer = $this->multiFactorAuthTransferBuilder->buildMultiFactorAuthTransfer(
            $multiFactorAuthCodeWithTypeTransfer->getTypeOrFail(),
            $customerTransfer,
            $multiFactorAuthCodeTransfer,
        );

        return $this->multiFactorAuthValidator->isMultiFactorAuthCodeValid($multiFactorAuthCode, $customerTransfer, $multiFactorAuthTransfer);
    }

    protected function safelySendActivationCode(MultiFactorAuthTransfer $multiFactorAuthTransfer): RestResponseInterface
    {
        try {
            $this->multiFactorAuthClient->sendCustomerCode(
                $multiFactorAuthTransfer->setStatus(MultiFactorAuthConstants::STATUS_ACTIVE),
            );
        } catch (Throwable $e) {
            return $this->multiFactorAuthResponseBuilder->createSendingCodeError();
        }

        return $this->multiFactorAuthResponseBuilder->createSuccessResponse();
    }
}
