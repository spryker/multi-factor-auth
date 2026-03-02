<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MultiFactorAuth\Persistence;

use Orm\Zed\MultiFactorAuth\Persistence\SpyCustomerMultiFactorAuth;
use Orm\Zed\MultiFactorAuth\Persistence\SpyCustomerMultiFactorAuthCodes;
use Orm\Zed\MultiFactorAuth\Persistence\SpyCustomerMultiFactorAuthCodesAttempts;
use Orm\Zed\MultiFactorAuth\Persistence\SpyCustomerMultiFactorAuthCodesAttemptsQuery;
use Orm\Zed\MultiFactorAuth\Persistence\SpyCustomerMultiFactorAuthCodesQuery;
use Orm\Zed\MultiFactorAuth\Persistence\SpyCustomerMultiFactorAuthQuery;
use Orm\Zed\MultiFactorAuth\Persistence\SpyUserMultiFactorAuth;
use Orm\Zed\MultiFactorAuth\Persistence\SpyUserMultiFactorAuthCodes;
use Orm\Zed\MultiFactorAuth\Persistence\SpyUserMultiFactorAuthCodesAttempts;
use Orm\Zed\MultiFactorAuth\Persistence\SpyUserMultiFactorAuthCodesAttemptsQuery;
use Orm\Zed\MultiFactorAuth\Persistence\SpyUserMultiFactorAuthCodesQuery;
use Orm\Zed\MultiFactorAuth\Persistence\SpyUserMultiFactorAuthQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;
use Spryker\Zed\MultiFactorAuth\Persistence\Mapper\MultiFactorAuthMapper;

/**
 * @method \Spryker\Zed\MultiFactorAuth\Persistence\MultiFactorAuthEntityManagerInterface getEntityManager()
 * @method \Spryker\Zed\MultiFactorAuth\Persistence\MultiFactorAuthRepositoryInterface getRepository()
 * @method \Spryker\Zed\MultiFactorAuth\MultiFactorAuthConfig getConfig()
 */
class MultiFactorAuthPersistenceFactory extends AbstractPersistenceFactory
{
    public function createSpyCustomerMultiFactorAuthCodeQuery(): SpyCustomerMultiFactorAuthCodesQuery
    {
        return SpyCustomerMultiFactorAuthCodesQuery::create();
    }

    public function createSpyCustomerMultiFactorAuthQuery(): SpyCustomerMultiFactorAuthQuery
    {
        return SpyCustomerMultiFactorAuthQuery::create();
    }

    public function createSpyCustomerMultiFactorAuthCodesAttemptsQuery(): SpyCustomerMultiFactorAuthCodesAttemptsQuery
    {
        return SpyCustomerMultiFactorAuthCodesAttemptsQuery::create();
    }

    public function createSpyCustomerMultiFactorAuthCodeEntity(): SpyCustomerMultiFactorAuthCodes
    {
        return new SpyCustomerMultiFactorAuthCodes();
    }

    public function createSpyCustomerMultiFactorAuthEntity(): SpyCustomerMultiFactorAuth
    {
        return new SpyCustomerMultiFactorAuth();
    }

    public function createSpyUserMultiFactorAuthEntity(): SpyUserMultiFactorAuth
    {
        return new SpyUserMultiFactorAuth();
    }

    public function createSpyUserMultiFactorAuthQuery(): SpyUserMultiFactorAuthQuery
    {
        return SpyUserMultiFactorAuthQuery::create();
    }

    public function createSpyUserMultiFactorAuthCodeQuery(): SpyUserMultiFactorAuthCodesQuery
    {
        return SpyUserMultiFactorAuthCodesQuery::create();
    }

    public function createSpyUserMultiFactorAuthCodeEntity(): SpyUserMultiFactorAuthCodes
    {
        return new SpyUserMultiFactorAuthCodes();
    }

    public function createSpyCustomerMultiFactorAuthCodesAttemptsEntity(): SpyCustomerMultiFactorAuthCodesAttempts
    {
        return new SpyCustomerMultiFactorAuthCodesAttempts();
    }

    public function createSpyUserMultiFactorAuthCodesAttemptsEntity(): SpyUserMultiFactorAuthCodesAttempts
    {
        return new SpyUserMultiFactorAuthCodesAttempts();
    }

    public function createSpyUserMultiFactorAuthCodesAttemptsQuery(): SpyUserMultiFactorAuthCodesAttemptsQuery
    {
        return SpyUserMultiFactorAuthCodesAttemptsQuery::create();
    }

    public function createMultiFactorAuthMapper(): MultiFactorAuthMapper
    {
        return new MultiFactorAuthMapper();
    }
}
