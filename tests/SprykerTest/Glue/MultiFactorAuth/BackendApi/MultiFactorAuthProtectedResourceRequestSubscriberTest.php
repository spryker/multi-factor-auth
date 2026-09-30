<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerTest\Glue\MultiFactorAuth\BackendApi;

use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ArrayObject;
use Codeception\Stub;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\MultiFactorAuthCodeCriteriaTransfer;
use Generated\Shared\Transfer\MultiFactorAuthCodeTransfer;
use Generated\Shared\Transfer\MultiFactorAuthCriteriaTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTransfer;
use Generated\Shared\Transfer\MultiFactorAuthTypesCollectionTransfer;
use Generated\Shared\Transfer\MultiFactorAuthValidationRequestTransfer;
use Generated\Shared\Transfer\MultiFactorAuthValidationResponseTransfer;
use Generated\Shared\Transfer\UserTransfer;
use Spryker\ApiPlatform\Exception\GlueApiException;
use Spryker\ApiPlatform\Request\RequestAttribute;
use Spryker\Glue\MultiFactorAuth\Api\Backend\EventSubscriber\MultiFactorAuthProtectedResourceRequestSubscriber;
use Spryker\Glue\MultiFactorAuth\Api\Backend\Exception\MultiFactorAuthBackendExceptionFactory;
use Spryker\Glue\MultiFactorAuth\Api\Backend\Validator\MultiFactorAuthUserCodeValidator;
use Spryker\Glue\MultiFactorAuth\MultiFactorAuthConfig;
use Spryker\Shared\MultiFactorAuth\MultiFactorAuthConstants;
use Spryker\Zed\MultiFactorAuth\Business\MultiFactorAuthFacadeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Glue
 * @group MultiFactorAuth
 * @group BackendApi
 * @group MultiFactorAuthProtectedResourceRequestSubscriberTest
 * Add your own group annotations below this line
 */
class MultiFactorAuthProtectedResourceRequestSubscriberTest extends Unit
{
    protected const int ID_USER = 42;

    protected const string PROTECTED_RESOURCE = 'warehouse-user-assignments';

    protected const string UNPROTECTED_RESOURCE = 'categories';

    protected const string TYPE_EMAIL = 'email';

    protected const string CODE = '482913';

    protected const int ID_CODE = 7;

    protected int $codeValidationCalls = 0;

    public function testGivenAWriteToAProtectedResourceWithAValidCodeWhenHandlingRequestThenItPasses(): void
    {
        // Arrange
        $request = $this->createRequest(Request::METHOD_POST, static::PROTECTED_RESOURCE, static::CODE);
        $subscriber = $this->createSubscriber(
            [static::TYPE_EMAIL],
            $this->createStoredCode(MultiFactorAuthConstants::STATUS_PENDING_ACTIVATION),
            MultiFactorAuthConstants::CODE_VERIFIED,
        );

        // Act
        $subscriber->onKernelRequest($this->createRequestEvent($request));

        // Assert
        $this->assertSame(1, $this->codeValidationCalls);
    }

    public function testGivenAWriteToAProtectedResourceWithoutACodeWhenHandlingRequestThenItIsForbidden(): void
    {
        // Arrange
        $request = $this->createRequest(Request::METHOD_POST, static::PROTECTED_RESOURCE, null);
        $subscriber = $this->createSubscriber([static::TYPE_EMAIL], new MultiFactorAuthCodeTransfer());

        // Act
        try {
            $subscriber->onKernelRequest($this->createRequestEvent($request));
            $this->fail('Expected the request to be rejected without an X-MFA-Code header.');
        } catch (GlueApiException $glueApiException) {
            // Assert
            $this->assertSame(Response::HTTP_FORBIDDEN, $glueApiException->getStatusCode());
            $this->assertSame(MultiFactorAuthConfig::ERROR_CODE_MULTI_FACTOR_AUTH_CODE_MISSING, $glueApiException->getErrorCode());
        }
    }

    public function testGivenAWriteToAProtectedResourceWithAnUnknownCodeWhenHandlingRequestThenItIsForbidden(): void
    {
        // Arrange
        $request = $this->createRequest(Request::METHOD_POST, static::PROTECTED_RESOURCE, static::CODE);
        $subscriber = $this->createSubscriber([static::TYPE_EMAIL], new MultiFactorAuthCodeTransfer());

        // Act
        try {
            $subscriber->onKernelRequest($this->createRequestEvent($request));
            $this->fail('Expected the request to be rejected for an unknown code.');
        } catch (GlueApiException $glueApiException) {
            // Assert
            $this->assertSame(Response::HTTP_FORBIDDEN, $glueApiException->getStatusCode());
            $this->assertSame(MultiFactorAuthConfig::ERROR_CODE_MULTI_FACTOR_AUTH_CODE_INVALID, $glueApiException->getErrorCode());
        }
    }

    public function testGivenAWriteToAProtectedResourceWithARejectedCodeWhenHandlingRequestThenItIsForbidden(): void
    {
        // Arrange
        $request = $this->createRequest(Request::METHOD_POST, static::PROTECTED_RESOURCE, static::CODE);
        $subscriber = $this->createSubscriber(
            [static::TYPE_EMAIL],
            $this->createStoredCode(MultiFactorAuthConstants::STATUS_PENDING_ACTIVATION),
            MultiFactorAuthConstants::CODE_BLOCKED,
        );

        // Act
        try {
            $subscriber->onKernelRequest($this->createRequestEvent($request));
            $this->fail('Expected the request to be rejected for a blocked code.');
        } catch (GlueApiException $glueApiException) {
            // Assert
            $this->assertSame(MultiFactorAuthConfig::ERROR_CODE_MULTI_FACTOR_AUTH_CODE_INVALID, $glueApiException->getErrorCode());
        }
    }

    public function testGivenAUserWithoutAnActivatedTypeWhenWritingToAProtectedResourceThenNoCodeIsRequired(): void
    {
        // Arrange
        $request = $this->createRequest(Request::METHOD_POST, static::PROTECTED_RESOURCE, null);
        $subscriber = $this->createSubscriber([], new MultiFactorAuthCodeTransfer());

        // Act
        $subscriber->onKernelRequest($this->createRequestEvent($request));

        // Assert
        $this->assertSame(0, $this->codeValidationCalls);
    }

    public function testGivenAWriteToAnUnprotectedResourceWhenHandlingRequestThenNoCodeIsRequired(): void
    {
        // Arrange
        $request = $this->createRequest(Request::METHOD_POST, static::UNPROTECTED_RESOURCE, null);
        $subscriber = $this->createSubscriber([static::TYPE_EMAIL], new MultiFactorAuthCodeTransfer());

        // Act
        $subscriber->onKernelRequest($this->createRequestEvent($request));

        // Assert
        $this->assertSame(0, $this->codeValidationCalls);
    }

    public function testGivenAReadOfAProtectedResourceWhenHandlingRequestThenNoCodeIsRequired(): void
    {
        // Arrange
        $request = $this->createRequest(Request::METHOD_GET, static::PROTECTED_RESOURCE, null);
        $subscriber = $this->createSubscriber([static::TYPE_EMAIL], new MultiFactorAuthCodeTransfer());

        // Act
        $subscriber->onKernelRequest($this->createRequestEvent($request));

        // Assert
        $this->assertSame(0, $this->codeValidationCalls);
    }

    public function testGivenNoResolvedUserWhenHandlingRequestThenTheRequestIsLeftUntouched(): void
    {
        // Arrange
        $request = $this->createRequest(Request::METHOD_POST, static::PROTECTED_RESOURCE, null);
        $request->attributes->remove(MultiFactorAuthProtectedResourceRequestSubscriber::ATTRIBUTE_USER_TRANSFER);
        $subscriber = $this->createSubscriber([static::TYPE_EMAIL], new MultiFactorAuthCodeTransfer());

        // Act
        $subscriber->onKernelRequest($this->createRequestEvent($request));

        // Assert
        $this->assertSame(0, $this->codeValidationCalls);
    }

    /**
     * @param array<string> $activeTypes
     */
    protected function createSubscriber(
        array $activeTypes,
        MultiFactorAuthCodeTransfer $storedCodeTransfer,
        int $codeValidationStatus = MultiFactorAuthConstants::CODE_UNVERIFIED,
    ): MultiFactorAuthProtectedResourceRequestSubscriber {
        $multiFactorAuthFacade = $this->createMultiFactorAuthFacadeStub($activeTypes, $storedCodeTransfer, $codeValidationStatus);

        return new MultiFactorAuthProtectedResourceRequestSubscriber(
            new MultiFactorAuthUserCodeValidator($multiFactorAuthFacade),
            new MultiFactorAuthBackendExceptionFactory(),
            $this->createConfigStub(),
            Stub::makeEmpty(ResourceMetadataCollectionFactoryInterface::class),
        );
    }

    /**
     * @param array<string> $activeTypes
     */
    protected function createMultiFactorAuthFacadeStub(
        array $activeTypes,
        MultiFactorAuthCodeTransfer $storedCodeTransfer,
        int $codeValidationStatus,
    ): MultiFactorAuthFacadeInterface {
        return Stub::makeEmpty(MultiFactorAuthFacadeInterface::class, [
            'getUserMultiFactorAuthTypes' => function (MultiFactorAuthCriteriaTransfer $criteriaTransfer) use ($activeTypes): MultiFactorAuthTypesCollectionTransfer {
                $multiFactorAuthTransfers = [];

                foreach ($activeTypes as $type) {
                    $multiFactorAuthTransfers[] = (new MultiFactorAuthTransfer())
                        ->setType($type)
                        ->setStatus(MultiFactorAuthConstants::STATUS_ACTIVE)
                        ->setUser($criteriaTransfer->getUser());
                }

                return (new MultiFactorAuthTypesCollectionTransfer())->setMultiFactorAuthTypes(new ArrayObject($multiFactorAuthTransfers));
            },
            'findUserMultiFactorAuthType' => function (MultiFactorAuthCodeCriteriaTransfer $criteriaTransfer) use ($storedCodeTransfer): MultiFactorAuthCodeTransfer {
                return $criteriaTransfer->getCode() === $storedCodeTransfer->getCode() ? $storedCodeTransfer : new MultiFactorAuthCodeTransfer();
            },
            'validateUserCode' => function (MultiFactorAuthTransfer $multiFactorAuthTransfer) use ($codeValidationStatus): MultiFactorAuthValidationResponseTransfer {
                $this->codeValidationCalls++;

                return (new MultiFactorAuthValidationResponseTransfer())->setStatus($codeValidationStatus);
            },
            'validateUserMultiFactorAuthStatus' => function (MultiFactorAuthValidationRequestTransfer $validationRequestTransfer): MultiFactorAuthValidationResponseTransfer {
                $this->codeValidationCalls++;

                return (new MultiFactorAuthValidationResponseTransfer())->setIsRequired(false);
            },
        ]);
    }

    protected function createConfigStub(): MultiFactorAuthConfig
    {
        return Stub::make(MultiFactorAuthConfig::class, [
            'getMultiFactorAuthProtectedBackendResources' => [static::PROTECTED_RESOURCE],
        ]);
    }

    protected function createStoredCode(int $status): MultiFactorAuthCodeTransfer
    {
        return (new MultiFactorAuthCodeTransfer())
            ->setIdCode(static::ID_CODE)
            ->setCode(static::CODE)
            ->setType(static::TYPE_EMAIL)
            ->setStatus($status);
    }

    protected function createRequest(string $method, string $resourceShortName, ?string $code): Request
    {
        $request = Request::create('/' . $resourceShortName, $method);
        $request->attributes->set(RequestAttribute::API_OPERATION, (new Post())->withShortName($resourceShortName));
        $request->attributes->set(
            MultiFactorAuthProtectedResourceRequestSubscriber::ATTRIBUTE_USER_TRANSFER,
            (new UserTransfer())->setIdUser(static::ID_USER),
        );

        if ($code !== null) {
            $request->headers->set(MultiFactorAuthConfig::HEADER_MULTI_FACTOR_AUTH_CODE, $code);
        }

        return $request;
    }

    protected function createRequestEvent(Request $request): RequestEvent
    {
        return new RequestEvent(
            Stub::makeEmpty(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        );
    }
}
