<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\MultiFactorAuth\Plugin\GlueBackendApiApplication;

use Generated\Shared\Transfer\GlueResourceMethodCollectionTransfer;
use Generated\Shared\Transfer\GlueResourceMethodConfigurationTransfer;
use Generated\Shared\Transfer\RestMultiFactorAuthAttributesTransfer;
use Spryker\Glue\GlueApplication\Plugin\GlueApplication\Backend\AbstractResourcePlugin;
use Spryker\Glue\GlueJsonApiConventionExtension\Dependency\Plugin\JsonApiResourceInterface;
use Spryker\Glue\MultiFactorAuth\Controller\BackendApi\MultiFactorAuthTriggerBackendResourceController;
use Spryker\Glue\MultiFactorAuth\MultiFactorAuthConfig;

/**
 * @method \Spryker\Glue\MultiFactorAuth\MultiFactorAuthFactory getFactory()
 */
class MultiFactorAuthTriggerBackendResourcePlugin extends AbstractResourcePlugin implements JsonApiResourceInterface
{
    public function getType(): string
    {
        return MultiFactorAuthConfig::RESOURCE_MULTI_FACTOR_AUTH_TRIGGER;
    }

    public function getController(): string
    {
        return MultiFactorAuthTriggerBackendResourceController::class;
    }

    public function getDeclaredMethods(): GlueResourceMethodCollectionTransfer
    {
        return (new GlueResourceMethodCollectionTransfer())
            ->setPost(
                (new GlueResourceMethodConfigurationTransfer())
                    ->setAction('postAction')
                    ->setAttributes(RestMultiFactorAuthAttributesTransfer::class),
            );
    }
}
