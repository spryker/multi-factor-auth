<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Yves\MultiFactorAuth\Reader\Customer;

use Generated\Shared\Transfer\MultiFactorAuthCriteriaTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTypesCollectionTransfer;
use Spryker\Client\MultiFactorAuth\MultiFactorAuthClientInterface;
use Spryker\Shared\MultiFactorAuth\MultiFactorAuthConstants;

class CustomerMultiFactorAuthReader implements CustomerMultiFactorAuthReaderInterface
{
    /**
     * @param \Spryker\Client\MultiFactorAuth\MultiFactorAuthClientInterface $client
     * @param array<\Spryker\Shared\MultiFactorAuthExtension\Dependency\Plugin\MultiFactorAuthPluginInterface> $customerMultiFactorAuthPlugins
     */
    public function __construct(
        protected MultiFactorAuthClientInterface $client,
        protected array $customerMultiFactorAuthPlugins
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\MultiFactorAuthCriteriaTransfer $multiFactorAuthCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\MultiFactorAuthTypesCollectionTransfer
     */
    public function getAvailableCustomerMultiFactorAuthTypes(
        MultiFactorAuthCriteriaTransfer $multiFactorAuthCriteriaTransfer
    ): MultiFactorAuthTypesCollectionTransfer {
        $configuredCustomerMultiFactorAuthTypes = $this->client->getCustomerMultiFactorAuthTypes($multiFactorAuthCriteriaTransfer);

        $multiFactorAuthTypes = [];
        foreach ($configuredCustomerMultiFactorAuthTypes->getMultiFactorAuthTypes() as $multiFactorAuthTransfer) {
            if ($multiFactorAuthTransfer->getStatus() !== MultiFactorAuthConstants::STATUS_ACTIVE) {
                continue;
            }

            $multiFactorAuthTypes[$multiFactorAuthTransfer->getType()] = $multiFactorAuthTransfer;
        }

        $wiredMultiFactorAuthTypesIndexedByName = [];
        foreach ($this->customerMultiFactorAuthPlugins as $plugin) {
            $wiredMultiFactorAuthTypesIndexedByName[$plugin->getName()] = $plugin;

            if (isset($multiFactorAuthTypes[$plugin->getName()])) {
                continue;
            }

            $multiFactorAuthTransfer = (new MultiFactorAuthTransfer())
                ->setType($plugin->getName())
                ->setStatus(MultiFactorAuthConstants::STATUS_INACTIVE)
                ->setCustomer($multiFactorAuthCriteriaTransfer->getCustomerOrFail());

            $multiFactorAuthTypes[$plugin->getName()] = $multiFactorAuthTransfer;
        }

        $multiFactorAuthTypes = array_intersect_key($multiFactorAuthTypes, $wiredMultiFactorAuthTypesIndexedByName);
        ksort($multiFactorAuthTypes);
        $multiFactorAuthTypesCollectionTransfer = new MultiFactorAuthTypesCollectionTransfer();

        foreach ($multiFactorAuthTypes as $multiFactorAuthTypeTransfer) {
            $multiFactorAuthTypesCollectionTransfer->addMultiFactorAuth($multiFactorAuthTypeTransfer);
        }

        return $multiFactorAuthTypesCollectionTransfer;
    }

    /**
     * @return bool
     */
    public function isCustomerMultiFactorAuthPluginsAvailable(): bool
    {
        return $this->customerMultiFactorAuthPlugins !== [];
    }
}
