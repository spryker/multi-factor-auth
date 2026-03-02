<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MultiFactorAuth\Persistence\Mapper;

use Generated\Shared\Transfer\MultiFactorAuthCodeTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTypesCollectionTransfer;
use Orm\Zed\MultiFactorAuth\Persistence\SpyCustomerMultiFactorAuth;
use Orm\Zed\MultiFactorAuth\Persistence\SpyCustomerMultiFactorAuthCodes;
use Orm\Zed\MultiFactorAuth\Persistence\SpyUserMultiFactorAuth;
use Orm\Zed\MultiFactorAuth\Persistence\SpyUserMultiFactorAuthCodes;
use Propel\Runtime\Collection\Collection;

class MultiFactorAuthMapper
{
    public function mapCustomerMultiFactorAuthCodeEntityToMultiFactorAuthCodeTransfer(
        SpyCustomerMultiFactorAuthCodes $customerMultiFactorAuthCode,
        MultiFactorAuthCodeTransfer $multiFactorAuthCodeTransfer
    ): MultiFactorAuthCodeTransfer {
        return $multiFactorAuthCodeTransfer->fromArray($customerMultiFactorAuthCode->toArray(), true)
            ->setIdCode($customerMultiFactorAuthCode->getIdCustomerMultiFactorAuthCode())
            ->setType($customerMultiFactorAuthCode->getSpyCustomerMultiFactorAuth()->getType());
    }

    public function mapUserMultiFactorAuthCodeEntityToMultiFactorAuthCodeTransfer(
        SpyUserMultiFactorAuthCodes $userMultiFactorAuthCode,
        MultiFactorAuthCodeTransfer $multiFactorAuthCodeTransfer
    ): MultiFactorAuthCodeTransfer {
        return $multiFactorAuthCodeTransfer->fromArray($userMultiFactorAuthCode->toArray(), true)
            ->setIdCode($userMultiFactorAuthCode->getIdUserMultiFactorAuthCode())
            ->setType($userMultiFactorAuthCode->getSpyUserMultiFactorAuth()->getType());
    }

    public function mapMultiFactorAuthEntitiesToMultiFactorAuthTypesCollectionTransfer(
        Collection $multiFactorAuthEntities,
        MultiFactorAuthTypesCollectionTransfer $multiFactorAuthTypesCollectionTransfer
    ): MultiFactorAuthTypesCollectionTransfer {
        foreach ($multiFactorAuthEntities as $multiFactorAuthEntity) {
            $multiFactorAuthTransfer = new MultiFactorAuthTransfer();
            $multiFactorAuthTransfer->fromArray($multiFactorAuthEntity->toArray(), true);

            $multiFactorAuthTypesCollectionTransfer->addMultiFactorAuth($multiFactorAuthTransfer);
        }

        return $multiFactorAuthTypesCollectionTransfer;
    }

    public function mapMultiFactorAuthTransferToCustomerMultiFactorAuthEntity(
        MultiFactorAuthTransfer $multiFactorAuthTransfer,
        SpyCustomerMultiFactorAuth $customerMultiFactorAuth
    ): SpyCustomerMultiFactorAuth {
        $customerMultiFactorAuth->fromArray($multiFactorAuthTransfer->toArray());

        if ($multiFactorAuthTransfer->getCustomer() !== null) {
            $customerMultiFactorAuth->setFkCustomer($multiFactorAuthTransfer->getCustomerOrFail()->getIdCustomerOrFail());
        }

        return $customerMultiFactorAuth;
    }

    public function mapMultiFactorAuthTransferToUserMultiFactorAuthEntity(
        MultiFactorAuthTransfer $multiFactorAuthTransfer,
        SpyUserMultiFactorAuth $userMultiFactorAuth
    ): SpyUserMultiFactorAuth {
        $userMultiFactorAuth->fromArray($multiFactorAuthTransfer->toArray());

        if ($multiFactorAuthTransfer->getUser() !== null) {
            $userMultiFactorAuth->setFkUser($multiFactorAuthTransfer->getUserOrFail()->getIdUserOrFail());
        }

        return $userMultiFactorAuth;
    }
}
