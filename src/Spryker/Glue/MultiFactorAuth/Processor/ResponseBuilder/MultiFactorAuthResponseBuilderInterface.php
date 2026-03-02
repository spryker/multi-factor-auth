<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace Spryker\Glue\MultiFactorAuth\Processor\ResponseBuilder;

use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;

interface MultiFactorAuthResponseBuilderInterface
{
    public function createNoCustomerIdentifierErrorResponse(): RestResponseInterface;

    public function createCustomerNotFoundResponse(): RestResponseInterface;

    public function createMissingTypeErrorResponse(): RestResponseInterface;

    public function createNotFoundTypeErrorResponse(): RestResponseInterface;

    public function createMissingMultiFactorAuthCodeError(): RestResponseInterface;

    public function createInvalidMultiFactorAuthCodeError(): RestResponseInterface;

    public function createDeactivationFailedError(): RestResponseInterface;

    public function createAlreadyActivatedMultiFactorAuthError(): RestResponseInterface;

    public function createSendingCodeError(): RestResponseInterface;

    public function createSuccessResponse(): RestResponseInterface;
}
