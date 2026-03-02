<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Yves\MultiFactorAuth\Controller;

use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Generated\Shared\Transfer\MultiFactorAuthValidationResponseTransfer;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Shared\MultiFactorAuth\MultiFactorAuthConstants;
use Spryker\Yves\Kernel\Controller\AbstractController;
use Spryker\Yves\Kernel\View\View;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

/**
 * @method \Spryker\Yves\MultiFactorAuth\MultiFactorAuthFactory getFactory()
 */
abstract class AbstractMultiFactorAuthController extends AbstractController
{
    /**
     * @var string
     */
    public const IS_ACTIVATION = 'is_activation';

    /**
     * @var string
     */
    public const IS_DEACTIVATION = 'is_deactivation';

    /**
     * @var string
     */
    public const TYPE_TO_SET_UP = 'type_to_set_up';

    /**
     * @var string
     */
    public const TYPES = 'types';

    /**
     * @var string
     */
    public const CODE_LENGTH = 'code_length';

    /**
     * @var string
     */
    public const AUTHENTICATION_CODE = 'authentication_code';

    /**
     * @var string
     */
    protected const MESSAGE_REQUIRED_SELECTION_ERROR = 'multi_factor_auth.selection.error.required';

    /**
     * @var string
     */
    protected const MESSAGE_CORRUPTED_CODE_ERROR = 'multi_factor_auth.error.corrupted_code';

    /**
     * @var string
     */
    protected const MESSAGE_SENDING_CODE_ERROR = 'multi_factor_auth.send_code.error';

    /**
     * @var string
     */
    protected const DATA_ERROR_PARAMETER = 'data-error';

    /**
     * @var string
     */
    protected const DATA_SUCCESS_PARAMETER = 'data-success';

    public function getEnabledTypesAction(Request $request): View
    {
        $options = $this->getOptions($request);

        if ($this->assertNoTypesEnabled($options) && $this->isSetUpMultiFactorAuthStep($request) === true) {
            return $this->sendCodeAction($request, $this->getParameterFromRequest($request, static::TYPE_TO_SET_UP));
        }

        if ($this->assertOneTypeEnabled($options)) {
            return $this->sendCodeAction($request, $options[static::TYPES][0]);
        }

        $typeSelectionFormName = $this->getFactory()->getTypeSelectionForm([])->getName();
        if ($this->isSetUpMultiFactorAuthStep($request, $typeSelectionFormName) === true) {
            $options = array_merge($options, $this->extractSetupParameters($request, $typeSelectionFormName));
        }

        $typeSelectionForm = $this->getFactory()
            ->getTypeSelectionForm($options)
            ->handleRequest($request);

        if ($typeSelectionForm->isSubmitted()) {
            $selectedType = $this->getParameterFromRequest($request, MultiFactorAuthTransfer::TYPE, $typeSelectionFormName);

            if ($selectedType === null) {
                $typeSelectionFormView = $this->getFactory()
                    ->getTypeSelectionForm($this->getOptions($request))
                    ->handleRequest($request)
                    ->addError(new FormError(static::MESSAGE_REQUIRED_SELECTION_ERROR))
                    ->createView();

                return $this->view(['form' => $typeSelectionFormView], [], $this->getTypeSelectionFormTemplate());
            }

            return $this->sendCodeAction($request, $selectedType, $typeSelectionForm);
        }

        return $this->view(['form' => $typeSelectionForm->createView()], [], $this->getTypeSelectionFormTemplate());
    }

    public function sendCodeAction(Request $request, ?string $multiFactorAuthType = null, ?FormInterface $form = null): View
    {
        $formName = $form?->getName() ?? $this->getFactory()->getCodeValidationForm()->getName();
        $multiFactorAuthType = $multiFactorAuthType ?? $this->getParameterFromRequest($request, MultiFactorAuthTransfer::TYPE, $formName);
        $identityTransfer = $this->getIdentity();
        $options = array_merge([
            static::TYPES => [$multiFactorAuthType],
            static::CODE_LENGTH => $this->resolveCodeLength(),
        ], $this->extractSetupParameters($request, $formName));

        $codeValidationForm = $this->getFactory()
            ->getCodeValidationForm($options)
            ->handleRequest($request);

        if ($codeValidationForm->isSubmitted() === false) {
            try {
                $this->sendCode($multiFactorAuthType, $identityTransfer, $request);

                return $this->view(['form' => $codeValidationForm->createView()], [], $this->getCodeValidationFormTemplate());
            } catch (Throwable $e) {
                return $this->view([
                    'form' => $codeValidationForm->addError(new FormError(static::MESSAGE_SENDING_CODE_ERROR))
                        ->createView(),
                ], [], $this->getCodeValidationFormTemplate());
            }
        }

        return $this->executeCodeValidation($request, $codeValidationForm, $identityTransfer);
    }

    protected function executeCodeValidation(
        Request $request,
        FormInterface $codeValidationForm,
        AbstractTransfer $identityTransfer
    ): View {
        $multiFactorAuthType = $codeValidationForm->getData()[MultiFactorAuthTransfer::TYPE];
        $multiFactorAuthValidationResponseTransfer = $this->validateCode($identityTransfer, $codeValidationForm);

        if ($multiFactorAuthValidationResponseTransfer->getStatus() === MultiFactorAuthConstants::CODE_VERIFIED) {
            if ($this->isSelectedTypeVerificationRequired($request, $multiFactorAuthType, $codeValidationForm->getName())) {
                $request->request->set(MultiFactorAuthTransfer::TYPE, $codeValidationForm->getData()[static::TYPE_TO_SET_UP]);
                $request->request->remove($codeValidationForm->getName());

                return $this->sendCodeAction($request, null, $codeValidationForm);
            }

            $this->executePostLoginMultiFactorAuthenticationPlugins($identityTransfer);

            return $this->view(['dataResult' => static::DATA_SUCCESS_PARAMETER], [], '@MultiFactorAuth/views/response/response.twig');
        }

        if ($multiFactorAuthValidationResponseTransfer->getStatus() === MultiFactorAuthConstants::CODE_BLOCKED) {
            $this->addErrorMessage($multiFactorAuthValidationResponseTransfer->getMessageOrFail());

            return $this->view(['dataResult' => static::DATA_ERROR_PARAMETER], [], '@MultiFactorAuth/views/response/response.twig');
        }

        $codeValidationForm->addError(new FormError($multiFactorAuthValidationResponseTransfer->getMessageOrFail()));

        return $this->view(['form' => $codeValidationForm->createView()], [], $this->getCodeValidationFormTemplate());
    }

    abstract protected function getTypeSelectionFormTemplate(): string;

    abstract protected function getCodeValidationFormTemplate(): string;

    abstract protected function sendCode(string $multiFactorAuthType, AbstractTransfer $transfer, Request $request): void;

    abstract protected function validateCode(AbstractTransfer $identityTransfer, FormInterface $codeValidationForm): MultiFactorAuthValidationResponseTransfer;

    abstract protected function getIdentity(): AbstractTransfer;

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array<string, mixed>
     */
    abstract protected function getOptions(Request $request): array;

    abstract protected function executePostLoginMultiFactorAuthenticationPlugins(AbstractTransfer $identityTransfer): void;

    abstract protected function resolveCodeLength(): int;

    protected function isSetUpMultiFactorAuthStep(Request $request, ?string $formName = null): bool
    {
        return $this->assertIsActivation($request, $formName) || $this->assertIsDeactivation($request, $formName);
    }

    protected function isSelectedTypeVerificationRequired(Request $request, string $multiFactorAuthType, ?string $formName = null): bool
    {
        return $this->assertIsActivation($request, $formName) && $this->getParameterFromRequest($request, static::TYPE_TO_SET_UP, $formName) !== $multiFactorAuthType;
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return bool
     */
    protected function assertNoTypesEnabled(array $options): bool
    {
        return $options[static::TYPES] === [];
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return bool
     */
    protected function assertOneTypeEnabled(array $options): bool
    {
        return count($options[static::TYPES]) === 1;
    }

    protected function assertIsActivation(Request $request, ?string $formName = null): ?string
    {
        return $this->getParameterFromRequest($request, static::IS_ACTIVATION, $formName);
    }

    protected function assertIsDeactivation(Request $request, ?string $formName = null): ?string
    {
        return $this->getParameterFromRequest($request, static::IS_DEACTIVATION, $formName);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string|null $formName
     *
     * @return array<string, mixed>
     */
    protected function extractSetupParameters(Request $request, ?string $formName = null): array
    {
        return [
            static::IS_ACTIVATION => $this->assertIsActivation($request, $formName),
            static::IS_DEACTIVATION => $this->assertIsDeactivation($request, $formName),
            static::TYPE_TO_SET_UP => $this->getParameterFromRequest($request, static::TYPE_TO_SET_UP, $formName),
        ];
    }

    protected function getParameterFromRequest(Request $request, string $parameter, ?string $formName = null): mixed
    {
        return $this->getFactory()->createRequestReader()->get($request, $parameter, $formName);
    }
}
