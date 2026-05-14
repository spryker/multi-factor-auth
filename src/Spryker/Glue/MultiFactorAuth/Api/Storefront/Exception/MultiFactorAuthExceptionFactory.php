<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\MultiFactorAuth\Api\Storefront\Exception;

use Spryker\ApiPlatform\Exception\GlueApiException;
use Spryker\Glue\MultiFactorAuth\MultiFactorAuthConfig;
use Symfony\Component\HttpFoundation\Response;

class MultiFactorAuthExceptionFactory
{
    public function createMultiFactorAuthCodeMissingException(): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_FORBIDDEN,
            MultiFactorAuthConfig::ERROR_CODE_MULTI_FACTOR_AUTH_CODE_MISSING,
            MultiFactorAuthConfig::ERROR_MESSAGE_MULTI_FACTOR_AUTH_CODE_MISSING,
        );
    }

    public function createMultiFactorAuthCodeInvalidException(): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_FORBIDDEN,
            MultiFactorAuthConfig::ERROR_CODE_MULTI_FACTOR_AUTH_CODE_INVALID,
            MultiFactorAuthConfig::ERROR_MESSAGE_MULTI_FACTOR_AUTH_CODE_INVALID,
        );
    }

    public function createMultiFactorAuthTypeMissingException(): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_BAD_REQUEST,
            MultiFactorAuthConfig::ERROR_CODE_MULTI_FACTOR_AUTH_TYPE_MISSING,
            MultiFactorAuthConfig::ERROR_MESSAGE_MULTI_FACTOR_AUTH_TYPE_MISSING,
        );
    }

    public function createMultiFactorAuthTypeNotFoundException(): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_BAD_REQUEST,
            MultiFactorAuthConfig::ERROR_CODE_MULTI_FACTOR_AUTH_TYPE_NOT_FOUND,
            MultiFactorAuthConfig::ERROR_MESSAGE_MULTI_FACTOR_AUTH_TYPE_NOT_FOUND,
        );
    }

    public function createMultiFactorAuthTypeAlreadyActivatedException(): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_BAD_REQUEST,
            MultiFactorAuthConfig::ERROR_CODE_MULTI_FACTOR_AUTH_VERIFY_FAILED,
            MultiFactorAuthConfig::ERROR_MESSAGE_MULTI_FACTOR_AUTH_VERIFY_FAILED,
        );
    }

    public function createMultiFactorAuthDeactivationFailedException(): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_BAD_REQUEST,
            MultiFactorAuthConfig::ERROR_CODE_MULTI_FACTOR_AUTH_DEACTIVATION_FAILED,
            MultiFactorAuthConfig::ERROR_MESSAGE_MULTI_FACTOR_AUTH_VERIFY_FAILED,
        );
    }

    public function createSendingCodeErrorException(): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            MultiFactorAuthConfig::RESPONSE_SENDING_CODE_ERROR,
            MultiFactorAuthConfig::ERROR_MESSAGE_SENDING_CODE_ERROR,
        );
    }
}
