<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\MultiFactorAuth\Api\Storefront\Processor;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Spryker\Client\Customer\CustomerClientInterface;
use Spryker\Client\MultiFactorAuth\MultiFactorAuthClientInterface;
use Spryker\Glue\MultiFactorAuth\Api\Storefront\Exception\MultiFactorAuthExceptionFactory;
use Spryker\Shared\MultiFactorAuth\MultiFactorAuthConstants;
use Throwable;

class MultiFactorAuthTriggerStorefrontProcessor extends AbstractMultiFactorAuthStorefrontProcessor
{
    public function __construct(
        MultiFactorAuthClientInterface $multiFactorAuthClient,
        protected CustomerClientInterface $customerClient,
        MultiFactorAuthExceptionFactory $exceptionFactory = new MultiFactorAuthExceptionFactory(),
    ) {
        parent::__construct($multiFactorAuthClient, $exceptionFactory);
    }

    /**
     * @param \Generated\Api\Storefront\MultiFactorAuthTriggerStorefrontResource $data
     *
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    protected function processPost(mixed $data): null
    {
        $type = $this->resolveType($data);
        $customerTransfer = $this->loadCustomer();
        $activeTypesCollectionTransfer = $this->getCustomerActiveTypes($customerTransfer);

        if (!$this->isTypeActivated($activeTypesCollectionTransfer, $type)) {
            throw $this->exceptionFactory->createMultiFactorAuthTypeNotFoundException();
        }

        $multiFactorAuthTransfer = (new MultiFactorAuthTransfer())
            ->setType($type)
            ->setCustomer($customerTransfer)
            ->setStatus(MultiFactorAuthConstants::STATUS_ACTIVE);

        try {
            $this->multiFactorAuthClient->sendCustomerCode($multiFactorAuthTransfer);
        } catch (Throwable) {
            throw $this->exceptionFactory->createSendingCodeErrorException();
        }

        return null;
    }

    /**
     * The request carries only the token claims; the send strategy mails the customer, so the full
     * record has to be loaded before it reaches one. The reference claim is always present while the
     * surrogate id is only set when the token carries it, so the id lookup is preferred and the
     * reference is the fallback — resolving by id alone would answer 500 for a token minted without
     * that claim.
     */
    protected function loadCustomer(): CustomerTransfer
    {
        $customerTransfer = $this->getCustomer();
        $idCustomer = $customerTransfer->getIdCustomer();

        if ($idCustomer !== null) {
            return $this->customerClient->getCustomerById($idCustomer);
        }

        $customerResponseTransfer = $this->customerClient->findCustomerByReference(
            (new CustomerTransfer())->setCustomerReference($customerTransfer->getCustomerReferenceOrFail()),
        );

        return $customerResponseTransfer->getCustomerTransfer() ?? $customerTransfer;
    }
}
