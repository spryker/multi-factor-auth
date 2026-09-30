<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\MultiFactorAuth\Api\Backend\EventSubscriber;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Generated\Shared\Transfer\UserTransfer;
use Spryker\ApiPlatform\Attribute\ApiType;
use Spryker\ApiPlatform\Request\RequestAttribute;
use Spryker\Glue\MultiFactorAuth\Api\Backend\Exception\MultiFactorAuthBackendExceptionFactory;
use Spryker\Glue\MultiFactorAuth\Api\Backend\Validator\MultiFactorAuthUserCodeValidator;
use Spryker\Glue\MultiFactorAuth\MultiFactorAuthConfig;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Requires a valid `X-MFA-Code` header on write requests to the resources listed in
 * {@see MultiFactorAuthConfig::getMultiFactorAuthProtectedBackendResources()} when the acting user has an
 * activated MFA type. It is the API Platform counterpart of the legacy
 * {@see \Spryker\Glue\MultiFactorAuth\Plugin\GlueBackendApiApplication\MultiFactorAuthBackendApiRequestValidatorPlugin}.
 */
#[ApiType(types: ['backend'])]
class MultiFactorAuthProtectedResourceRequestSubscriber implements EventSubscriberInterface
{
    /**
     * @uses \Spryker\Glue\User\Api\Backend\EventSubscriber\UserIdentityRequestSubscriber::ATTRIBUTE_USER_TRANSFER
     */
    public const string ATTRIBUTE_USER_TRANSFER = 'UserTransfer';

    /**
     * @uses \Spryker\Glue\User\Api\Backend\EventSubscriber\UserIdentityRequestSubscriber::PRIORITY_AFTER_IDENTITY
     */
    protected const int PRIORITY_AFTER_USER_IDENTITY = 5;

    protected const array UNPROTECTED_METHODS = [Request::METHOD_GET, Request::METHOD_HEAD, Request::METHOD_OPTIONS];

    public function __construct(
        protected MultiFactorAuthUserCodeValidator $codeValidator,
        protected MultiFactorAuthBackendExceptionFactory $exceptionFactory,
        protected MultiFactorAuthConfig $multiFactorAuthConfig,
        protected ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory,
    ) {
    }

    /**
     * @return array<string, array<int, string|int>>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', static::PRIORITY_AFTER_USER_IDENTITY],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (in_array($request->getMethod(), static::UNPROTECTED_METHODS, true)) {
            return;
        }

        $userTransfer = $request->attributes->get(static::ATTRIBUTE_USER_TRANSFER);

        if (!$userTransfer instanceof UserTransfer || !$this->isProtectedResource($request)) {
            return;
        }

        $activeTypesCollectionTransfer = $this->codeValidator->getActiveTypes($userTransfer);

        if ($activeTypesCollectionTransfer->getMultiFactorAuthTypes()->count() === 0) {
            return;
        }

        $code = $request->headers->get(MultiFactorAuthConfig::HEADER_MULTI_FACTOR_AUTH_CODE);

        if ($code === null || $code === '') {
            throw $this->exceptionFactory->createMultiFactorAuthCodeMissingException();
        }

        $multiFactorAuthCodeTransfer = $this->codeValidator->findCode($code, $userTransfer);
        $type = $multiFactorAuthCodeTransfer->getType();

        if ($type === null || !$this->codeValidator->isTypeActivated($activeTypesCollectionTransfer, $type)) {
            throw $this->exceptionFactory->createMultiFactorAuthCodeInvalidException();
        }

        $multiFactorAuthTransfer = (new MultiFactorAuthTransfer())
            ->setType($type)
            ->setUser($userTransfer)
            ->setMultiFactorAuthCode($multiFactorAuthCodeTransfer);

        if (!$this->codeValidator->isCodeValid($code, $userTransfer, $multiFactorAuthTransfer)) {
            throw $this->exceptionFactory->createMultiFactorAuthCodeInvalidException();
        }
    }

    protected function isProtectedResource(Request $request): bool
    {
        $shortName = $this->resolveOperation($request)?->getShortName();

        return $shortName !== null
            && in_array($shortName, $this->multiFactorAuthConfig->getMultiFactorAuthProtectedBackendResources(), true);
    }

    /**
     * `_api_operation` is only set by API Platform later in the request cycle; at this priority only the
     * router attributes are available, so the operation is resolved from the metadata factory.
     */
    protected function resolveOperation(Request $request): ?Operation
    {
        $operation = $request->attributes->get(RequestAttribute::API_OPERATION);

        if ($operation instanceof Operation) {
            return $operation;
        }

        $resourceClass = $request->attributes->get(RequestAttribute::API_RESOURCE_CLASS);
        $operationName = $request->attributes->get(RequestAttribute::API_OPERATION_NAME);

        if (!is_string($resourceClass) || !is_string($operationName)) {
            return null;
        }

        return $this->resourceMetadataCollectionFactory->create($resourceClass)->getOperation($operationName);
    }
}
