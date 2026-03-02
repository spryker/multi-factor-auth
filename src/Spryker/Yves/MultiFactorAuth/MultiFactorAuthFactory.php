<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Yves\MultiFactorAuth;

use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Yves\Kernel\AbstractFactory;
use Spryker\Yves\MultiFactorAuth\Activator\Agent\AgentMultiFactorAuthActivator;
use Spryker\Yves\MultiFactorAuth\Activator\Agent\AgentMultiFactorAuthActivatorInterface;
use Spryker\Yves\MultiFactorAuth\Activator\Customer\CustomerMultiFactorAuthActivator;
use Spryker\Yves\MultiFactorAuth\Activator\Customer\CustomerMultiFactorAuthActivatorInterface;
use Spryker\Yves\MultiFactorAuth\Deactivator\Agent\AgentMultiFactorAuthDeactivator;
use Spryker\Yves\MultiFactorAuth\Deactivator\Agent\AgentMultiFactorAuthDeactivatorInterface;
use Spryker\Yves\MultiFactorAuth\Deactivator\Customer\CustomerMultiFactorAuthDeactivator;
use Spryker\Yves\MultiFactorAuth\Deactivator\Customer\CustomerMultiFactorAuthDeactivatorInterface;
use Spryker\Yves\MultiFactorAuth\Dependency\Client\MultiFactorAuthToAgentClientInterface;
use Spryker\Yves\MultiFactorAuth\Dependency\Client\MultiFactorAuthToCustomerClientInterface;
use Spryker\Yves\MultiFactorAuth\Dependency\Client\MultiFactorAuthToSessionClientInterface;
use Spryker\Yves\MultiFactorAuth\Form\CodeValidationForm;
use Spryker\Yves\MultiFactorAuth\Form\DataProvider\Agent\AgentTypeSelectionFormDataProvider;
use Spryker\Yves\MultiFactorAuth\Form\DataProvider\Customer\CustomerTypeSelectionFormDataProvider;
use Spryker\Yves\MultiFactorAuth\Form\Type\Extension\MultiFactorAuthHandlerExtension;
use Spryker\Yves\MultiFactorAuth\Form\Type\Extension\MultiFactorAuthValidationExtension;
use Spryker\Yves\MultiFactorAuth\Form\TypeSelectionForm;
use Spryker\Yves\MultiFactorAuth\Reader\Agent\AgentMultiFactorAuthReader;
use Spryker\Yves\MultiFactorAuth\Reader\Agent\AgentMultiFactorAuthReaderInterface;
use Spryker\Yves\MultiFactorAuth\Reader\Customer\CustomerMultiFactorAuthReader;
use Spryker\Yves\MultiFactorAuth\Reader\Customer\CustomerMultiFactorAuthReaderInterface;
use Spryker\Yves\MultiFactorAuth\Reader\Request\RequestReader;
use Spryker\Yves\MultiFactorAuth\Reader\Request\RequestReaderInterface;
use Spryker\Yves\MultiFactorAuth\Subscriber\MultiFactorAuthFormEventSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeExtensionInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @method \Spryker\Yves\MultiFactorAuth\MultiFactorAuthConfig getConfig()
 * @method \Spryker\Client\MultiFactorAuth\MultiFactorAuthClientInterface getClient()
 */
class MultiFactorAuthFactory extends AbstractFactory
{
    public function getCustomerClient(): MultiFactorAuthToCustomerClientInterface
    {
        return $this->getProvidedDependency(MultiFactorAuthDependencyProvider::CLIENT_CUSTOMER);
    }

    public function getAgentClient(): MultiFactorAuthToAgentClientInterface
    {
        return $this->getProvidedDependency(MultiFactorAuthDependencyProvider::CLIENT_AGENT);
    }

    /**
     * @return array<\Spryker\Shared\MultiFactorAuthExtension\Dependency\Plugin\MultiFactorAuthPluginInterface>
     */
    public function getCustomerMultiFactorAuthPlugins(): array
    {
        return $this->getProvidedDependency(MultiFactorAuthDependencyProvider::PLUGINS_CUSTOMER_MULTI_FACTOR_AUTH);
    }

    /**
     * @return array<\Spryker\Shared\MultiFactorAuthExtension\Dependency\Plugin\MultiFactorAuthPluginInterface>
     */
    public function getAgentMultiFactorAuthPlugins(): array
    {
        return $this->getProvidedDependency(MultiFactorAuthDependencyProvider::PLUGINS_AGENT_MULTI_FACTOR_AUTH);
    }

    /**
     * @return array<\Spryker\Shared\MultiFactorAuthExtension\Dependency\Plugin\PostLoginMultiFactorAuthenticationPluginInterface>
     */
    public function getPostLoginMultiFactorAuthenticationPlugins(): array
    {
        return $this->getProvidedDependency(MultiFactorAuthDependencyProvider::PLUGINS_POST_LOGIN_MULTI_FACTOR_AUTH);
    }

    public function getFormFactory(): FormFactory
    {
        return $this->getProvidedDependency(ApplicationConstants::FORM_FACTORY);
    }

    /**
     * @param array<string, mixed> $formOptions
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getTypeSelectionForm(array $formOptions = []): FormInterface
    {
        return $this->getFormFactory()->create(TypeSelectionForm::class, null, $formOptions);
    }

    /**
     * @param array<string, mixed> $formOptions
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getCodeValidationForm(array $formOptions = []): FormInterface
    {
        return $this->getFormFactory()->create(CodeValidationForm::class, null, $formOptions);
    }

    public function createCustomerTypeSelectionFormDataProvider(): CustomerTypeSelectionFormDataProvider
    {
        return new CustomerTypeSelectionFormDataProvider(
            $this->getClient(),
            $this->getCustomerMultiFactorAuthPlugins(),
        );
    }

    public function createAgentTypeSelectionFormDataProvider(): AgentTypeSelectionFormDataProvider
    {
        return new AgentTypeSelectionFormDataProvider(
            $this->getClient(),
            $this->getAgentMultiFactorAuthPlugins(),
        );
    }

    public function getTranslatorService(): TranslatorInterface
    {
        return $this->getProvidedDependency(MultiFactorAuthDependencyProvider::SERVICE_TRANSLATOR);
    }

    /**
     * @return \Spryker\Yves\MultiFactorAuth\Subscriber\MultiFactorAuthFormEventSubscriber
     */
    public function createMultiFactorAuthFormEventSubscriber(): EventSubscriberInterface
    {
        return new MultiFactorAuthFormEventSubscriber(
            $this->getClient(),
            $this->getCustomerClient(),
            $this->getTranslatorService(),
        );
    }

    public function createMultiFactorAuthValidationExtension(): FormTypeExtensionInterface
    {
        return new MultiFactorAuthValidationExtension(
            $this->getConfig(),
            $this->getRequestStackService(),
            $this->createMultiFactorAuthFormEventSubscriber(),
        );
    }

    public function createMultiFactorAuthHandlerExtension(): FormTypeExtensionInterface
    {
        return new MultiFactorAuthHandlerExtension(
            $this->getConfig(),
            $this->getRequestStackService(),
            $this->getProvidedDependency(MultiFactorAuthDependencyProvider::TWIG_ENVIRONMENT),
            $this->getClient(),
            $this->getCustomerClient(),
        );
    }

    public function createCustomerMultiFactorAuthReader(): CustomerMultiFactorAuthReaderInterface
    {
        return new CustomerMultiFactorAuthReader(
            $this->getClient(),
            $this->getCustomerMultiFactorAuthPlugins(),
        );
    }

    public function createAgentMultiFactorAuthReader(): AgentMultiFactorAuthReaderInterface
    {
        return new AgentMultiFactorAuthReader(
            $this->getClient(),
            $this->getAgentMultiFactorAuthPlugins(),
        );
    }

    public function createCustomerMultiFactorAuthActivator(): CustomerMultiFactorAuthActivatorInterface
    {
        return new CustomerMultiFactorAuthActivator(
            $this->getClient(),
            $this->createRequestReader(),
        );
    }

    public function createAgentMultiFactorAuthActivator(): AgentMultiFactorAuthActivatorInterface
    {
        return new AgentMultiFactorAuthActivator(
            $this->getClient(),
            $this->createRequestReader(),
        );
    }

    /**
     * @return \Spryker\Yves\MultiFactorAuth\Deactivator\Customer\CustomerMultiFactorAuthDeactivator
     */
    public function createCustomerMultiFactorAuthDeactivator(): CustomerMultiFactorAuthDeactivatorInterface
    {
        return new CustomerMultiFactorAuthDeactivator(
            $this->getClient(),
            $this->createRequestReader(),
        );
    }

    /**
     * @return \Spryker\Yves\MultiFactorAuth\Deactivator\Agent\AgentMultiFactorAuthDeactivator
     */
    public function createAgentMultiFactorAuthDeactivator(): AgentMultiFactorAuthDeactivatorInterface
    {
        return new AgentMultiFactorAuthDeactivator(
            $this->getClient(),
            $this->createRequestReader(),
        );
    }

    public function createRequestReader(): RequestReaderInterface
    {
        return new RequestReader();
    }

    public function getRequestStackService(): RequestStack
    {
        return $this->getProvidedDependency(MultiFactorAuthDependencyProvider::SERVICE_REQUEST_STACK);
    }

    public function getCsrfTokenManager(): CsrfTokenManagerInterface
    {
        return $this->getProvidedDependency(MultiFactorAuthDependencyProvider::SERVICE_FORM_CSRF_PROVIDER);
    }

    public function getSessionClient(): MultiFactorAuthToSessionClientInterface
    {
        return $this->getProvidedDependency(MultiFactorAuthDependencyProvider::CLIENT_SESSION);
    }
}
