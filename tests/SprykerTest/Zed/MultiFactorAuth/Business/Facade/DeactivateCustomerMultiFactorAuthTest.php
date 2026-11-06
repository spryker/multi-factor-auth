<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\MultiFactorAuth\Business\Facade;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Generated\Shared\Transfer\MultiFactorAuthValidationRequestTransfer;
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
 * @group DeactivateCustomerMultiFactorAuthTest
 * Add your own group annotations below this line
 */
class DeactivateCustomerMultiFactorAuthTest extends Unit
{
    protected MultiFactorAuthBusinessTester $tester;

    public function testGivenACustomerWithActiveEmailMultiFactorAuthWhenDeactivateCustomerMultiFactorAuthThenMultiFactorAuthIsNoLongerRequired(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer();
        $this->tester->getFacade()->activateCustomerMultiFactorAuth(
            (new MultiFactorAuthTransfer())
                ->setCustomer($customerTransfer)
                ->setType(MultiFactorAuthBusinessTester::TYPE_EMAIL)
                ->setStatus(MultiFactorAuthConstants::STATUS_ACTIVE),
        );

        // Act
        $this->tester->getFacade()->deactivateCustomerMultiFactorAuth(
            (new MultiFactorAuthTransfer())
                ->setCustomer((new CustomerTransfer())->setIdCustomer($customerTransfer->getIdCustomerOrFail()))
                ->setType(MultiFactorAuthBusinessTester::TYPE_EMAIL)
                ->setStatus(MultiFactorAuthConstants::STATUS_INACTIVE),
        );

        // Assert
        $multiFactorAuthValidationResponseTransfer = $this->tester->getFacade()->validateCustomerMultiFactorAuthStatus(
            (new MultiFactorAuthValidationRequestTransfer())->setCustomer($customerTransfer),
        );
        $this->assertFalse($multiFactorAuthValidationResponseTransfer->getIsRequired());
    }
}
