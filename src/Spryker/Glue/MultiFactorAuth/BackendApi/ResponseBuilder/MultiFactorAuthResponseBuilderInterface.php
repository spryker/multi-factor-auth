<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace Spryker\Glue\MultiFactorAuth\BackendApi\ResponseBuilder;

use Generated\Shared\Transfer\GlueResponseTransfer;

interface MultiFactorAuthResponseBuilderInterface
{
    public function createNoUserIdentifierErrorResponse(): GlueResponseTransfer;

    public function createUserNotFoundResponse(): GlueResponseTransfer;

    public function createMissingTypeErrorResponse(): GlueResponseTransfer;

    public function createNotFoundTypeErrorResponse(): GlueResponseTransfer;

    public function createMissingMultiFactorAuthCodeError(): GlueResponseTransfer;

    public function createInvalidMultiFactorAuthCodeError(): GlueResponseTransfer;

    public function createDeactivationFailedError(): GlueResponseTransfer;

    public function createAlreadyActivatedMultiFactorAuthError(): GlueResponseTransfer;

    public function createSendingCodeError(): GlueResponseTransfer;

    public function createSuccessResponse(): GlueResponseTransfer;
}
