<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Yves\MultiFactorAuth\Controller;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Generated\Shared\Transfer\MultiFactorAuthValidationResponseTransfer;
use ReflectionMethod;
use Spryker\Shared\MultiFactorAuth\MultiFactorAuthConstants;
use Spryker\Yves\Kernel\View\View;
use Spryker\Yves\MultiFactorAuth\Controller\CustomerOauthMultiFactorAuthFlowController;
use Spryker\Yves\MultiFactorAuth\Dependency\Client\MultiFactorAuthToCustomerClientInterface;
use Spryker\Yves\MultiFactorAuth\Dependency\Client\MultiFactorAuthToSessionClientInterface;
use Spryker\Yves\MultiFactorAuth\MultiFactorAuthFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Yves
 * @group MultiFactorAuth
 * @group Controller
 * @group CustomerOauthMultiFactorAuthFlowControllerTest
 * Add your own group annotations below this line
 */
class CustomerOauthMultiFactorAuthFlowControllerTest extends Unit
{
    /**
     * @uses \Spryker\Yves\MultiFactorAuth\Controller\CustomerOauthMultiFactorAuthFlowController::ROUTE_CUSTOMER_OAUTH_LOGIN
     */
    protected const string ROUTE_CUSTOMER_OAUTH_LOGIN = '/multi-factor-auth/customer/login';

    /**
     * @uses \Spryker\Yves\MultiFactorAuth\Controller\CustomerOauthMultiFactorAuthFlowController::ROUTE_CUSTOMER_OVERVIEW
     */
    protected const string ROUTE_CUSTOMER_OVERVIEW = '/customer/overview';

    protected const string TYPE_SELECTION_FORM_TEMPLATE = '@MultiFactorAuth/views/customer-oauth-login-type-selection-form/customer-oauth-login-type-selection-form.twig';

    protected const string CODE_VALIDATION_FORM_TEMPLATE = '@MultiFactorAuth/views/customer-oauth-login-code-validation-form/customer-oauth-login-code-validation-form.twig';

    protected const string REDIRECT_TEMPLATE = '@MultiFactorAuth/views/customer-oauth-login-redirect/customer-oauth-login-redirect.twig';

    protected const string SOME_MULTI_FACTOR_AUTH_TYPE = 'EMAIL';

    protected const string SOME_ERROR_MESSAGE = 'multi_factor_auth.error.code_blocked';

    protected const string CODE_VALIDATION_FORM_NAME = 'multiFactorAuthCodeValidationForm';

    /**
     * @uses \Spryker\Yves\MultiFactorAuth\Controller\CustomerOauthMultiFactorAuthFlowController::ROUTE_CUSTOMER_LOGIN
     */
    protected const string ROUTE_CUSTOMER_LOGIN = '/login';

    /**
     * @uses \Spryker\Yves\MultiFactorAuth\Controller\CustomerOauthMultiFactorAuthFlowController::MESSAGE_NO_MULTI_FACTOR_AUTH_METHOD_AVAILABLE
     */
    protected const string MESSAGE_NO_METHOD_AVAILABLE = 'multi_factor_auth.error.no_method_available';

    /**
     * @uses \\Spryker\\Yves\\MultiFactorAuth\\Controller\\CustomerMultiFactorAuthFlowController::MULTI_FACTOR_AUTH_LOGIN_CUSTOMER_EMAIL_SESSION_KEY
     */
    protected const string MULTI_FACTOR_AUTH_LOGIN_EMAIL_SESSION_KEY = '_multi_factor_auth_login_customer_email';

    public function testGetCustomerOauthLoginEnabledTypesActionDelegatesToGetEnabledTypesAction(): void
    {
        // Arrange
        $request = new Request();
        $expectedView = new View([], []);

        $controllerMock = $this->getMockBuilder(CustomerOauthMultiFactorAuthFlowController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getEnabledTypesAction', 'hasPendingMultiFactorAuthChallenge', 'hasUsableMultiFactorAuthMethod'])
            ->getMock();

        $controllerMock->method('hasPendingMultiFactorAuthChallenge')->willReturn(true);
        $controllerMock->method('hasUsableMultiFactorAuthMethod')->willReturn(true);
        $controllerMock->expects($this->once())
            ->method('getEnabledTypesAction')
            ->with($request)
            ->willReturn($expectedView);

        // Act
        $view = $controllerMock->getCustomerOauthLoginEnabledTypesAction($request);

        // Assert
        $this->assertSame($expectedView, $view, 'Expected the OAuth action to delegate to the shared enabled-types action.');
    }

    public function testGetCustomerOauthLoginEnabledTypesActionRedirectsAwayWhenNoChallengeIsPending(): void
    {
        // Arrange
        $controllerMock = $this->getMockBuilder(CustomerOauthMultiFactorAuthFlowController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getEnabledTypesAction', 'hasPendingMultiFactorAuthChallenge', 'resolveNoPendingChallengeRedirectUrl'])
            ->getMock();

        $controllerMock->method('hasPendingMultiFactorAuthChallenge')->willReturn(false);
        $controllerMock->method('resolveNoPendingChallengeRedirectUrl')->willReturn(static::ROUTE_CUSTOMER_OVERVIEW);
        $controllerMock->expects($this->never())->method('getEnabledTypesAction');

        // Act
        $response = $controllerMock->getCustomerOauthLoginEnabledTypesAction(new Request());

        // Assert
        $this->assertInstanceOf(
            RedirectResponse::class,
            $response,
            'Expected the challenge page to redirect away when no Multi-Factor Authentication challenge is outstanding.',
        );
        $this->assertSame(static::ROUTE_CUSTOMER_OVERVIEW, $response->getTargetUrl());
    }

    public function testGetCustomerOauthLoginEnabledTypesActionRedirectsToLoginWhenNoMethodIsUsable(): void
    {
        // Arrange
        $controllerMock = $this->getMockBuilder(CustomerOauthMultiFactorAuthFlowController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getEnabledTypesAction', 'hasPendingMultiFactorAuthChallenge', 'hasUsableMultiFactorAuthMethod', 'addErrorMessage'])
            ->getMock();

        $controllerMock->method('hasPendingMultiFactorAuthChallenge')->willReturn(true);
        $controllerMock->method('hasUsableMultiFactorAuthMethod')->willReturn(false);
        $controllerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with(static::MESSAGE_NO_METHOD_AVAILABLE)
            ->willReturn($controllerMock);
        $controllerMock->expects($this->never())->method('getEnabledTypesAction');

        // Act
        $response = $controllerMock->getCustomerOauthLoginEnabledTypesAction(new Request());

        // Assert
        $this->assertInstanceOf(
            RedirectResponse::class,
            $response,
            'Expected a real redirect, not a rendered redirect page: the intermediate page consumes the flash message before the login page can show it.',
        );
        $this->assertSame(static::ROUTE_CUSTOMER_LOGIN, $response->getTargetUrl());
    }

    public function testSendCustomerOauthLoginCodeActionDelegatesToSendCodeAction(): void
    {
        // Arrange
        $request = new Request();
        $form = $this->createMock(FormInterface::class);
        $expectedView = new View([], []);

        $controllerMock = $this->getMockBuilder(CustomerOauthMultiFactorAuthFlowController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['sendCodeAction'])
            ->getMock();

        $controllerMock->expects($this->once())
            ->method('sendCodeAction')
            ->with($request, static::SOME_MULTI_FACTOR_AUTH_TYPE, $form)
            ->willReturn($expectedView);

        // Act
        $view = $controllerMock->sendCustomerOauthLoginCodeAction($request, static::SOME_MULTI_FACTOR_AUTH_TYPE, $form);

        // Assert
        $this->assertSame($expectedView, $view, 'Expected the OAuth action to delegate to the shared send-code action.');
    }

    public function testGetTypeSelectionFormTemplateReturnsOauthTemplate(): void
    {
        // Arrange
        $controller = $this->getMockBuilder(CustomerOauthMultiFactorAuthFlowController::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        // Act
        $template = (new ReflectionMethod($controller, 'getTypeSelectionFormTemplate'))->invoke($controller);

        // Assert
        $this->assertSame(
            static::TYPE_SELECTION_FORM_TEMPLATE,
            $template,
            'Expected the OAuth flow to render its own type-selection template.',
        );
    }

    public function testGetCodeValidationFormTemplateReturnsOauthTemplate(): void
    {
        // Arrange
        $controller = $this->getMockBuilder(CustomerOauthMultiFactorAuthFlowController::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        // Act
        $template = (new ReflectionMethod($controller, 'getCodeValidationFormTemplate'))->invoke($controller);

        // Assert
        $this->assertSame(
            static::CODE_VALIDATION_FORM_TEMPLATE,
            $template,
            'Expected the OAuth flow to render its own code-validation template.',
        );
    }

    public function testCreateCodeVerifiedViewRedirectsToCustomerOverview(): void
    {
        // Arrange
        $expectedView = new View([], []);
        $controllerMock = $this->getMockBuilder(CustomerOauthMultiFactorAuthFlowController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['view'])
            ->getMock();

        $controllerMock->expects($this->once())
            ->method('view')
            ->with(['redirectUrl' => static::ROUTE_CUSTOMER_OVERVIEW], [], static::REDIRECT_TEMPLATE)
            ->willReturn($expectedView);

        // Act
        $view = (new ReflectionMethod($controllerMock, 'createCodeVerifiedView'))
            ->invoke($controllerMock, new CustomerTransfer());

        // Assert
        $this->assertSame($expectedView, $view, 'Expected a verified code to redirect the customer to the account overview.');
    }

    public function testCreateCodeBlockedViewAddsErrorMessageAndRedirectsBackToOauthLogin(): void
    {
        // Arrange
        $expectedView = new View([], []);
        $controllerMock = $this->getMockBuilder(CustomerOauthMultiFactorAuthFlowController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['view', 'addErrorMessage'])
            ->getMock();

        $controllerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with(static::SOME_ERROR_MESSAGE)
            ->willReturn($controllerMock);

        $controllerMock->expects($this->once())
            ->method('view')
            ->with(['redirectUrl' => static::ROUTE_CUSTOMER_OAUTH_LOGIN], [], static::REDIRECT_TEMPLATE)
            ->willReturn($expectedView);

        // Act
        $view = (new ReflectionMethod($controllerMock, 'createCodeBlockedView'))
            ->invoke($controllerMock, static::SOME_ERROR_MESSAGE);

        // Assert
        $this->assertSame($expectedView, $view, 'Expected a blocked code to send the customer back to the OAuth Multi-Factor Authentication step.');
    }

    public function testExecuteCodeValidationRendersOauthRedirectWhenCodeIsVerified(): void
    {
        // Arrange
        $customerTransfer = new CustomerTransfer();
        $controllerMock = $this->getMockBuilder(CustomerOauthMultiFactorAuthFlowController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['validateCode', 'isSelectedTypeVerificationRequired', 'executePostLoginMultiFactorAuthenticationPlugins'])
            ->getMock();

        $controllerMock->method('validateCode')->willReturn(
            (new MultiFactorAuthValidationResponseTransfer())->setStatus(MultiFactorAuthConstants::CODE_VERIFIED),
        );
        $controllerMock->method('isSelectedTypeVerificationRequired')->willReturn(false);
        $controllerMock->expects($this->once())
            ->method('executePostLoginMultiFactorAuthenticationPlugins')
            ->with($customerTransfer);

        // Act
        $view = (new ReflectionMethod($controllerMock, 'executeCodeValidation'))
            ->invoke($controllerMock, new Request(), $this->createCodeValidationFormMock(), $customerTransfer);

        // Assert
        $this->assertSame(
            static::REDIRECT_TEMPLATE,
            $view->getTemplate(),
            'Expected a verified code to render the OAuth redirect template instead of the base response template.',
        );
        $this->assertSame(
            static::ROUTE_CUSTOMER_OVERVIEW,
            $view->getData()['redirectUrl'] ?? null,
            'Expected a verified code to redirect the customer to the account overview.',
        );
    }

    public function testExecuteCodeValidationRendersOauthRedirectWhenCodeIsBlocked(): void
    {
        // Arrange
        $controllerMock = $this->getMockBuilder(CustomerOauthMultiFactorAuthFlowController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['validateCode', 'executePostLoginMultiFactorAuthenticationPlugins', 'addErrorMessage'])
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

        // Act
        $view = (new ReflectionMethod($controllerMock, 'executeCodeValidation'))
            ->invoke($controllerMock, new Request(), $this->createCodeValidationFormMock(), new CustomerTransfer());

        // Assert
        $this->assertSame(
            static::REDIRECT_TEMPLATE,
            $view->getTemplate(),
            'Expected a blocked code to render the OAuth redirect template instead of the base response template.',
        );
        $this->assertSame(
            static::ROUTE_CUSTOMER_OAUTH_LOGIN,
            $view->getData()['redirectUrl'] ?? null,
            'Expected a blocked code to send the customer back to the OAuth Multi-Factor Authentication step.',
        );
    }

    protected function createCodeValidationFormMock(): FormInterface
    {
        $formMock = $this->createMock(FormInterface::class);
        $formMock->method('getName')->willReturn(static::CODE_VALIDATION_FORM_NAME);
        $formMock->method('getData')->willReturn([MultiFactorAuthTransfer::TYPE => static::SOME_MULTI_FACTOR_AUTH_TYPE]);

        return $formMock;
    }

    public function testResolveNoPendingChallengeRedirectUrlSendsLoggedInCustomerToOverview(): void
    {
        // Arrange
        $controllerMock = $this->createControllerWithLoggedInCustomer(true);

        // Act
        $url = (new ReflectionMethod($controllerMock, 'resolveNoPendingChallengeRedirectUrl'))->invoke($controllerMock);

        // Assert
        $this->assertSame(static::ROUTE_CUSTOMER_OVERVIEW, $url);
    }

    public function testResolveNoPendingChallengeRedirectUrlSendsAnonymousVisitorToLogin(): void
    {
        // Arrange
        $controllerMock = $this->createControllerWithLoggedInCustomer(false);

        // Act
        $url = (new ReflectionMethod($controllerMock, 'resolveNoPendingChallengeRedirectUrl'))->invoke($controllerMock);

        // Assert
        $this->assertSame(static::ROUTE_CUSTOMER_LOGIN, $url);
    }

    protected function createControllerWithLoggedInCustomer(bool $isLoggedIn): CustomerOauthMultiFactorAuthFlowController
    {
        $customerClientMock = $this->createMock(MultiFactorAuthToCustomerClientInterface::class);
        $customerClientMock->method('isLoggedIn')->willReturn($isLoggedIn);

        $factoryMock = $this->createMock(MultiFactorAuthFactory::class);
        $factoryMock->method('getCustomerClient')->willReturn($customerClientMock);

        $controllerMock = $this->getMockBuilder(CustomerOauthMultiFactorAuthFlowController::class)
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
        $controllerMock = $this->getMockBuilder(CustomerOauthMultiFactorAuthFlowController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getOptions', 'isSetUpMultiFactorAuthStep'])
            ->getMock();

        $controllerMock->method('getOptions')->willReturn($options);
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
                [CustomerOauthMultiFactorAuthFlowController::TYPES => []],
                false,
                false,
                'Expected an account whose active factors cannot be offered to have no usable method.',
            ],
            'enabled types present' => [
                [CustomerOauthMultiFactorAuthFlowController::TYPES => ['email']],
                false,
                true,
                'Expected an offerable factor to count as a usable method.',
            ],
            'no enabled types during a set-up step' => [
                [CustomerOauthMultiFactorAuthFlowController::TYPES => []],
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

        $factoryMock = $this->createMock(MultiFactorAuthFactory::class);
        $factoryMock->method('getSessionClient')->willReturn($sessionClientMock);

        $controllerMock = $this->getMockBuilder(CustomerOauthMultiFactorAuthFlowController::class)
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
                'customer@example.com',
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
