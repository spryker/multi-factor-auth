<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\MultiFactorAuth;

use Generated\Shared\Transfer\MultiFactorAuthCodeCriteriaTransfer;
use Generated\Shared\Transfer\MultiFactorAuthCodeTransfer;
use Generated\Shared\Transfer\MultiFactorAuthCriteriaTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTypesCollectionTransfer;
use Generated\Shared\Transfer\MultiFactorAuthValidationRequestTransfer;
use Generated\Shared\Transfer\MultiFactorAuthValidationResponseTransfer;

interface MultiFactorAuthClientInterface
{
    /**
     * Specification:
     * - Makes Zed request.
     * - Retrieves all multi-factor authentication types mapped with the corresponding data by the provided Customer.
     *
     * @api
     */
    public function getCustomerMultiFactorAuthTypes(MultiFactorAuthCriteriaTransfer $multiFactorAuthCriteriaTransfer): MultiFactorAuthTypesCollectionTransfer;

    /**
     * Specification:
     * - Makes Zed request.
     * - Generates a new multi-factor authentication code for the provided Customer.
     * - Saves it in the `spy_customer_multi_factor_auth_codes` table.
     * - Sends the generated code to the Customer based on the provided multi-factor authentication method.
     *
     * @api
     */
    public function sendCustomerCode(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthTransfer;

    /**
     * Specification:
     * - Makes Zed request.
     * - Validates the provided multi-factor authentication code by the provided Customer.
     * - The validation will be handled by a selected multi-factor authentication strategy.
     *
     * @api
     */
    public function validateCustomerCode(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthValidationResponseTransfer;

    /**
     * Specification:
     * - Makes Zed request.
     * - Validates the provided multi-factor authentication status by the provided Customer.
     * - The validation will be handled by a selected multi-factor authentication strategy.
     *
     * @api
     */
    public function validateCustomerMultiFactorAuthStatus(
        MultiFactorAuthValidationRequestTransfer $multiFactorAuthValidationRequestTransfer
    ): MultiFactorAuthValidationResponseTransfer;

    /**
     * Specification:
     * - Makes Zed request.
     * - Activates the provided multi-factor authentication method for the provided Customer.
     *
     * @api
     */
    public function activateCustomerMultiFactorAuth(MultiFactorAuthTransfer $multiFactorAuthTransfer): void;

    /**
     * Specification:
     * - Makes Zed request.
     * - Deactivates the provided multi-factor authentication method for the provided Customer.
     *
     * @api
     */
    public function deactivateCustomerMultiFactorAuth(MultiFactorAuthTransfer $multiFactorAuthTransfer): void;

    /**
     * Specification:
     * - Makes Zed request.
     * - Retrieves all multi-factor authentication types mapped with the corresponding data by the provided Agent.
     *
     * @api
     */
    public function getAgentMultiFactorAuthTypes(MultiFactorAuthCriteriaTransfer $multiFactorAuthCriteriaTransfer): MultiFactorAuthTypesCollectionTransfer;

    /**
     * Specification:
     * - Makes Zed request.
     * - Generates a new multi-factor authentication code for the provided Agent.
     * - Saves it in the `spy_user_multi_factor_auth_codes` table.
     * - Sends the generated code to the user based on the provided multi-factor authentication method.
     *
     * @api
     */
    public function sendAgentCode(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthTransfer;

    /**
     * Specification:
     * - Makes Zed request.
     * - Validates the provided multi-factor authentication code by the provided Agent.
     * - The validation will be handled by a selected multi-factor authentication strategy.
     *
     * @api
     */
    public function validateAgentCode(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthValidationResponseTransfer;

    /**
     * Specification:
     * - Makes Zed request.
     * - Validates the provided multi-factor authentication status by the provided Agent.
     * - The validation will be handled by a selected multi-factor authentication strategy.
     *
     * @api
     */
    public function validateAgentMultiFactorAuthStatus(
        MultiFactorAuthValidationRequestTransfer $multiFactorAuthValidationRequestTransfer
    ): MultiFactorAuthValidationResponseTransfer;

    /**
     * Specification:
     * - Makes Zed request.
     * - Activates the provided multi-factor authentication method for the provided Agent.
     *
     * @api
     */
    public function activateAgentMultiFactorAuth(MultiFactorAuthTransfer $multiFactorAuthTransfer): void;

    /**
     * Specification:
     * - Makes Zed request.
     * - Deactivates the provided multi-factor authentication method for the provided Agent.
     *
     * @api
     */
    public function deactivateAgentMultiFactorAuth(MultiFactorAuthTransfer $multiFactorAuthTransfer): void;

    /**
     * Specification:
     * - Makes Zed request.
     * - Finds authentication code by criteria.
     * - Searches from the end of the codes list.
     * - Returns type associated with the code.
     *
     * @api
     */
    public function findCustomerMultiFactorAuthType(
        MultiFactorAuthCodeCriteriaTransfer $multiFactorAuthCodeCriteriaTransfer
    ): MultiFactorAuthCodeTransfer;

    /**
     * Specification:
     * - Makes Zed request.
     * - Invalidates all active multi-factor authentication codes for a customer.
     * - Sets all active codes status to invalidated.
     *
     * @api
     */
    public function invalidateCustomerCodes(MultiFactorAuthTransfer $multiFactorAuthTransfer): void;

    /**
     * Specification:
     * - Makes Zed request.
     * - Invalidates all active multi-factor authentication codes for a user.
     * - Sets all active codes status to invalidated.
     *
     * @api
     */
    public function invalidateAgentCodes(MultiFactorAuthTransfer $multiFactorAuthTransfer): void;
}
