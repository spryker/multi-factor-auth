<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MultiFactorAuth\Business\Validator;

use DateTime;
use Generated\Shared\Transfer\MultiFactorAuthCodeTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Generated\Shared\Transfer\MultiFactorAuthValidationResponseTransfer;
use Spryker\Shared\MultiFactorAuth\MultiFactorAuthConstants;
use Spryker\Zed\MultiFactorAuth\Dependency\Facade\MultiFactorAuthToGlossaryFacadeInterface;

abstract class AbstractCodeValidator implements CodeValidatorInterface
{
    /**
     * @var string
     */
    protected const GLOSSARY_KEY_ATTEMPTS_EXCEEDED = 'multi_factor_auth.error.attempts_exceeded';

    /**
     * @var string
     */
    protected const GLOSSARY_KEY_INVALID_MULTI_FACTOR_AUTH_CODE = 'multi_factor_auth.error.invalid_code';

    /**
     * @var string
     */
    protected const GLOSSARY_KEY_EXPIRED_MULTI_FACTOR_AUTH_CODE = 'multi_factor_auth.error.expired_code';

    /**
     * @var string
     */
    protected const GLOSSARY_PARAM_REMAINING_ATTEMPTS = '%remainingAttempts%';

    public function __construct(
        protected MultiFactorAuthToGlossaryFacadeInterface $glossaryFacade
    ) {
    }

    public function validate(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthValidationResponseTransfer
    {
        $validMultiFactorAuthCodeTransfer = $this->getValidCode($multiFactorAuthTransfer);
        $this->saveMultiFactorAuthCodeAttempt($validMultiFactorAuthCodeTransfer);

        if ($this->isCodeVerificationSuccessful($validMultiFactorAuthCodeTransfer, $multiFactorAuthTransfer)) {
            return $this->handleSuccessfulCodeVerification($multiFactorAuthTransfer, new MultiFactorAuthValidationResponseTransfer());
        }

        $validMultiFactorAuthCodeTransfer->setAttempts(
            $this->getCodeEnteringAttemptsCount($validMultiFactorAuthCodeTransfer),
        );

        if ($this->hasReachedMaxAttempts($validMultiFactorAuthCodeTransfer)) {
            return $this->handleMaxAttemptsReached($validMultiFactorAuthCodeTransfer, $multiFactorAuthTransfer);
        }

        return $this->handleInvalidAttempt($validMultiFactorAuthCodeTransfer, $multiFactorAuthTransfer);
    }

    protected function isCodeVerificationSuccessful(
        MultiFactorAuthCodeTransfer $multiFactorAuthCodeTransfer,
        MultiFactorAuthTransfer $multiFactorAuthTransfer
    ): bool {
        return $multiFactorAuthCodeTransfer->getCode() === $multiFactorAuthTransfer->getMultiFactorAuthCodeOrFail()->getCode()
            && !$this->isCodeExpired($multiFactorAuthCodeTransfer)
            && $multiFactorAuthCodeTransfer->getStatus() === MultiFactorAuthConstants::CODE_UNVERIFIED;
    }

    protected function handleSuccessfulCodeVerification(
        MultiFactorAuthTransfer $multiFactorAuthTransfer,
        MultiFactorAuthValidationResponseTransfer $responseTransfer
    ): MultiFactorAuthValidationResponseTransfer {
        $multiFactorAuthTransfer->getMultiFactorAuthCodeOrFail()->setStatus(MultiFactorAuthConstants::CODE_VERIFIED);
        $this->updateCode($multiFactorAuthTransfer);

        if ($multiFactorAuthTransfer->getStatus() === MultiFactorAuthConstants::STATUS_PENDING_ACTIVATION) {
            $multiFactorAuthTransfer->setStatus(MultiFactorAuthConstants::STATUS_ACTIVE);
            $this->saveMultiFactorAuth($multiFactorAuthTransfer);
        }

        return $responseTransfer->setStatus(MultiFactorAuthConstants::CODE_VERIFIED);
    }

    protected function handleMaxAttemptsReached(
        MultiFactorAuthCodeTransfer $multiFactorAuthCodeTransfer,
        MultiFactorAuthTransfer $multiFactorAuthTransfer
    ): MultiFactorAuthValidationResponseTransfer {
        return $this->processMultiFactorAuthFailure(
            $multiFactorAuthCodeTransfer,
            $multiFactorAuthTransfer,
            MultiFactorAuthConstants::CODE_BLOCKED,
            $this->glossaryFacade->translate(static::GLOSSARY_KEY_ATTEMPTS_EXCEEDED),
        );
    }

    protected function handleInvalidAttempt(
        MultiFactorAuthCodeTransfer $multiFactorAuthCodeTransfer,
        MultiFactorAuthTransfer $multiFactorAuthTransfer
    ): MultiFactorAuthValidationResponseTransfer {
        $status = MultiFactorAuthConstants::CODE_UNVERIFIED;
        $message = $this->glossaryFacade->translate(
            static::GLOSSARY_KEY_INVALID_MULTI_FACTOR_AUTH_CODE,
            [static::GLOSSARY_PARAM_REMAINING_ATTEMPTS => $this->getAttemptsLimit() - $multiFactorAuthCodeTransfer->getAttempts()],
        );

        if ($this->isCodeExpired($multiFactorAuthCodeTransfer)) {
            $status = MultiFactorAuthConstants::CODE_BLOCKED;
            $message = $this->glossaryFacade->translate(static::GLOSSARY_KEY_EXPIRED_MULTI_FACTOR_AUTH_CODE);
        }

        return $this->processMultiFactorAuthFailure(
            $multiFactorAuthCodeTransfer,
            $multiFactorAuthTransfer,
            $status,
            $message,
        );
    }

    protected function processMultiFactorAuthFailure(
        MultiFactorAuthCodeTransfer $multiFactorAuthCodeTransfer,
        MultiFactorAuthTransfer $multiFactorAuthTransfer,
        int $status,
        string $message
    ): MultiFactorAuthValidationResponseTransfer {
        $multiFactorAuthCodeTransfer->setStatus($status);
        $multiFactorAuthTransfer->setMultiFactorAuthCode($multiFactorAuthCodeTransfer);

        $this->updateCode($multiFactorAuthTransfer);

        return (new MultiFactorAuthValidationResponseTransfer())
            ->setStatus($status)
            ->setMessage($message);
    }

    protected function isCodeExpired(MultiFactorAuthCodeTransfer $multiFactorAuthCodeTransfer): bool
    {
        return new DateTime($multiFactorAuthCodeTransfer->getExpirationDateOrFail()) < new DateTime();
    }

    protected function hasReachedMaxAttempts(MultiFactorAuthCodeTransfer $multiFactorAuthCodeTransfer): bool
    {
        return $multiFactorAuthCodeTransfer->getAttempts() >= $this->getAttemptsLimit();
    }

    abstract protected function getValidCode(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthCodeTransfer;

    abstract protected function saveMultiFactorAuthCodeAttempt(MultiFactorAuthCodeTransfer $multiFactorAuthCodeTransfer): void;

    abstract protected function updateCode(MultiFactorAuthTransfer $multiFactorAuthTransfer): void;

    abstract protected function saveMultiFactorAuth(MultiFactorAuthTransfer $multiFactorAuthTransfer): void;

    abstract protected function getCodeEnteringAttemptsCount(MultiFactorAuthCodeTransfer $multiFactorAuthCodeTransfer): int;

    abstract protected function getAttemptsLimit(): int;
}
