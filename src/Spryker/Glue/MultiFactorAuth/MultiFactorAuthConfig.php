<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\MultiFactorAuth;

use Spryker\Glue\Kernel\AbstractBundleConfig;
use Spryker\Shared\MultiFactorAuth\MultiFactorAuthConstants;

class MultiFactorAuthConfig extends AbstractBundleConfig
{
    /**
     * @api
     *
     * @var string
     */
    public const RESOURCE_MULTI_FACTOR_AUTH_TYPES = 'multi-factor-auth-types';

    /**
     * @api
     *
     * @var string
     */
    public const RESOURCE_MULTI_FACTOR_AUTH_TRIGGER = 'multi-factor-auth-trigger';

    /**
     * @api
     *
     * @var string
     */
    public const RESOURCE_MULTI_FACTOR_AUTH_TYPE_ACTIVATE = 'multi-factor-auth-type-activate';

    /**
     * @api
     *
     * @var string
     */
    public const RESOURCE_MULTI_FACTOR_AUTH_TYPE_VERIFY = 'multi-factor-auth-type-verify';

    /**
     * @api
     *
     * @var string
     */
    public const RESOURCE_MULTI_FACTOR_AUTH_TYPE_DEACTIVATE = 'multi-factor-auth-type-deactivate';

    /**
     * @api
     *
     * @var string
     */
    public const CONTROLLER_MULTI_FACTOR_AUTH_TYPES = 'multi-factor-auth-types-resource';

    /**
     * @api
     *
     * @var string
     */
    public const CONTROLLER_MULTI_FACTOR_AUTH_TRIGGER = 'multi-factor-auth-trigger-resource';

    /**
     * @api
     *
     * @var string
     */
    public const CONTROLLER_MULTI_FACTOR_AUTH_TYPE_ACTIVATE = 'multi-factor-auth-type-activate-resource';

    /**
     * @api
     *
     * @var string
     */
    public const CONTROLLER_MULTI_FACTOR_AUTH_TYPE_VERIFY = 'multi-factor-auth-type-verify-resource';

    /**
     * @api
     *
     * @var string
     */
    public const CONTROLLER_MULTI_FACTOR_AUTH_TYPE_DEACTIVATE = 'multi-factor-auth-type-deactivate-resource';

    /**
     * @api
     *
     * @var string
     */
    public const HEADER_MULTI_FACTOR_AUTH_CODE = 'X-MFA-Code';

    /**
     * @api
     *
     * @var string
     */
    public const ERROR_CODE_MULTI_FACTOR_AUTH_CODE_MISSING = '5900';

    /**
     * @api
     *
     * @var string
     */
    public const ERROR_CODE_MULTI_FACTOR_AUTH_CODE_INVALID = '5901';

    /**
     * @api
     *
     * @var string
     */
    public const ERROR_CODE_MULTI_FACTOR_AUTH_TYPE_MISSING = '5902';

    /**
     * @api
     *
     * @var string
     */
    public const ERROR_CODE_MULTI_FACTOR_AUTH_DEACTIVATION_FAILED = '5903';

    /**
     * @api
     *
     * @var string
     */
    public const ERROR_CODE_MULTI_FACTOR_AUTH_VERIFY_FAILED = '5904';

    /**
     * @api
     *
     * @var string
     */
    public const RESPONSE_CODE_NO_CUSTOMER_IDENTIFIER = '5905';

    /**
     * @api
     *
     * @var string
     */
    public const ERROR_CODE_MULTI_FACTOR_AUTH_TYPE_NOT_FOUND = '5906';

    /**
     * @api
     *
     * @var string
     */
    public const RESPONSE_CUSTOMER_NOT_FOUND = '5907';

    /**
     * @api
     *
     * @var string
     */
    public const RESPONSE_CODE_NO_USER_IDENTIFIER = '5908';

    /**
     * @api
     *
     * @var string
     */
    public const RESPONSE_USER_NOT_FOUND = '5909';

    /**
     * @api
     *
     * @var string
     */
    public const RESPONSE_SENDING_CODE_ERROR = '5910';

    /**
     * @api
     *
     * @var string
     */
    public const ERROR_MESSAGE_MULTI_FACTOR_AUTH_CODE_MISSING = 'X-MFA-Code header is missing.';

    /**
     * @api
     *
     * @var string
     */
    public const ERROR_MESSAGE_MULTI_FACTOR_AUTH_CODE_INVALID = 'X-MFA-Code is invalid.';

    /**
     * @api
     *
     * @var string
     */
    public const ERROR_MESSAGE_MULTI_FACTOR_AUTH_TYPE_MISSING = 'Multi-factor authentication type is missing.';

    /**
     * @api
     *
     * @var string
     */
    public const ERROR_MESSAGE_MULTI_FACTOR_AUTH_DEACTIVATION_FAILED = 'Failed to deactivate multi-factor authentication.';

    /**
     * @api
     *
     * @var string
     */
    public const ERROR_MESSAGE_MULTI_FACTOR_AUTH_VERIFY_FAILED = 'Multi-factor authentication type already activated.';

    /**
     * @api
     *
     * @var string
     */
    public const RESPONSE_DETAIL_NO_CUSTOMER_IDENTIFIER = 'No customer identifier provided.';

    /**
     * @api
     *
     * @var string
     */
    public const ERROR_MESSAGE_MULTI_FACTOR_AUTH_TYPE_NOT_FOUND = 'Multi-factor authentication type is not found.';

    /**
     * @api
     *
     * @var string
     */
    public const ERROR_MESSAGE_SENDING_CODE_ERROR = 'Something went wrong while sending your code. Please try again later or contact the system administrator.';

    /**
     * @api
     *
     * @var string
     */
    public const RESPONSE_DETAIL_CUSTOMER_NOT_FOUND = 'Customer not found.';

    /**
     * @api
     *
     * @var string
     */
    public const RESPONSE_DETAIL_NO_USER_IDENTIFIER = 'No user identifier provided.';

    /**
     * @api
     *
     * @var string
     */
    public const RESPONSE_DETAIL_USER_NOT_FOUND = 'User not found.';

    /**
     * Specification:
     * - Returns a list of enabled resources for the multi-factor authentication in the following format:
     * [
     *    'resource-name',
     * ]
     *
     * @api
     *
     * @return array<string>
     */
    public function getRestApiMultiFactorAuthProtectedResources(): array
    {
        return [];
    }

    /**
     * Specification:
     * - Returns a list of enabled backend resources for the multi-factor authentication in the following format:
     * [
     *    'resource-name',
     * ]
     *
     * @api
     *
     * @return array<string>
     */
    public function getMultiFactorAuthProtectedBackendResources(): array
    {
        return [];
    }

    /**
     * Specification:
     * - Returns a list of enabled storefront resources for the multi-factor authentication in the following format:
     * [
     *    'resource-name',
     * ]
     *
     * @api
     *
     * @return array<string>
     */
    public function getMultiFactorAuthProtectedStorefrontResources(): array
    {
        return [];
    }

    /**
     * Specification:
     * - Returns a list of multi-factor authentication type statuses with their descriptions.
     *
     * @api
     *
     * @return array<int, string>
     */
    public function getMultiFactorAuthTypeStatuses(): array
    {
        return [
            MultiFactorAuthConstants::STATUS_PENDING_ACTIVATION => 'activation is pending',
            MultiFactorAuthConstants::STATUS_ACTIVE => 'activated',
            MultiFactorAuthConstants::STATUS_INACTIVE => 'deactivated',
        ];
    }
}
