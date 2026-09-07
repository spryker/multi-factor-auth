<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\MultiFactorAuth\Communication\Controller;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Generated\Shared\Transfer\MultiFactorAuthValidationResponseTransfer;
use Generated\Shared\Transfer\UserTransfer;
use ReflectionMethod;
use Spryker\Shared\MultiFactorAuth\MultiFactorAuthConstants;
use Spryker\Zed\MultiFactorAuth\Communication\Controller\UserOauthMultiFactorAuthFlowController;
use Spryker\Zed\MultiFactorAuth\Communication\Form\DataProvider\TypeSelectionFormDataProvider;
use Spryker\Zed\MultiFactorAuth\Communication\MultiFactorAuthCommunicationFactory;
use Spryker\Zed\MultiFactorAuth\Dependency\Client\MultiFactorAuthToSessionClientInterface;
use Spryker\Zed\MultiFactorAuth\Dependency\Facade\MultiFactorAuthToUserFacadeInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group MultiFactorAuth
 * @group Communication
 * @group Controller
 * @group UserOauthMultiFactorAuthFlowControllerTest
 * Add your own group annotations below this line
 */
class UserOauthMultiFactorAuthFlowControllerTest extends Unit
{
    /**
     * @uses \Spryker\Zed\MultiFactorAuth\Communication\Controller\UserOauthMultiFactorAuthFlowController::ROUTE_USER_OAUTH_MFA
     */
    protected const string ROUTE_USER_OAUTH_MFA = '/multi-factor-auth/user-oauth-multi-factor-auth-flow/get-user-oauth-login-enabled-types';

    /**
     * @uses \Spryker\Zed\MultiFactorAuth\Communication\Controller\UserOauthMultiFactorAuthFlowController::ROUTE_BACK_OFFICE_HOME
     */
    protected const string ROUTE_BACK_OFFICE_HOME = '/';

    /**
     * @uses \Spryker\Zed\MultiFactorAuth\Communication\Controller\UserOauthMultiFactorAuthFlowController::OAUTH_SEND_CODE_TWIG_TEMPLATE
     */
    protected const string OAUTH_SEND_CODE_TWIG_TEMPLATE = '@MultiFactorAuth/UserOauthMultiFactorAuthFlow/send-code.twig';

    /**
     * @uses \Spryker\Zed\MultiFactorAuth\Communication\Controller\UserOauthMultiFactorAuthFlowController::REDIRECT_TWIG_TEMPLATE
     */
    protected const string REDIRECT_TWIG_TEMPLATE = '@MultiFactorAuth/UserOauthMultiFactorAuthFlow/redirect.twig';

    protected const string SOME_MULTI_FACTOR_AUTH_TYPE = 'EMAIL';

    protected const string SOME_ERROR_MESSAGE = 'multi_factor_auth.error.code_blocked';

    protected const string CODE_VALIDATION_FORM_NAME = 'multiFactorAuthCodeValidationForm';

    /**
     * @uses \Spryker\Zed\MultiFactorAuth\Communication\Controller\UserOauthMultiFactorAuthFlowController::ROUTE_BACK_OFFICE_LOGIN
     */
    protected const string ROUTE_BACK_OFFICE_LOGIN = '/security-gui/login';

    /**
     * @uses \Spryker\Zed\MultiFactorAuth\Communication\Controller\UserOauthMultiFactorAuthFlowController::MESSAGE_NO_MULTI_FACTOR_AUTH_METHOD_AVAILABLE
     */
    protected const string MESSAGE_NO_METHOD_AVAILABLE = 'Multi-Factor Authentication is required for your account, but no verification method is currently available. Please contact site support.';

    /**
     * @uses \\Spryker\\Zed\\MultiFactorAuth\\Communication\\Controller\\UserController::MULTI_FACTOR_AUTH_LOGIN_USER_EMAIL_SESSION_KEY
     */
    protected const string MULTI_FACTOR_AUTH_LOGIN_EMAIL_SESSION_KEY = '_multi_factor_auth_login_user_email';

    public function testGetUserOauthLoginEnabledTypesActionDelegatesToGetEnabledTypesAction(): void
    {
        // Arrange
        $request = new Request();
        $expectedResponse = new Response();

        $controllerMock = $this->getMockBuilder(UserOauthMultiFactorAuthFlowController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getEnabledTypesAction', 'hasPendingMultiFactorAuthChallenge', 'hasUsableMultiFactorAuthMethod'])
            ->getMock();

        $controllerMock->method('hasPendingMultiFactorAuthChallenge')->willReturn(true);
        $controllerMock->method('hasUsableMultiFactorAuthMethod')->willReturn(true);
        $controllerMock->expects($this->once())
            ->method('getEnabledTypesAction')
            ->with($request)
            ->willReturn($expectedResponse);

        // Act
        $response = $controllerMock->getUserOauthLoginEnabledTypesAction($request);

        // Assert
        $this->assertSame($expectedResponse, $response, 'Expected the OAuth action to delegate to the shared enabled-types action.');
    }

    public function testGetUserOauthLoginEnabledTypesActionRedirectsAwayWhenNoChallengeIsPending(): void
    {
        // Arrange
        $controllerMock = $this->getMockBuilder(UserOauthMultiFactorAuthFlowController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getEnabledTypesAction', 'hasPendingMultiFactorAuthChallenge', 'resolveNoPendingChallengeRedirectUrl'])
            ->getMock();

        $controllerMock->method('hasPendingMultiFactorAuthChallenge')->willReturn(false);
        $controllerMock->method('resolveNoPendingChallengeRedirectUrl')->willReturn(static::ROUTE_BACK_OFFICE_HOME);
        $controllerMock->expects($this->never())->method('getEnabledTypesAction');

        // Act
        $response = $controllerMock->getUserOauthLoginEnabledTypesAction(new Request());

        // Assert
        $this->assertInstanceOf(
            RedirectResponse::class,
            $response,
            'Expected the challenge page to redirect away when no Multi-Factor Authentication challenge is outstanding.',
        );
        $this->assertSame(static::ROUTE_BACK_OFFICE_HOME, $response->getTargetUrl());
    }

    public function testGetUserOauthLoginEnabledTypesActionRedirectsToLoginWhenNoMethodIsUsable(): void
    {
        // Arrange
        $controllerMock = $this->getMockBuilder(UserOauthMultiFactorAuthFlowController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getEnabledTypesAction', 'hasPendingMultiFactorAuthChallenge', 'hasUsableMultiFactorAuthMethod', 'addErrorMessage'])
            ->getMock();

        $controllerMock->method('hasPendingMultiFactorAuthChallenge')->willReturn(true);
        $controllerMock->method('hasUsableMultiFactorAuthMethod')->willReturn(false);
        $controllerMock->expects($this->once())->method('addErrorMessage')->with(static::MESSAGE_NO_METHOD_AVAILABLE);
        $controllerMock->expects($this->never())->method('getEnabledTypesAction');

        // Act
        $response = $controllerMock->getUserOauthLoginEnabledTypesAction(new Request());

        // Assert
        $this->assertInstanceOf(
            RedirectResponse::class,
            $response,
            'Expected an explained bounce to the login page when no Multi-Factor Authentication method can be offered.',
        );
        $this->assertSame(static::ROUTE_BACK_OFFICE_LOGIN, $response->getTargetUrl());
    }

    public function testSendUserOauthLoginCodeActionDelegatesToSendCodeAction(): void
    {
        // Arrange
        $request = new Request();
        $form = $this->createMock(FormInterface::class);
        $expectedResponse = new Response();

        $controllerMock = $this->getMockBuilder(UserOauthMultiFactorAuthFlowController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['sendCodeAction'])
            ->getMock();

        $controllerMock->expects($this->once())
            ->method('sendCodeAction')
            ->with($request, static::SOME_MULTI_FACTOR_AUTH_TYPE, $form)
            ->willReturn($expectedResponse);

        // Act
        $response = $controllerMock->sendUserOauthLoginCodeAction($request, static::SOME_MULTI_FACTOR_AUTH_TYPE, $form);

        // Assert
        $this->assertSame($expectedResponse, $response, 'Expected the OAuth action to delegate to the shared send-code action.');
    }

    public function testGetSendCodeTwigTemplateReturnsOauthSendCodeTemplate(): void
    {
        // Arrange
        $controller = $this->getMockBuilder(UserOauthMultiFactorAuthFlowController::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        // Act
        $template = (new ReflectionMethod($controller, 'getSendCodeTwigTemplate'))->invoke($controller);

        // Assert
        $this->assertSame(
            static::OAUTH_SEND_CODE_TWIG_TEMPLATE,
            $template,
            'Expected the OAuth flow to render its own send-code template instead of the modal one.',
        );
    }

    public function testCreateCodeVerifiedResponseRedirectsToBackOfficeHome(): void
    {
        // Arrange
        $expectedResponse = new Response();
        $controllerMock = $this->getMockBuilder(UserOauthMultiFactorAuthFlowController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['renderView'])
            ->getMock();

        $controllerMock->expects($this->once())
            ->method('renderView')
            ->with(static::REDIRECT_TWIG_TEMPLATE, ['redirectUrl' => static::ROUTE_BACK_OFFICE_HOME])
            ->willReturn($expectedResponse);

        // Act
        $response = (new ReflectionMethod($controllerMock, 'createCodeVerifiedResponse'))
            ->invoke($controllerMock, new UserTransfer());

        // Assert
        $this->assertSame($expectedResponse, $response, 'Expected a verified code to redirect the user to the Back Office home page.');
    }

    public function testCreateCodeBlockedResponseAddsErrorMessageAndRedirectsBackToOauthMultiFactorAuthRoute(): void
    {
        // Arrange
        $expectedResponse = new Response();
        $controllerMock = $this->getMockBuilder(UserOauthMultiFactorAuthFlowController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['renderView', 'addErrorMessage'])
            ->getMock();

        $controllerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with(static::SOME_ERROR_MESSAGE)
            ->willReturn($controllerMock);

        $controllerMock->expects($this->once())
            ->method('renderView')
            ->with(static::REDIRECT_TWIG_TEMPLATE, ['redirectUrl' => static::ROUTE_USER_OAUTH_MFA])
            ->willReturn($expectedResponse);

        // Act
        $response = (new ReflectionMethod($controllerMock, 'createCodeBlockedResponse'))
            ->invoke($controllerMock, static::SOME_ERROR_MESSAGE);

        // Assert
        $this->assertSame($expectedResponse, $response, 'Expected a blocked code to send the user back to the OAuth Multi-Factor Authentication step.');
    }

    public function testExecuteCodeValidationRendersOauthRedirectWhenCodeIsVerified(): void
    {
        // Arrange
        $userTransfer = new UserTransfer();
        $expectedResponse = new Response();
        $controllerMock = $this->getMockBuilder(UserOauthMultiFactorAuthFlowController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['validateCode', 'isSelectedTypeVerificationRequired', 'executePostLoginMultiFactorAuthenticationPlugins', 'renderView'])
            ->getMock();

        $controllerMock->method('validateCode')->willReturn(
            (new MultiFactorAuthValidationResponseTransfer())->setStatus(MultiFactorAuthConstants::CODE_VERIFIED),
        );
        $controllerMock->method('isSelectedTypeVerificationRequired')->willReturn(false);
        $controllerMock->expects($this->once())
            ->method('executePostLoginMultiFactorAuthenticationPlugins')
            ->with($userTransfer);
        $controllerMock->expects($this->once())
            ->method('renderView')
            ->with(static::REDIRECT_TWIG_TEMPLATE, ['redirectUrl' => static::ROUTE_BACK_OFFICE_HOME])
            ->willReturn($expectedResponse);

        // Act
        $response = (new ReflectionMethod($controllerMock, 'executeCodeValidation'))
            ->invoke($controllerMock, new Request(), $this->createCodeValidationFormMock(), $userTransfer);

        // Assert
        $this->assertSame(
            $expectedResponse,
            $response,
            'Expected a verified code to render the OAuth redirect template instead of the base validation-response template.',
        );
    }

    public function testExecuteCodeValidationRendersOauthRedirectWhenCodeIsBlocked(): void
    {
        // Arrange
        $expectedResponse = new Response();
        $controllerMock = $this->getMockBuilder(UserOauthMultiFactorAuthFlowController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['validateCode', 'executePostLoginMultiFactorAuthenticationPlugins', 'addErrorMessage', 'renderView'])
            ->getMock();

        $controllerMock->method('validateCode')->willReturn(
            (new MultiFactorAuthValidationResponseTransfer())
                ->setStatus(MultiFactorAuthConstants::CODE_BLOCKED)
                ->setMessage(static::SOME_ERROR_MESSAGE),
        );
        $controllerMock->expects($this->never())->method('executePostLoginMultiFactorAuthenticationPlugins');
        $controllerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with(static::SOME_ERROR_MESSAGE)
            ->willReturn($controllerMock);
        $controllerMock->expects($this->once())
            ->method('renderView')
            ->with(static::REDIRECT_TWIG_TEMPLATE, ['redirectUrl' => static::ROUTE_USER_OAUTH_MFA])
            ->willReturn($expectedResponse);

        // Act
        $response = (new ReflectionMethod($controllerMock, 'executeCodeValidation'))
            ->invoke($controllerMock, new Request(), $this->createCodeValidationFormMock(), new UserTransfer());

        // Assert
        $this->assertSame(
            $expectedResponse,
            $response,
            'Expected a blocked code to render the OAuth redirect template instead of the base validation-response template.',
        );
    }

    protected function createCodeValidationFormMock(): FormInterface
    {
        $formMock = $this->createMock(FormInterface::class);
        $formMock->method('getName')->willReturn(static::CODE_VALIDATION_FORM_NAME);
        $formMock->method('getData')->willReturn([MultiFactorAuthTransfer::TYPE => static::SOME_MULTI_FACTOR_AUTH_TYPE]);

        return $formMock;
    }

    public function testResolveNoPendingChallengeRedirectUrlSendsAuthenticatedUserToBackOfficeHome(): void
    {
        // Arrange
        $controllerMock = $this->createControllerWithCurrentUser(true);

        // Act
        $url = (new ReflectionMethod($controllerMock, 'resolveNoPendingChallengeRedirectUrl'))->invoke($controllerMock);

        // Assert
        $this->assertSame(static::ROUTE_BACK_OFFICE_HOME, $url);
    }

    public function testResolveNoPendingChallengeRedirectUrlSendsAnonymousVisitorToLogin(): void
    {
        // Arrange
        $controllerMock = $this->createControllerWithCurrentUser(false);

        // Act
        $url = (new ReflectionMethod($controllerMock, 'resolveNoPendingChallengeRedirectUrl'))->invoke($controllerMock);

        // Assert
        $this->assertSame(static::ROUTE_BACK_OFFICE_LOGIN, $url);
    }

    protected function createControllerWithCurrentUser(bool $hasCurrentUser): UserOauthMultiFactorAuthFlowController
    {
        $userFacadeMock = $this->createMock(MultiFactorAuthToUserFacadeInterface::class);
        $userFacadeMock->method('hasCurrentUser')->willReturn($hasCurrentUser);

        $factoryMock = $this->createMock(MultiFactorAuthCommunicationFactory::class);
        $factoryMock->method('getUserFacade')->willReturn($userFacadeMock);

        $controllerMock = $this->getMockBuilder(UserOauthMultiFactorAuthFlowController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFactory'])
            ->getMock();
        $controllerMock->method('getFactory')->willReturn($factoryMock);

        return $controllerMock;
    }

    /**
     * @dataProvider usableMultiFactorAuthMethodDataProvider
     *
     * @param array<string, mixed> $options
     */
    public function testHasUsableMultiFactorAuthMethod(array $options, bool $isSetUpStep, bool $expected, string $message): void
    {
        // Arrange
        $dataProviderMock = $this->createMock(TypeSelectionFormDataProvider::class);
        $dataProviderMock->method('getOptions')->willReturn($options);

        $factoryMock = $this->createMock(MultiFactorAuthCommunicationFactory::class);
        $factoryMock->method('createTypeSelectionFormDataProvider')->willReturn($dataProviderMock);

        $controllerMock = $this->getMockBuilder(UserOauthMultiFactorAuthFlowController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFactory', 'isSetUpMultiFactorAuthStep'])
            ->getMock();

        $controllerMock->method('getFactory')->willReturn($factoryMock);
        $controllerMock->method('isSetUpMultiFactorAuthStep')->willReturn($isSetUpStep);

        // Act
        $hasUsableMethod = (new ReflectionMethod($controllerMock, 'hasUsableMultiFactorAuthMethod'))
            ->invoke($controllerMock, new Request());

        // Assert
        $this->assertSame($expected, $hasUsableMethod, $message);
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function usableMultiFactorAuthMethodDataProvider(): array
    {
        return [
            'no enabled types outside a set-up step' => [
                [UserOauthMultiFactorAuthFlowController::TYPES => []],
                false,
                false,
                'Expected an account whose active factors cannot be offered to have no usable method.',
            ],
            'enabled types present' => [
                [UserOauthMultiFactorAuthFlowController::TYPES => ['email']],
                false,
                true,
                'Expected an offerable factor to count as a usable method.',
            ],
            'no enabled types during a set-up step' => [
                [UserOauthMultiFactorAuthFlowController::TYPES => []],
                true,
                true,
                'Expected the set-up flow to proceed: it legitimately starts with no enabled types.',
            ],
        ];
    }

    /**
     * @dataProvider pendingMultiFactorAuthChallengeDataProvider
     */
    public function testHasPendingMultiFactorAuthChallenge(?string $sessionValue, bool $expected, string $message): void
    {
        // Arrange
        $sessionClientMock = $this->createMock(MultiFactorAuthToSessionClientInterface::class);
        $sessionClientMock->method('get')
            ->with(static::MULTI_FACTOR_AUTH_LOGIN_EMAIL_SESSION_KEY)
            ->willReturn($sessionValue);

        $factoryMock = $this->createMock(MultiFactorAuthCommunicationFactory::class);
        $factoryMock->method('getSessionClient')->willReturn($sessionClientMock);

        $controllerMock = $this->getMockBuilder(UserOauthMultiFactorAuthFlowController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFactory'])
            ->getMock();
        $controllerMock->method('getFactory')->willReturn($factoryMock);

        // Act
        $hasPendingChallenge = (new ReflectionMethod($controllerMock, 'hasPendingMultiFactorAuthChallenge'))
            ->invoke($controllerMock);

        // Assert
        $this->assertSame($expected, $hasPendingChallenge, $message);
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function pendingMultiFactorAuthChallengeDataProvider(): array
    {
        return [
            'session key set by the pre-auth handler' => [
                'user@example.com',
                true,
                'Expected a login awaiting its Multi-Factor Authentication code to count as a pending challenge.',
            ],
            'session key absent or already cleared' => [
                null,
                false,
                'Expected no pending challenge once the code is verified or the visitor never started a login.',
            ],
        ];
    }
}
