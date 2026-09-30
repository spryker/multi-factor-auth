<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\MultiFactorAuth\Api\Backend\Provider;

use Generated\Api\Backend\MultiFactorAuthTypesBackendResource;
use Generated\Shared\Transfer\MultiFactorAuthCriteriaTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Spryker\ApiPlatform\State\Provider\AbstractBackendProvider;
use Spryker\Glue\MultiFactorAuth\MultiFactorAuthConfig;
use Spryker\Service\Container\Attributes\Plugins;
use Spryker\Shared\MultiFactorAuth\MultiFactorAuthConstants;
use Spryker\Zed\MultiFactorAuth\Business\MultiFactorAuthFacadeInterface;

/**
 * Lists every registered user MFA type once: activated and pending types come from the facade, the
 * remaining registered types are reported as deactivated.
 */
class MultiFactorAuthTypesBackendProvider extends AbstractBackendProvider
{
    /**
     * @param array<\Spryker\Shared\MultiFactorAuthExtension\Dependency\Plugin\MultiFactorAuthPluginInterface> $multiFactorAuthPlugins
     */
    public function __construct(
        protected MultiFactorAuthFacadeInterface $multiFactorAuthFacade,
        protected MultiFactorAuthConfig $multiFactorAuthConfig,
        #[Plugins(dependencyProviderMethod: 'getUserMultiFactorAuthPlugins')]
        protected array $multiFactorAuthPlugins = [],
    ) {
    }

    /**
     * @return array<\Generated\Api\Backend\MultiFactorAuthTypesBackendResource>
     */
    protected function provideCollection(): array
    {
        if (!$this->hasUser()) {
            return [];
        }

        $userTransfer = $this->getUser();
        $statusLabels = $this->multiFactorAuthConfig->getMultiFactorAuthTypeStatuses();
        $resources = [];
        $processedTypes = [];

        $activeCollectionTransfer = $this->multiFactorAuthFacade->getUserMultiFactorAuthTypes(
            (new MultiFactorAuthCriteriaTransfer())->setUser($userTransfer),
        );

        foreach ($activeCollectionTransfer->getMultiFactorAuthTypes() as $multiFactorAuthTransfer) {
            $processedTypes[$multiFactorAuthTransfer->getTypeOrFail()] = true;
            $resources[] = $this->buildResource($multiFactorAuthTransfer, $statusLabels);
        }

        $pendingCollectionTransfer = $this->multiFactorAuthFacade->getUserMultiFactorAuthTypes(
            (new MultiFactorAuthCriteriaTransfer())
                ->setUser($userTransfer)
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

            $inactiveMultiFactorAuthTransfer = (new MultiFactorAuthTransfer())
                ->setType($type)
                ->setUser($userTransfer)
                ->setStatus(MultiFactorAuthConstants::STATUS_INACTIVE);

            $resources[] = $this->buildResource($inactiveMultiFactorAuthTransfer, $statusLabels);
        }

        return $resources;
    }

    /**
     * @param array<int, string> $statusLabels
     */
    protected function buildResource(MultiFactorAuthTransfer $multiFactorAuthTransfer, array $statusLabels): MultiFactorAuthTypesBackendResource
    {
        $resource = new MultiFactorAuthTypesBackendResource();
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

        foreach ($this->multiFactorAuthPlugins as $multiFactorAuthPlugin) {
            $typeNames[] = $multiFactorAuthPlugin->getName();
        }

        return $typeNames;
    }
}
