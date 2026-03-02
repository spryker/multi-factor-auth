<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MultiFactorAuth\Dependency\Facade;

use Generated\Shared\Transfer\CustomerCriteriaTransfer;
use Generated\Shared\Transfer\CustomerResponseTransfer;

class MultiFactorAuthToCustomerFacadeBridge implements MultiFactorAuthToCustomerFacadeInterface
{
    /**
     * @var \Spryker\Zed\Customer\Business\CustomerFacadeInterface
     */
    protected $customerFacade;

    /**
     * @param \Spryker\Zed\Customer\Business\CustomerFacadeInterface $customerFacade
     */
    public function __construct($customerFacade)
    {
        $this->customerFacade = $customerFacade;
    }

    public function getCustomerByCriteria(CustomerCriteriaTransfer $customerCriteriaTransfer): CustomerResponseTransfer
    {
        return $this->customerFacade->getCustomerByCriteria($customerCriteriaTransfer);
    }
}
