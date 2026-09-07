<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Yves\MultiFactorAuth\Controller;

use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Yves\Kernel\View\View;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Spryker\Yves\MultiFactorAuth\MultiFactorAuthFactory getFactory()
 * @method \Spryker\Client\MultiFactorAuth\MultiFactorAuthClientInterface getClient()
 * @method \Spryker\Yves\MultiFactorAuth\MultiFactorAuthConfig getConfig()
 */
class CustomerOauthMultiFactorAuthFlowController extends CustomerMultiFactorAuthFlowController
{
    /**
     * @uses \Spryker\Yves\MultiFactorAuth\Plugin\Router\Customer\MultiFactorAuthCustomerRouteProviderPlugin::MULTI_FACTOR_AUTH_ROUTE_GET_CUSTOMER_OAUTH_LOGIN_ENABLED_TYPES
     *
     * @var string
     */
    protected const ROUTE_CUSTOMER_OAUTH_LOGIN = '/multi-factor-auth/customer/login';

    protected const string ROUTE_CUSTOMER_OVERVIEW = '/customer/overview';

    /**
     * @uses \SprykerShop\Yves\CustomerPage\Plugin\Router\CustomerPageRouteProviderPlugin::ROUTE_LOGIN
     */
    protected const string ROUTE_CUSTOMER_LOGIN = '/login';

    protected const string REDIRECT_TEMPLATE = '@MultiFactorAuth/views/customer-oauth-login-redirect/customer-oauth-login-redirect.twig';

    protected const string MESSAGE_NO_MULTI_FACTOR_AUTH_METHOD_AVAILABLE = 'multi_factor_auth.error.no_method_available';

    public function getCustomerOauthLoginEnabledTypesAction(Request $request): View|RedirectResponse
    {
        if ($this->hasPendingMultiFactorAuthChallenge() === false) {
            return new RedirectResponse($this->resolveNoPendingChallengeRedirectUrl());
        }

        if ($this->hasUsableMultiFactorAuthMethod($request) === false) {
            $this->addErrorMessage(static::MESSAGE_NO_MULTI_FACTOR_AUTH_METHOD_AVAILABLE);

            return new RedirectResponse(static::ROUTE_CUSTOMER_LOGIN);
        }

        return $this->getEnabledTypesAction($request);
    }

    protected function hasUsableMultiFactorAuthMethod(Request $request): bool
    {
        if ($this->isSetUpMultiFactorAuthStep($request) === true) {
            return true;
        }

        return $this->assertNoTypesEnabled($this->getOptions($request)) === false;
    }

    protected function hasPendingMultiFactorAuthChallenge(): bool
    {
        return $this->getFactory()->getSessionClient()->get(static::MULTI_FACTOR_AUTH_LOGIN_CUSTOMER_EMAIL_SESSION_KEY) !== null;
    }

    protected function resolveNoPendingChallengeRedirectUrl(): string
    {
        if ($this->getFactory()->getCustomerClient()->isLoggedIn() === true) {
            return static::ROUTE_CUSTOMER_OVERVIEW;
        }

        return static::ROUTE_CUSTOMER_LOGIN;
    }

    protected function createRedirectView(string $redirectUrl): View
    {
        return $this->view(['redirectUrl' => $redirectUrl], [], static::REDIRECT_TEMPLATE);
    }

    public function sendCustomerOauthLoginCodeAction(Request $request, ?string $multiFactorAuthType = null, ?FormInterface $form = null): View
    {
        return $this->sendCodeAction($request, $multiFactorAuthType, $form);
    }

    protected function getTypeSelectionFormTemplate(): string
    {
        return '@MultiFactorAuth/views/customer-oauth-login-type-selection-form/customer-oauth-login-type-selection-form.twig';
    }

    protected function getCodeValidationFormTemplate(): string
    {
        return '@MultiFactorAuth/views/customer-oauth-login-code-validation-form/customer-oauth-login-code-validation-form.twig';
    }

    protected function createCodeVerifiedView(AbstractTransfer $identityTransfer): View
    {
        return $this->createRedirectView(static::ROUTE_CUSTOMER_OVERVIEW);
    }

    protected function createCodeBlockedView(string $errorMessage): View
    {
        $this->addErrorMessage($errorMessage);

        return $this->createRedirectView(static::ROUTE_CUSTOMER_OAUTH_LOGIN);
    }
}
