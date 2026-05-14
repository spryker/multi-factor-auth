<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\MultiFactorAuth\Api\Storefront\Processor;

use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTypesCollectionTransfer;
use Spryker\Client\Customer\CustomerClientInterface;
use Spryker\Client\MultiFactorAuth\MultiFactorAuthClientInterface;
use Spryker\Glue\MultiFactorAuth\Api\Storefront\Exception\MultiFactorAuthExceptionFactory;
use Spryker\Service\Container\Attributes\Plugins;
use Spryker\Shared\MultiFactorAuth\MultiFactorAuthConstants;
use Throwable;

class MultiFactorAuthTypeActivateStorefrontProcessor extends AbstractMultiFactorAuthStorefrontProcessor
{
    /**
     * @param array<\Spryker\Shared\MultiFactorAuthExtension\Dependency\Plugin\MultiFactorAuthPluginInterface> $multiFactorAuthPlugins
     */
    public function __construct(
        MultiFactorAuthClientInterface $multiFactorAuthClient,
        protected CustomerClientInterface $customerClient,
        #[Plugins(dependencyProviderMethod: 'getCustomerMultiFactorAuthPlugins')]
        protected array $multiFactorAuthPlugins = [],
        MultiFactorAuthExceptionFactory $exceptionFactory = new MultiFactorAuthExceptionFactory(),
    ) {
        parent::__construct($multiFactorAuthClient, $exceptionFactory);
    }

    /**
     * @param \Generated\Api\Storefront\MultiFactorAuthTypeActivateStorefrontResource $data
     *
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    protected function processPost(mixed $data): null
    {
        $type = $this->resolveType($data);

        if (!$this->isRegisteredType($type)) {
            throw $this->exceptionFactory->createMultiFactorAuthTypeNotFoundException();
        }

        $customerTransfer = $this->customerClient->getCustomerById($this->getCustomer()->getIdCustomerOrFail());
        $activeTypesCollectionTransfer = $this->getCustomerActiveTypes($customerTransfer);

        if ($this->isTypeActive($activeTypesCollectionTransfer, $type)) {
            throw $this->exceptionFactory->createMultiFactorAuthTypeAlreadyActivatedException();
        }

        if ($this->hasActiveType($activeTypesCollectionTransfer)) {
            $code = $this->getRequiredMultiFactorAuthCode();

            $codeValidationTransfer = (new MultiFactorAuthTransfer())
                ->setType($type)
                ->setCustomer($customerTransfer);

            if (!$this->isMultiFactorAuthCodeValid($code, $customerTransfer, $codeValidationTransfer)) {
                throw $this->exceptionFactory->createMultiFactorAuthCodeInvalidException();
            }
        }

        $multiFactorAuthTransfer = (new MultiFactorAuthTransfer())
            ->setType($type)
            ->setCustomer($customerTransfer)
            ->setStatus(MultiFactorAuthConstants::STATUS_PENDING_ACTIVATION);

        $this->multiFactorAuthClient->activateCustomerMultiFactorAuth($multiFactorAuthTransfer);

        try {
            $this->multiFactorAuthClient->sendCustomerCode(
                $multiFactorAuthTransfer->setStatus(MultiFactorAuthConstants::STATUS_ACTIVE),
            );
        } catch (Throwable) {
            throw $this->exceptionFactory->createSendingCodeErrorException();
        }

        return null;
    }

    protected function isRegisteredType(string $type): bool
    {
        foreach ($this->multiFactorAuthPlugins as $plugin) {
            if ($plugin->getName() === $type) {
                return true;
            }
        }

        return false;
    }

    protected function isTypeActive(MultiFactorAuthTypesCollectionTransfer $collection, string $type): bool
    {
        foreach ($collection->getMultiFactorAuthTypes() as $activatedType) {
            if (
                $activatedType->getTypeOrFail() === $type
                && $activatedType->getStatus() === MultiFactorAuthConstants::STATUS_ACTIVE
            ) {
                return true;
            }
        }

        return false;
    }

    protected function hasActiveType(MultiFactorAuthTypesCollectionTransfer $collection): bool
    {
        foreach ($collection->getMultiFactorAuthTypes() as $multiFactorAuthTransfer) {
            if ($multiFactorAuthTransfer->getStatus() === MultiFactorAuthConstants::STATUS_ACTIVE) {
                return true;
            }
        }

        return false;
    }
}
