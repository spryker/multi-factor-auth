<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Shared\MultiFactorAuth\Helper;

use Codeception\Module;
use Generated\Shared\Transfer\MultiFactorAuthCodeTransfer;
use Orm\Zed\MultiFactorAuth\Persistence\SpyCustomerMultiFactorAuthCodesAttemptsQuery;
use Orm\Zed\MultiFactorAuth\Persistence\SpyCustomerMultiFactorAuthCodesQuery;
use Orm\Zed\MultiFactorAuth\Persistence\SpyCustomerMultiFactorAuthQuery;
use Orm\Zed\MultiFactorAuth\Persistence\SpyUserMultiFactorAuthCodesAttemptsQuery;
use Orm\Zed\MultiFactorAuth\Persistence\SpyUserMultiFactorAuthCodesQuery;
use Orm\Zed\MultiFactorAuth\Persistence\SpyUserMultiFactorAuthQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use SprykerTest\Shared\Testify\Helper\DataCleanupHelperTrait;
use SprykerTest\Shared\Testify\Helper\LocatorHelperTrait;

class MultiFactorAuthDataHelper extends Module
{
    use LocatorHelperTrait;
    use DataCleanupHelperTrait;

    /**
     * @var string
     */
    protected const COL_CODE = 'code';

    public function getMultiFactorAuthCode(string $email, string $type): MultiFactorAuthCodeTransfer
    {
        $codeEntity = SpyCustomerMultiFactorAuthCodesQuery::create()
            ->useSpyCustomerMultiFactorAuthQuery()
                ->filterByType($type)
                ->useSpyCustomerQuery()
                    ->filterByEmail($email)
                ->endUse()
            ->endUse()
            ->orderByIdCustomerMultiFactorAuthCode(Criteria::DESC)
            ->select([static::COL_CODE])
            ->findOne();

        return (new MultiFactorAuthCodeTransfer())->setCode($codeEntity);
    }

    public function getUserMultiFactorAuthCode(string $username, string $type): MultiFactorAuthCodeTransfer
    {
        $codeEntity = SpyUserMultiFactorAuthCodesQuery::create()
            ->useSpyUserMultiFactorAuthQuery()
                ->filterByType($type)
                ->useSpyUserQuery()
                    ->filterByUsername($username)
                ->endUse()
            ->endUse()
            ->orderByIdUserMultiFactorAuthCode(Criteria::DESC)
            ->select([static::COL_CODE])
            ->findOne();

        return (new MultiFactorAuthCodeTransfer())->setCode($codeEntity);
    }

    public function cleanUpMultiFactorAuthCode(string $code): void
    {
        SpyCustomerMultiFactorAuthCodesAttemptsQuery::create()->deleteAll();

        SpyCustomerMultiFactorAuthCodesQuery::create()
            ->filterByCode($code)
            ->delete();
    }

    public function cleanUpUserMultiFactorAuthCode(string $code): void
    {
        SpyUserMultiFactorAuthCodesAttemptsQuery::create()->deleteAll();

        SpyUserMultiFactorAuthCodesQuery::create()
            ->filterByCode($code)
            ->delete();
    }

    public function cleanUpCustomerMultiFactorAuth(): void
    {
        SpyCustomerMultiFactorAuthCodesAttemptsQuery::create()->deleteAll();
        SpyCustomerMultiFactorAuthCodesQuery::create()->deleteAll();
        SpyCustomerMultiFactorAuthQuery::create()->deleteAll();
    }

    public function cleanUpUserMultiFactorAuth(): void
    {
        SpyUserMultiFactorAuthCodesAttemptsQuery::create()->deleteAll();
        SpyUserMultiFactorAuthCodesQuery::create()->deleteAll();
        SpyUserMultiFactorAuthQuery::create()->deleteAll();
    }
}
