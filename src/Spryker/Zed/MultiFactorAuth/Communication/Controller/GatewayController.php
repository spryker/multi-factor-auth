<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MultiFactorAuth\Communication\Controller;

use Generated\Shared\Transfer\MultiFactorAuthCodeCriteriaTransfer;
use Generated\Shared\Transfer\MultiFactorAuthCodeTransfer;
use Generated\Shared\Transfer\MultiFactorAuthCriteriaTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTypesCollectionTransfer;
use Generated\Shared\Transfer\MultiFactorAuthValidationRequestTransfer;
use Generated\Shared\Transfer\MultiFactorAuthValidationResponseTransfer;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;

/**
 * @method \Spryker\Zed\MultiFactorAuth\Persistence\MultiFactorAuthRepositoryInterface getRepository()
 * @method \Spryker\Zed\MultiFactorAuth\Business\MultiFactorAuthFacadeInterface getFacade()
 * @method \Spryker\Zed\MultiFactorAuth\Communication\MultiFactorAuthCommunicationFactory getFactory()
 * @method \Spryker\Zed\MultiFactorAuth\Persistence\MultiFactorAuthEntityManagerInterface getEntityManager()
 */
class GatewayController extends AbstractGatewayController
{
    public function validateCustomerMultiFactorAuthStatusAction(
        MultiFactorAuthValidationRequestTransfer $multiFactorAuthValidationRequestTransfer
    ): MultiFactorAuthValidationResponseTransfer {
        return $this->getFacade()->validateCustomerMultiFactorAuthStatus($multiFactorAuthValidationRequestTransfer);
    }

    public function getCustomerMultiFactorAuthTypesAction(
        MultiFactorAuthCriteriaTransfer $multiFactorAuthCriteriaTransfer
    ): MultiFactorAuthTypesCollectionTransfer {
        return $this->getRepository()->getCustomerMultiFactorAuthTypes($multiFactorAuthCriteriaTransfer);
    }

    public function validateCustomerCodeAction(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthValidationResponseTransfer
    {
        return $this->getFacade()->validateCustomerCode($multiFactorAuthTransfer);
    }

    public function sendCustomerCodeAction(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthTransfer
    {
        return $this->getFacade()->sendCustomerCode($multiFactorAuthTransfer);
    }

    public function activateCustomerMultiFactorAuthAction(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthTransfer
    {
        $this->getFacade()->activateCustomerMultiFactorAuth($multiFactorAuthTransfer);

        return $multiFactorAuthTransfer;
    }

    public function deactivateCustomerMultiFactorAuthAction(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthTransfer
    {
        $this->getFacade()->deactivateCustomerMultiFactorAuth($multiFactorAuthTransfer);

        return $multiFactorAuthTransfer;
    }

    public function findCustomerMultiFactorAuthTypeAction(
        MultiFactorAuthCodeCriteriaTransfer $multiFactorAuthCodeCriteriaTransfer
    ): MultiFactorAuthCodeTransfer {
        return $this->getFacade()->findCustomerMultiFactorAuthType($multiFactorAuthCodeCriteriaTransfer);
    }

    public function getUserMultiFactorAuthTypesAction(MultiFactorAuthCriteriaTransfer $multiFactorAuthCriteriaTransfer): MultiFactorAuthTypesCollectionTransfer
    {
        return $this->getRepository()->getUserMultiFactorAuthTypes($multiFactorAuthCriteriaTransfer);
    }

    public function validateUserMultiFactorAuthStatusAction(
        MultiFactorAuthValidationRequestTransfer $multiFactorAuthValidationRequestTransfer
    ): MultiFactorAuthValidationResponseTransfer {
        return $this->getFacade()->validateUserMultiFactorAuthStatus($multiFactorAuthValidationRequestTransfer);
    }

    public function sendUserCodeAction(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthTransfer
    {
        return $this->getFacade()->sendUserCode($multiFactorAuthTransfer);
    }

    public function validateUserCodeAction(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthValidationResponseTransfer
    {
        return $this->getFacade()->validateUserCode($multiFactorAuthTransfer);
    }

    public function activateUserMultiFactorAuthAction(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthTransfer
    {
        $this->getFacade()->activateUserMultiFactorAuth($multiFactorAuthTransfer);

        return $multiFactorAuthTransfer;
    }

    public function deactivateUserMultiFactorAuthAction(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthTransfer
    {
        $this->getFacade()->deactivateUserMultiFactorAuth($multiFactorAuthTransfer);

        return $multiFactorAuthTransfer;
    }

    public function invalidateCustomerCodesAction(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthTransfer
    {
        $this->getFacade()->invalidateCustomerCodes($multiFactorAuthTransfer);

        return $multiFactorAuthTransfer;
    }

    public function invalidateUserCodesAction(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthTransfer
    {
        $this->getFacade()->invalidateUserCodes($multiFactorAuthTransfer);

        return $multiFactorAuthTransfer;
    }
}
