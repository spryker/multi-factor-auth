<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MultiFactorAuth\Business\Validator\Customer;

use Generated\Shared\Transfer\MultiFactorAuthCodeTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Spryker\Zed\MultiFactorAuth\Business\Validator\AbstractCodeValidator;
use Spryker\Zed\MultiFactorAuth\Dependency\Facade\MultiFactorAuthToGlossaryFacadeInterface;
use Spryker\Zed\MultiFactorAuth\MultiFactorAuthConfig;
use Spryker\Zed\MultiFactorAuth\Persistence\MultiFactorAuthEntityManagerInterface;
use Spryker\Zed\MultiFactorAuth\Persistence\MultiFactorAuthRepositoryInterface;

class CustomerCodeValidator extends AbstractCodeValidator
{
    public function __construct(
        protected MultiFactorAuthRepositoryInterface $repository,
        protected MultiFactorAuthEntityManagerInterface $entityManager,
        MultiFactorAuthToGlossaryFacadeInterface $glossaryFacade,
        protected MultiFactorAuthConfig $config
    ) {
        parent::__construct($glossaryFacade);
    }

    protected function getValidCode(MultiFactorAuthTransfer $multiFactorAuthTransfer): MultiFactorAuthCodeTransfer
    {
        return $this->repository->getCustomerCode($multiFactorAuthTransfer);
    }

    protected function saveMultiFactorAuthCodeAttempt(MultiFactorAuthCodeTransfer $multiFactorAuthCodeTransfer): void
    {
        $this->entityManager->saveCustomerMultiFactorAuthCodeAttempt($multiFactorAuthCodeTransfer);
    }

    protected function updateCode(MultiFactorAuthTransfer $multiFactorAuthTransfer): void
    {
        $this->entityManager->updateCustomerCode($multiFactorAuthTransfer);
    }

    protected function saveMultiFactorAuth(MultiFactorAuthTransfer $multiFactorAuthTransfer): void
    {
        $this->entityManager->saveCustomerMultiFactorAuth($multiFactorAuthTransfer);
    }

    protected function getCodeEnteringAttemptsCount(MultiFactorAuthCodeTransfer $multiFactorAuthCodeTransfer): int
    {
        return $this->repository->getCustomerCodeEnteringAttemptsCount($multiFactorAuthCodeTransfer);
    }

    protected function getAttemptsLimit(): int
    {
        return $this->config->getCustomerAttemptsLimit();
    }
}
