<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\MultiFactorAuth\Business\Facade;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\MultiFactorAuthCriteriaTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Generated\Shared\Transfer\MultiFactorAuthValidationRequestTransfer;
use Generated\Shared\Transfer\UserTransfer;
use Spryker\Shared\MultiFactorAuth\MultiFactorAuthConstants;
use SprykerTest\Zed\MultiFactorAuth\MultiFactorAuthBusinessTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group MultiFactorAuth
 * @group Business
 * @group Facade
 * @group DeactivateUserMultiFactorAuthTest
 * Add your own group annotations below this line
 */
class DeactivateUserMultiFactorAuthTest extends Unit
{
    protected MultiFactorAuthBusinessTester $tester;

    public function testGivenAUserWithActiveEmailMultiFactorAuthWhenDeactivateUserMultiFactorAuthThenTheTypeIsNoLongerReturnedAsActive(): void
    {
        // Arrange
        $userTransfer = $this->tester->haveUser();
        $this->tester->getFacade()->activateUserMultiFactorAuth(
            $this->createMultiFactorAuthTransfer($userTransfer, MultiFactorAuthConstants::STATUS_ACTIVE),
        );

        // Act
        $this->tester->getFacade()->deactivateUserMultiFactorAuth(
            $this->createMultiFactorAuthTransfer($userTransfer, MultiFactorAuthConstants::STATUS_INACTIVE),
        );

        // Assert
        $multiFactorAuthTypesCollectionTransfer = $this->tester->getFacade()->getUserMultiFactorAuthTypes(
            (new MultiFactorAuthCriteriaTransfer())->setUser($userTransfer),
        );
        $this->assertCount(0, $multiFactorAuthTypesCollectionTransfer->getMultiFactorAuthTypes());
    }

    public function testGivenAUserWithActiveEmailMultiFactorAuthWhenDeactivateUserMultiFactorAuthThenMultiFactorAuthIsNoLongerRequired(): void
    {
        // Arrange
        $userTransfer = $this->tester->haveUser();
        $this->tester->getFacade()->activateUserMultiFactorAuth(
            $this->createMultiFactorAuthTransfer($userTransfer, MultiFactorAuthConstants::STATUS_ACTIVE),
        );

        // Act
        $this->tester->getFacade()->deactivateUserMultiFactorAuth(
            $this->createMultiFactorAuthTransfer($userTransfer, MultiFactorAuthConstants::STATUS_INACTIVE),
        );

        // Assert
        $multiFactorAuthValidationResponseTransfer = $this->tester->getFacade()->validateUserMultiFactorAuthStatus(
            (new MultiFactorAuthValidationRequestTransfer())->setUser($userTransfer),
        );
        $this->assertFalse($multiFactorAuthValidationResponseTransfer->getIsRequired());
    }

    protected function createMultiFactorAuthTransfer(UserTransfer $userTransfer, int $status): MultiFactorAuthTransfer
    {
        return (new MultiFactorAuthTransfer())
            ->setUser($userTransfer)
            ->setType(MultiFactorAuthBusinessTester::TYPE_EMAIL)
            ->setStatus($status);
    }
}
