<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\MultiFactorAuth\Dependency\Client;

use Generated\Shared\Transfer\MultiFactorAuthCodeCriteriaTransfer;
use Generated\Shared\Transfer\MultiFactorAuthCodeTransfer;
use Generated\Shared\Transfer\MultiFactorAuthCriteriaTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTypesCollectionTransfer;
use Generated\Shared\Transfer\MultiFactorAuthValidationRequestTransfer;
use Generated\Shared\Transfer\MultiFactorAuthValidationResponseTransfer;

class MultiFactorAuthToMultiFactorAuthClientBridge implements MultiFactorAuthToMultiFactorAuthClientInterface
{
    /**
     * @var \Spryker\Client\MultiFactorAuth\MultiFactorAuthClientInterface
     */
    protected $multiFactorAuthClient;

    /**
     * @param \Spryker\Client\MultiFactorAuth\MultiFactorAuthClientInterface $multiFactorAuthClient
     */
    public function __construct($multiFactorAuthClient)
    {
        $this->multiFactorAuthClient = $multiFactorAuthClient;
    }

    public function getCustomerMultiFactorAuthTypes(MultiFactorAuthCriteriaTransfer $multiFactorAuthCriteriaTransfer): MultiFactorAuthTypesCollectionTransfer
    {
        return $this->multiFactorAuthClient->getCustomerMultiFactorAuthTypes($multiFactorAuthCriteriaTransfer);
    }

    public function validateCustomerCode(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthValidationResponseTransfer
    {
        return $this->multiFactorAuthClient->validateCustomerCode($multiFactorAuthTransfer);
    }

    public function validateCustomerMultiFactorAuthStatus(
        MultiFactorAuthValidationRequestTransfer $multiFactorAuthValidationRequestTransfer
    ): MultiFactorAuthValidationResponseTransfer {
        return $this->multiFactorAuthClient->validateCustomerMultiFactorAuthStatus($multiFactorAuthValidationRequestTransfer);
    }

    public function sendCustomerCode(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthTransfer
    {
        return $this->multiFactorAuthClient->sendCustomerCode($multiFactorAuthTransfer);
    }

    public function deactivateCustomerMultiFactorAuth(MultiFactorAuthTransfer $multiFactorAuthTransfer): void
    {
        $this->multiFactorAuthClient->deactivateCustomerMultiFactorAuth($multiFactorAuthTransfer);
    }

    public function activateCustomerMultiFactorAuth(MultiFactorAuthTransfer $multiFactorAuthTransfer): void
    {
        $this->multiFactorAuthClient->activateCustomerMultiFactorAuth($multiFactorAuthTransfer);
    }

    public function findCustomerMultiFactorAuthType(
        MultiFactorAuthCodeCriteriaTransfer $multiFactorAuthCodeCriteriaTransfer
    ): MultiFactorAuthCodeTransfer {
        return $this->multiFactorAuthClient->findCustomerMultiFactorAuthType($multiFactorAuthCodeCriteriaTransfer);
    }
}
