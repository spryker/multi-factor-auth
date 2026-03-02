<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Yves\MultiFactorAuth\Deactivator\Agent;

use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Generated\Shared\Transfer\UserTransfer;
use Spryker\Client\MultiFactorAuth\MultiFactorAuthClientInterface;
use Spryker\Shared\MultiFactorAuth\MultiFactorAuthConstants;
use Spryker\Yves\MultiFactorAuth\Controller\AgentMultiFactorAuthFlowController;
use Spryker\Yves\MultiFactorAuth\Reader\Request\RequestReaderInterface;
use Symfony\Component\HttpFoundation\Request;

class AgentMultiFactorAuthDeactivator implements AgentMultiFactorAuthDeactivatorInterface
{
    public function __construct(
        protected MultiFactorAuthClientInterface $client,
        protected RequestReaderInterface $requestReader
    ) {
    }

    public function deactivate(Request $request, UserTransfer $userTransfer): void
    {
        $isDeactivation = $this->requestReader->get($request, AgentMultiFactorAuthFlowController::IS_DEACTIVATION);

        $type = $isDeactivation ? $this->requestReader->get($request, AgentMultiFactorAuthFlowController::TYPE_TO_SET_UP) : $request->query->get(MultiFactorAuthTransfer::TYPE);

        $multiFactorAuthTransfer = (new MultiFactorAuthTransfer())
            ->setUser($userTransfer)
            ->setStatus(MultiFactorAuthConstants::STATUS_INACTIVE)
            ->setType($type);

        $this->client->deactivateAgentMultiFactorAuth($multiFactorAuthTransfer);
    }
}
