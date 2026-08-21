<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\MultiFactorAuth\Zed\Customer;

use Generated\Shared\Transfer\MultiFactorAuthCodeCriteriaTransfer;
use Generated\Shared\Transfer\MultiFactorAuthCodeTransfer;
use Generated\Shared\Transfer\MultiFactorAuthCriteriaTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTypesCollectionTransfer;
use Generated\Shared\Transfer\MultiFactorAuthValidationRequestTransfer;
use Generated\Shared\Transfer\MultiFactorAuthValidationResponseTransfer;
use Spryker\Client\MultiFactorAuth\Dependency\Client\MultiFactorAuthToZedRequestClientInterface;

class CustomerMultiFactorAuthStub implements CustomerMultiFactorAuthStubInterface
{
    public function __construct(protected MultiFactorAuthToZedRequestClientInterface $zedStub)
    {
    }

    /**
     * @uses {@link \Spryker\Zed\MultiFactorAuth\Communication\Controller\GatewayController::getCustomerMultiFactorAuthTypesAction()}
     */
    public function getCustomerMultiFactorAuthTypes(MultiFactorAuthCriteriaTransfer $multiFactorAuthCriteriaTransfer): MultiFactorAuthTypesCollectionTransfer
    {
        /** @var \Generated\Shared\Transfer\MultiFactorAuthTypesCollectionTransfer $multiFactorAuthTypesCollectionTransfer */
        $multiFactorAuthTypesCollectionTransfer = $this->zedStub->call('/multi-factor-auth/gateway/get-customer-multi-factor-auth-types', $multiFactorAuthCriteriaTransfer);

        return $multiFactorAuthTypesCollectionTransfer;
    }

    /**
     * @uses {@link \Spryker\Zed\MultiFactorAuth\Communication\Controller\GatewayController::sendCustomerCodeAction()}
     */
    public function sendCustomerCode(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthTransfer
    {
        /** @var \Generated\Shared\Transfer\MultiFactorAuthTransfer $multiFactorAuthTransfer */
        $multiFactorAuthTransfer = $this->zedStub->call('/multi-factor-auth/gateway/send-customer-code', $multiFactorAuthTransfer);

        return $multiFactorAuthTransfer;
    }

    /**
     * @uses {@link \Spryker\Zed\MultiFactorAuth\Communication\Controller\GatewayController::validateCustomerMultiFactorAuthStatusAction()}
     */
    public function validateCustomerMultiFactorAuthStatus(
        MultiFactorAuthValidationRequestTransfer $multiFactorAuthValidationRequestTransfer
    ): MultiFactorAuthValidationResponseTransfer {
        /** @var \Generated\Shared\Transfer\MultiFactorAuthValidationResponseTransfer $multiFactorAuthValidationResponseTransfer */
        $multiFactorAuthValidationResponseTransfer = $this->zedStub->call('/multi-factor-auth/gateway/validate-customer-multi-factor-auth-status', $multiFactorAuthValidationRequestTransfer);

        return $multiFactorAuthValidationResponseTransfer;
    }

    /**
     * @uses {@link \Spryker\Zed\MultiFactorAuth\Communication\Controller\GatewayController::validateCustomerCodeAction()}
     */
    public function validateCustomerCode(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthValidationResponseTransfer
    {
        /** @var \Generated\Shared\Transfer\MultiFactorAuthValidationResponseTransfer $multiFactorAuthValidationResponseTransfer */
        $multiFactorAuthValidationResponseTransfer = $this->zedStub->call('/multi-factor-auth/gateway/validate-customer-code', $multiFactorAuthTransfer);

        return $multiFactorAuthValidationResponseTransfer;
    }

    /**
     * @uses {@link \Spryker\Zed\MultiFactorAuth\Communication\Controller\GatewayController::activateCustomerMultiFactorAuthAction()}
     */
    public function activateCustomerMultiFactorAuth(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthTransfer
    {
        /** @var \Generated\Shared\Transfer\MultiFactorAuthTransfer $multiFactorAuthTransfer */
        $multiFactorAuthTransfer = $this->zedStub->call('/multi-factor-auth/gateway/activate-customer-multi-factor-auth', $multiFactorAuthTransfer);

        return $multiFactorAuthTransfer;
    }

    /**
     * @uses {@link \Spryker\Zed\MultiFactorAuth\Communication\Controller\GatewayController::deactivateCustomerMultiFactorAuthAction()}
     */
    public function deactivateCustomerMultiFactorAuth(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthTransfer
    {
        /** @var \Generated\Shared\Transfer\MultiFactorAuthTransfer $multiFactorAuthTransfer */
        $multiFactorAuthTransfer = $this->zedStub->call('/multi-factor-auth/gateway/deactivate-customer-multi-factor-auth', $multiFactorAuthTransfer);

        return $multiFactorAuthTransfer;
    }

    /**
     * @uses {@link \Spryker\Zed\MultiFactorAuth\Communication\Controller\GatewayController::findCustomerMultiFactorAuthTypeAction()}
     */
    public function findCustomerMultiFactorAuthType(
        MultiFactorAuthCodeCriteriaTransfer $multiFactorAuthCodeCriteriaTransfer
    ): MultiFactorAuthCodeTransfer {
        /** @var \Generated\Shared\Transfer\MultiFactorAuthCodeTransfer $multiFactorAuthCodeTransfer */
        $multiFactorAuthCodeTransfer = $this->zedStub->call('/multi-factor-auth/gateway/find-customer-multi-factor-auth-type', $multiFactorAuthCodeCriteriaTransfer);

        return $multiFactorAuthCodeTransfer;
    }

    /**
     * @uses {@link \Spryker\Zed\MultiFactorAuth\Communication\Controller\GatewayController::invalidateCustomerCodesAction()}
     */
    public function invalidateCustomerCodes(MultiFactorAuthTransfer $multiFactorAuthTransfer): void
    {
        $this->zedStub->call('/multi-factor-auth/gateway/invalidate-customer-codes', $multiFactorAuthTransfer);
    }
}
