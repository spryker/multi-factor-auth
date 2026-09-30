<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\MultiFactorAuth\Api\Backend\Processor;

use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Spryker\ApiPlatform\State\Processor\AbstractBackendProcessor;
use Spryker\Glue\MultiFactorAuth\Api\Backend\Exception\MultiFactorAuthBackendExceptionFactory;
use Spryker\Glue\MultiFactorAuth\Api\Backend\Validator\MultiFactorAuthUserCodeValidator;
use Spryker\Glue\MultiFactorAuth\MultiFactorAuthConfig;
use Spryker\Shared\Log\LoggerTrait;
use Spryker\Shared\MultiFactorAuth\MultiFactorAuthConstants;
use Spryker\Zed\MultiFactorAuth\Business\MultiFactorAuthFacadeInterface;
use Throwable;

/**
 * The acting user is the `UserTransfer` resolved from the access token by
 * {@see \Spryker\Glue\User\Api\Backend\EventSubscriber\UserIdentityRequestSubscriber}; Back Office users and
 * merchant users share the user MFA tables, so one processor set serves both.
 */
abstract class AbstractMultiFactorAuthBackendProcessor extends AbstractBackendProcessor
{
    use LoggerTrait;

    public function __construct(
        protected MultiFactorAuthFacadeInterface $multiFactorAuthFacade,
        protected MultiFactorAuthUserCodeValidator $codeValidator,
        protected MultiFactorAuthBackendExceptionFactory $exceptionFactory,
    ) {
    }

    protected function resolveType(mixed $data): string
    {
        $type = $data->type ?? null;

        if (!is_string($type) || $type === '') {
            throw $this->exceptionFactory->createMultiFactorAuthTypeMissingException();
        }

        return $type;
    }

    protected function getRequiredMultiFactorAuthCode(): string
    {
        $code = $this->getRequest()->headers->get(MultiFactorAuthConfig::HEADER_MULTI_FACTOR_AUTH_CODE);

        if ($code === null || $code === '') {
            throw $this->exceptionFactory->createMultiFactorAuthCodeMissingException();
        }

        return $code;
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     *
     * @return void
     */
    protected function sendUserCode(MultiFactorAuthTransfer $multiFactorAuthTransfer): void
    {
        try {
            $this->multiFactorAuthFacade->sendUserCode(
                $multiFactorAuthTransfer->setStatus(MultiFactorAuthConstants::STATUS_ACTIVE),
            );
        } catch (Throwable $throwable) {
            $this->getLogger()->error(
                sprintf(
                    'Sending the multi-factor authentication code of type "%s" to user %d failed with %s: %s',
                    $multiFactorAuthTransfer->getType(),
                    $multiFactorAuthTransfer->getUser()?->getIdUser(),
                    $throwable::class,
                    $throwable->getMessage(),
                ),
                ['exception' => $throwable],
            );

            throw $this->exceptionFactory->createSendingCodeErrorException($throwable);
        }
    }
}
