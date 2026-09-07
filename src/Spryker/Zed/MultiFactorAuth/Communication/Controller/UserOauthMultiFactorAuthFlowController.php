<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MultiFactorAuth\Communication\Controller;

use Generated\Shared\Transfer\UserTransfer;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method \Spryker\Zed\MultiFactorAuth\Business\MultiFactorAuthFacadeInterface getFacade()
 * @method \Spryker\Zed\MultiFactorAuth\Communication\MultiFactorAuthCommunicationFactory getFactory()
 * @method \Spryker\Zed\MultiFactorAuth\Persistence\MultiFactorAuthRepositoryInterface getRepository()
 */
class UserOauthMultiFactorAuthFlowController extends UserController
{
    /**
     * @uses \Spryker\Zed\SecurityOauthUser\Communication\Security\Handler\OauthUserAuthenticationSuccessHandler::ROUTE_USER_OAUTH_MFA
     */
    protected const string ROUTE_USER_OAUTH_MFA = '/multi-factor-auth/user-oauth-multi-factor-auth-flow/get-user-oauth-login-enabled-types';

    protected const string ROUTE_BACK_OFFICE_HOME = '/';

    /**
     * @uses \Spryker\Zed\SecurityGui\SecurityGuiConfig::LOGIN_PATH
     */
    protected const string ROUTE_BACK_OFFICE_LOGIN = '/security-gui/login';

    protected const string OAUTH_SEND_CODE_TWIG_TEMPLATE = '@MultiFactorAuth/UserOauthMultiFactorAuthFlow/send-code.twig';

    protected const string REDIRECT_TWIG_TEMPLATE = '@MultiFactorAuth/UserOauthMultiFactorAuthFlow/redirect.twig';

    protected const string MESSAGE_NO_MULTI_FACTOR_AUTH_METHOD_AVAILABLE = 'Multi-Factor Authentication is required for your account, but no verification method is currently available. Please contact site support.';

    /**
     * @return \Symfony\Component\HttpFoundation\Response|array<string, mixed>
     */
    public function getUserOauthLoginEnabledTypesAction(Request $request)
    {
        if ($this->hasPendingMultiFactorAuthChallenge() === false) {
            return new RedirectResponse($this->resolveNoPendingChallengeRedirectUrl());
        }

        if ($this->hasUsableMultiFactorAuthMethod($request) === false) {
            $this->addErrorMessage(static::MESSAGE_NO_MULTI_FACTOR_AUTH_METHOD_AVAILABLE);

            return new RedirectResponse(static::ROUTE_BACK_OFFICE_LOGIN);
        }

        return $this->getEnabledTypesAction($request);
    }

    protected function hasUsableMultiFactorAuthMethod(Request $request): bool
    {
        if ($this->isSetUpMultiFactorAuthStep($request) === true) {
            return true;
        }

        $options = $this->getFactory()->createTypeSelectionFormDataProvider()->getOptions($request);

        return $this->assertNoTypesEnabled($options) === false;
    }

    protected function hasPendingMultiFactorAuthChallenge(): bool
    {
        return $this->getFactory()->getSessionClient()->get(static::MULTI_FACTOR_AUTH_LOGIN_USER_EMAIL_SESSION_KEY) !== null;
    }

    protected function resolveNoPendingChallengeRedirectUrl(): string
    {
        if ($this->getFactory()->getUserFacade()->hasCurrentUser() === true) {
            return static::ROUTE_BACK_OFFICE_HOME;
        }

        return static::ROUTE_BACK_OFFICE_LOGIN;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response|array<string, mixed>
     */
    public function sendUserOauthLoginCodeAction(
        Request $request,
        ?string $multiFactorAuthType = null,
        ?FormInterface $form = null
    ) {
        return $this->sendCodeAction($request, $multiFactorAuthType, $form);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response|array<string, mixed>
     */
    protected function createCodeVerifiedResponse(UserTransfer $userTransfer)
    {
        return $this->renderView(static::REDIRECT_TWIG_TEMPLATE, ['redirectUrl' => static::ROUTE_BACK_OFFICE_HOME]);
    }

    protected function createCodeBlockedResponse(string $errorMessage): Response
    {
        $this->addErrorMessage($errorMessage);

        return $this->renderView(static::REDIRECT_TWIG_TEMPLATE, ['redirectUrl' => static::ROUTE_USER_OAUTH_MFA]);
    }

    protected function getSendCodeTwigTemplate(): string
    {
        return static::OAUTH_SEND_CODE_TWIG_TEMPLATE;
    }
}
