<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\MultiFactorAuth\Api\Storefront\Provider;

use Generated\Api\Storefront\MultiFactorAuthTypesStorefrontResource;
use Generated\Shared\Transfer\MultiFactorAuthCriteriaTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Spryker\ApiPlatform\State\Provider\AbstractStorefrontProvider;
use Spryker\Client\MultiFactorAuth\MultiFactorAuthClientInterface;
use Spryker\Glue\MultiFactorAuth\MultiFactorAuthConfig;
use Spryker\Service\Container\Attributes\Plugins;
use Spryker\Shared\MultiFactorAuth\MultiFactorAuthConstants;

class MultiFactorAuthTypesStorefrontProvider extends AbstractStorefrontProvider
{
    /**
     * @param array<\Spryker\Shared\MultiFactorAuthExtension\Dependency\Plugin\MultiFactorAuthPluginInterface> $multiFactorAuthPlugins
     */
    public function __construct(
        protected MultiFactorAuthClientInterface $multiFactorAuthClient,
        protected MultiFactorAuthConfig $multiFactorAuthConfig,
        #[Plugins(dependencyProviderMethod: 'getCustomerMultiFactorAuthPlugins')]
        protected array $multiFactorAuthPlugins = [],
    ) {
    }

    /**
     * @return array<\Generated\Api\Storefront\MultiFactorAuthTypesStorefrontResource>
     */
    protected function provideCollection(): array
    {
        if (!$this->hasCustomer()) {
            return [];
        }

        $customerTransfer = $this->getCustomer();
        $statusLabels = $this->multiFactorAuthConfig->getMultiFactorAuthTypeStatuses();
        $resources = [];
        $processedTypes = [];

        $activeCollectionTransfer = $this->multiFactorAuthClient->getCustomerMultiFactorAuthTypes(
            (new MultiFactorAuthCriteriaTransfer())->setCustomer($customerTransfer),
        );

        foreach ($activeCollectionTransfer->getMultiFactorAuthTypes() as $multiFactorAuthTransfer) {
            $processedTypes[$multiFactorAuthTransfer->getTypeOrFail()] = true;
            $resources[] = $this->buildResource($multiFactorAuthTransfer, $statusLabels);
        }

        $pendingCollectionTransfer = $this->multiFactorAuthClient->getCustomerMultiFactorAuthTypes(
            (new MultiFactorAuthCriteriaTransfer())
                ->setCustomer($customerTransfer)
                ->setStatuses([MultiFactorAuthConstants::STATUS_PENDING_ACTIVATION]),
        );

        foreach ($pendingCollectionTransfer->getMultiFactorAuthTypes() as $multiFactorAuthTransfer) {
            $processedTypes[$multiFactorAuthTransfer->getTypeOrFail()] = true;
            $resources[] = $this->buildResource($multiFactorAuthTransfer, $statusLabels);
        }

        foreach ($this->getRegisteredTypeNames() as $type) {
            if (isset($processedTypes[$type])) {
                continue;
            }

            $inactiveTransfer = (new MultiFactorAuthTransfer())
                ->setType($type)
                ->setCustomer($customerTransfer)
                ->setStatus(MultiFactorAuthConstants::STATUS_INACTIVE);

            $resources[] = $this->buildResource($inactiveTransfer, $statusLabels);
        }

        return $resources;
    }

    /**
     * @param array<int, string> $statusLabels
     */
    protected function buildResource(MultiFactorAuthTransfer $multiFactorAuthTransfer, array $statusLabels): MultiFactorAuthTypesStorefrontResource
    {
        $resource = new MultiFactorAuthTypesStorefrontResource();
        $resource->type = $multiFactorAuthTransfer->getTypeOrFail();
        $resource->status = $statusLabels[$multiFactorAuthTransfer->getStatusOrFail()] ?? (string)$multiFactorAuthTransfer->getStatus();

        return $resource;
    }

    /**
     * @return array<string>
     */
    protected function getRegisteredTypeNames(): array
    {
        $typeNames = [];

        foreach ($this->multiFactorAuthPlugins as $plugin) {
            $typeNames[] = $plugin->getName();
        }

        return $typeNames;
    }
}
