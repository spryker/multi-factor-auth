<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Yves\MultiFactorAuth\Form\Type\Extension;

use Spryker\Yves\MultiFactorAuth\MultiFactorAuthConfig;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class MultiFactorAuthValidationExtension extends BasicMultiFactorAuthTypeExtension
{
    public function __construct(
        protected MultiFactorAuthConfig $config,
        protected RequestStack $requestStack,
        protected EventSubscriberInterface $multiFactorAuthFormEventSubscriber
    ) {
        parent::__construct($config, $requestStack);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array<string, mixed> $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($this->assertFormMustNotBeValidated($builder->getForm())) {
            return;
        }

        $builder->addEventSubscriber($this->multiFactorAuthFormEventSubscriber);
    }
}
