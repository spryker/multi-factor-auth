<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\MultiFactorAuth\Api\Backend\Processor;

use Generated\Shared\Transfer\MultiFactorAuthCodeTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTypesCollectionTransfer;
use Generated\Shared\Transfer\UserTransfer;
use Spryker\Glue\MultiFactorAuth\Api\Backend\Exception\MultiFactorAuthBackendExceptionFactory;
use Spryker\Glue\MultiFactorAuth\Api\Backend\Validator\MultiFactorAuthUserCodeValidator;
use Spryker\Service\Container\Attributes\Plugins;
use Spryker\Shared\MultiFactorAuth\MultiFactorAuthConstants;
use Spryker\Zed\MultiFactorAuth\Business\MultiFactorAuthFacadeInterface;

class MultiFactorAuthTypeActivateBackendProcessor extends AbstractMultiFactorAuthBackendProcessor
{
    /**
     * @param array<\Spryker\Shared\MultiFactorAuthExtension\Dependency\Plugin\MultiFactorAuthPluginInterface> $multiFactorAuthPlugins
     */
    public function __construct(
        MultiFactorAuthFacadeInterface $multiFactorAuthFacade,
        MultiFactorAuthUserCodeValidator $codeValidator,
        MultiFactorAuthBackendExceptionFactory $exceptionFactory,
        #[Plugins(dependencyProviderMethod: 'getUserMultiFactorAuthPlugins')]
        protected array $multiFactorAuthPlugins = [],
    ) {
        parent::__construct($multiFactorAuthFacade, $codeValidator, $exceptionFactory);
    }

    protected function processPost(mixed $data): null
    {
        $type = $this->resolveType($data);

        if (!$this->isRegisteredType($type)) {
            throw $this->exceptionFactory->createMultiFactorAuthTypeNotFoundException();
        }

        $userTransfer = $this->getUser();
        $activeTypesCollectionTransfer = $this->codeValidator->getActiveTypes($userTransfer);

        if ($this->isTypeActive($activeTypesCollectionTransfer, $type)) {
            throw $this->exceptionFactory->createMultiFactorAuthTypeAlreadyActivatedException();
        }

        if ($this->hasActiveType($activeTypesCollectionTransfer)) {
            $this->assertActiveTypeCodeIsValid($userTransfer);
        }

        $multiFactorAuthTransfer = (new MultiFactorAuthTransfer())
            ->setType($type)
            ->setUser($userTransfer)
            ->setStatus(MultiFactorAuthConstants::STATUS_PENDING_ACTIVATION);

        $this->multiFactorAuthFacade->activateUserMultiFactorAuth($multiFactorAuthTransfer);

        $this->sendUserCode($multiFactorAuthTransfer);

        return null;
    }

    /**
     * The code proves possession of the already activated type, so it is validated against the type it was
     * issued for, not against the type being activated.
     *
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     *
     * @return void
     */
    protected function assertActiveTypeCodeIsValid(UserTransfer $userTransfer): void
    {
        $code = $this->getRequiredMultiFactorAuthCode();
        $multiFactorAuthCodeTransfer = $this->codeValidator->findCode($code, $userTransfer);

        if ($multiFactorAuthCodeTransfer->getType() === null) {
            throw $this->exceptionFactory->createMultiFactorAuthCodeInvalidException();
        }

        $codeValidationTransfer = (new MultiFactorAuthTransfer())
            ->setType($multiFactorAuthCodeTransfer->getTypeOrFail())
            ->setUser($userTransfer)
            ->setMultiFactorAuthCode((new MultiFactorAuthCodeTransfer())->setCode($code));

        if (!$this->codeValidator->isCodeValid($code, $userTransfer, $codeValidationTransfer)) {
            throw $this->exceptionFactory->createMultiFactorAuthCodeInvalidException();
        }
    }

    protected function isRegisteredType(string $type): bool
    {
        foreach ($this->multiFactorAuthPlugins as $multiFactorAuthPlugin) {
            if ($multiFactorAuthPlugin->getName() === $type) {
                return true;
            }
        }

        return false;
    }

    protected function isTypeActive(MultiFactorAuthTypesCollectionTransfer $multiFactorAuthTypesCollectionTransfer, string $type): bool
    {
        foreach ($multiFactorAuthTypesCollectionTransfer->getMultiFactorAuthTypes() as $multiFactorAuthTransfer) {
            if (
                $multiFactorAuthTransfer->getTypeOrFail() === $type
                && $multiFactorAuthTransfer->getStatus() === MultiFactorAuthConstants::STATUS_ACTIVE
            ) {
                return true;
            }
        }

        return false;
    }

    protected function hasActiveType(MultiFactorAuthTypesCollectionTransfer $multiFactorAuthTypesCollectionTransfer): bool
    {
        foreach ($multiFactorAuthTypesCollectionTransfer->getMultiFactorAuthTypes() as $multiFactorAuthTransfer) {
            if ($multiFactorAuthTransfer->getStatus() === MultiFactorAuthConstants::STATUS_ACTIVE) {
                return true;
            }
        }

        return false;
    }
}
