<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Tests\Unit\Model;

use MatheusGontijo\SystemConfigHistory\Model\RequestStateRegistry;
use MatheusGontijo\SystemConfigHistory\Model\SystemConfigRepositoryDecorationProcess;
use MatheusGontijo\SystemConfigHistory\Repository\Model\SystemConfigRepositoryDecorationProcessRepository;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Api\Context\AdminApiSource;
use Shopware\Core\Framework\Api\Context\SalesChannelApiSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\PlatformRequest;
use Shopware\Core\System\User\UserEntity;
use Symfony\Component\HttpFoundation\Request;

class SystemConfigRepositoryDecorationProcessUnitTest extends TestCase
{
    public function testIsDisabled(): void
    {
        $systemConfigRepositoryDecorationProcessRepositoryMock = $this->createMock(
            SystemConfigRepositoryDecorationProcessRepository::class
        );
        $requestStateRegistryMock = $this->createMock(RequestStateRegistry::class);

        // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
        $callMock = fn (...$args) => $this->createMock(EntityWrittenContainerEvent::class);

        $systemConfigRepositoryDecorationProcessRepositoryMock->expects(static::exactly(1))
            ->method('isEnabled')
            ->willReturn(false);

        $systemConfigRepositoryDecorationProcessRepositoryMock->expects(static::never())
            ->method('generateId');

        $systemConfigRepositoryDecorationProcessRepositoryMock->expects(static::never())
            ->method('getValue');

        $systemConfigRepositoryDecorationProcessRepositoryMock->expects(static::never())
            ->method('insert');

        $requestStateRegistryMock->expects(static::never())
            ->method(static::anything());

        $systemConfigServiceDecoration = new SystemConfigRepositoryDecorationProcess(
            $systemConfigRepositoryDecorationProcessRepositoryMock,
            $requestStateRegistryMock
        );

        $systemConfigServiceDecoration->process($callMock, [
            [
                'id' => 'c6316df22e754fe1af0eae305fd3a495',
                'configurationKey' => 'my.custom.systemConfig1',
                'configurationValue' => ['_value' => 'aaa'],
                'salesChannelId' => null,
            ],
            [
                'id' => '1c957ed20cef4410ad1a6150079ab9f7',
                'configurationKey' => 'my.custom.systemConfig2',
                'configurationValue' => ['_value' => 'bbb'],
                'salesChannelId' => null,
            ],
        ]);
    }

    public function testEqualValues(): void
    {
        $systemConfigRepositoryDecorationProcessRepositoryMock = $this->createMock(
            SystemConfigRepositoryDecorationProcessRepository::class
        );
        $requestStateRegistryMock = $this->createMock(RequestStateRegistry::class);

        // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
        $callMock = fn (...$args) => $this->createMock(EntityWrittenContainerEvent::class);

        $systemConfigRepositoryDecorationProcessRepositoryMock->expects(static::exactly(1))
            ->method('isEnabled')
            ->willReturn(true);

        $systemConfigRepositoryDecorationProcessRepositoryMock->expects(static::exactly(4))
            ->method('getValue')
            ->withConsecutive(
                ['my.custom.systemConfig1', null],
                ['my.custom.systemConfig2', null],
                ['my.custom.systemConfig1', null],
                ['my.custom.systemConfig2', null]
            )
            ->willReturnOnConsecutiveCalls(
                ['_value' => 'aaa'],
                ['_value' => 'bbb'],
                ['_value' => 'aaa'],
                ['_value' => 'bbb']
            );

        $requestStateRegistryMock->expects(static::never())
            ->method(static::anything());

        $systemConfigServiceDecoration = new SystemConfigRepositoryDecorationProcess(
            $systemConfigRepositoryDecorationProcessRepositoryMock,
            $requestStateRegistryMock
        );

        $systemConfigServiceDecoration->process($callMock, [
            [
                'id' => 'c6316df22e754fe1af0eae305fd3a495',
                'configurationKey' => 'my.custom.systemConfig1',
                'configurationValue' => ['_value' => 'aaa'],
                'salesChannelId' => null,
            ],
            [
                'id' => '1c957ed20cef4410ad1a6150079ab9f7',
                'configurationKey' => 'my.custom.systemConfig2',
                'configurationValue' => ['_value' => 'bbb'],
                'salesChannelId' => null,
            ],
        ]);
    }

    public function testMixedDifferentAndEqualValues(): void
    {
        $systemConfigRepositoryDecorationProcessRepositoryMock = $this->createMock(
            SystemConfigRepositoryDecorationProcessRepository::class
        );
        $requestStateRegistryMock = $this->createMock(RequestStateRegistry::class);

        // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
        $callMock = fn (...$args) => $this->createMock(EntityWrittenContainerEvent::class);

        $systemConfigRepositoryDecorationProcessRepositoryMock->expects(static::exactly(1))
            ->method('isEnabled')
            ->willReturn(true);

        $systemConfigRepositoryDecorationProcessRepositoryMock->expects(static::exactly(6))
            ->method('getValue')
            ->withConsecutive(
                ['my.custom.systemConfig1', null],
                ['my.custom.systemConfig2', null],
                ['my.custom.systemConfig3', null],
                ['my.custom.systemConfig1', null],
                ['my.custom.systemConfig2', null],
                ['my.custom.systemConfig3', null]
            )
            ->willReturnOnConsecutiveCalls(
                ['_value' => 'aaa'],
                ['_value' => 'bbb'],
                ['_value' => 'ccc'],
                ['_value' => 'bbb'],
                ['_value' => 'bbb'],
                ['_value' => 'zzz']
            );

        $requestStateRegistryMock->expects(static::exactly(2))
            ->method('getRequest')
            ->willReturn(null);

        $systemConfigServiceDecoration = new SystemConfigRepositoryDecorationProcess(
            $systemConfigRepositoryDecorationProcessRepositoryMock,
            $requestStateRegistryMock
        );

        $systemConfigServiceDecoration->process($callMock, [
            [
                'id' => 'c6316df22e754fe1af0eae305fd3a495',
                'configurationKey' => 'my.custom.systemConfig1',
                'configurationValue' => ['_value' => 'aaa'],
                'salesChannelId' => null,
            ],
            [
                'id' => '1c957ed20cef4410ad1a6150079ab9f7',
                'configurationKey' => 'my.custom.systemConfig2',
                'configurationValue' => ['_value' => 'bbb'],
                'salesChannelId' => null,
            ],
            [
                'id' => '4191798f32f045b78e116097e1ac6ed3',
                'configurationKey' => 'my.custom.systemConfig3',
                'configurationValue' => ['_value' => 'ccc'],
                'salesChannelId' => null,
            ],
        ]);
    }

    public function testDifferentValuesWithoutAdminRequest(): void
    {
        $systemConfigRepositoryDecorationProcessRepositoryMock = $this->createMock(
            SystemConfigRepositoryDecorationProcessRepository::class
        );
        $requestStateRegistryMock = $this->createMock(RequestStateRegistry::class);

        // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
        $callMock = fn (...$args) => $this->createMock(EntityWrittenContainerEvent::class);

        $systemConfigRepositoryDecorationProcessRepositoryMock->expects(static::exactly(1))
            ->method('isEnabled')
            ->willReturn(true);

        $systemConfigRepositoryDecorationProcessRepositoryMock->expects(static::exactly(4))
            ->method('getValue')
            ->withConsecutive(
                ['my.custom.systemConfig1', null],
                ['my.custom.systemConfig2', null],
                ['my.custom.systemConfig1', null],
                ['my.custom.systemConfig2', null]
            )
            ->willReturnOnConsecutiveCalls(
                ['_value' => 'aaa'],
                ['_value' => 'bbb'],
                ['_value' => 'bbb'],
                ['_value' => 'aaa']
            );

        $requestStateRegistryMock->expects(static::exactly(2))
            ->method('getRequest')
            ->willReturn(null);

        $systemConfigRepositoryDecorationProcessRepositoryMock->expects(static::exactly(2))
            ->method('generateId')
            ->willReturnOnConsecutiveCalls('c6316df22e754fe1af0eae305fd3a495', '1c957ed20cef4410ad1a6150079ab9f7');

        $systemConfigRepositoryDecorationProcessRepositoryMock->expects(static::exactly(1))
            ->method('insert')
            ->withConsecutive([
                [
                    [
                        'id' => 'c6316df22e754fe1af0eae305fd3a495',
                        'configurationKey' => 'my.custom.systemConfig1',
                        'configurationValueOld' => ['_value' => 'aaa'],
                        'configurationValueNew' => ['_value' => 'bbb'],
                        'salesChannelId' => null,
                    ],
                    [
                        'id' => '1c957ed20cef4410ad1a6150079ab9f7',
                        'configurationKey' => 'my.custom.systemConfig2',
                        'configurationValueOld' => ['_value' => 'bbb'],
                        'configurationValueNew' => ['_value' => 'aaa'],
                        'salesChannelId' => null,
                    ],
                ],
            ]);

        $systemConfigServiceDecoration = new SystemConfigRepositoryDecorationProcess(
            $systemConfigRepositoryDecorationProcessRepositoryMock,
            $requestStateRegistryMock
        );

        $systemConfigServiceDecoration->process($callMock, [
            [
                'id' => 'c6316df22e754fe1af0eae305fd3a495',
                'configurationKey' => 'my.custom.systemConfig1',
                'configurationValue' => ['_value' => 'aaa'],
                'salesChannelId' => null,
            ],
            [
                'id' => '1c957ed20cef4410ad1a6150079ab9f7',
                'configurationKey' => 'my.custom.systemConfig2',
                'configurationValue' => ['_value' => 'bbb'],
                'salesChannelId' => null,
            ],
        ]);
    }

    public function testDifferentValuesWithAdminRequest(): void
    {
        $systemConfigRepositoryDecorationProcessRepositoryMock = $this->createMock(
            SystemConfigRepositoryDecorationProcessRepository::class
        );
        $requestStateRegistryMock = $this->createMock(RequestStateRegistry::class);

        // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
        $callMock = fn (...$args) => $this->createMock(EntityWrittenContainerEvent::class);

        $systemConfigRepositoryDecorationProcessRepositoryMock->expects(static::exactly(1))
            ->method('isEnabled')
            ->willReturn(true);

        $systemConfigRepositoryDecorationProcessRepositoryMock->expects(static::exactly(4))
            ->method('getValue')
            ->withConsecutive(
                ['my.custom.systemConfig1', null],
                ['my.custom.systemConfig2', null],
                ['my.custom.systemConfig1', null],
                ['my.custom.systemConfig2', null]
            )
            ->willReturnOnConsecutiveCalls(
                ['_value' => 'aaa'],
                ['_value' => 'bbb'],
                ['_value' => 'bbb'],
                ['_value' => 'aaa']
            );

        $request = Request::create('http://localhost', 'POST', [], [], [], []);

        $context = new Context(new AdminApiSource('72e7593c3a374ddc9c864abdf31dc766'));

        $request->attributes->set(PlatformRequest::ATTRIBUTE_CONTEXT_OBJECT, $context);

        $requestStateRegistryMock->method('getRequest')
            ->willReturn($request);

        $userEntity = new UserEntity();
        $userEntity->setUsername('johndoe');
        $userEntity->setFirstName('John');
        $userEntity->setLastName('Doe');
        $userEntity->setEmail('johndoe@example.com');
        $userEntity->setActive(true);

        $systemConfigRepositoryDecorationProcessRepositoryMock->expects(static::exactly(1))
            ->method('loadUser')
            ->willReturn($userEntity);

        $systemConfigRepositoryDecorationProcessRepositoryMock->expects(static::exactly(2))
            ->method('generateId')
            ->willReturnOnConsecutiveCalls('c6316df22e754fe1af0eae305fd3a495', '1c957ed20cef4410ad1a6150079ab9f7');

        $systemConfigRepositoryDecorationProcessRepositoryMock->expects(static::exactly(1))
            ->method('insert')
            ->withConsecutive([
                [
                    [
                        'id' => 'c6316df22e754fe1af0eae305fd3a495',
                        'configurationKey' => 'my.custom.systemConfig1',
                        'configurationValueOld' => ['_value' => 'aaa'],
                        'configurationValueNew' => ['_value' => 'bbb'],
                        'salesChannelId' => null,
                        'username' => 'johndoe',
                    ],
                    [
                        'id' => '1c957ed20cef4410ad1a6150079ab9f7',
                        'configurationKey' => 'my.custom.systemConfig2',
                        'configurationValueOld' => ['_value' => 'bbb'],
                        'configurationValueNew' => ['_value' => 'aaa'],
                        'salesChannelId' => null,
                        'username' => 'johndoe',
                    ],
                ],
            ]);

        $systemConfigServiceDecoration = new SystemConfigRepositoryDecorationProcess(
            $systemConfigRepositoryDecorationProcessRepositoryMock,
            $requestStateRegistryMock
        );

        $systemConfigServiceDecoration->process($callMock, [
            [
                'id' => 'c6316df22e754fe1af0eae305fd3a495',
                'configurationKey' => 'my.custom.systemConfig1',
                'configurationValue' => ['_value' => 'aaa'],
                'salesChannelId' => null,
            ],
            [
                'id' => '1c957ed20cef4410ad1a6150079ab9f7',
                'configurationKey' => 'my.custom.systemConfig2',
                'configurationValue' => ['_value' => 'bbb'],
                'salesChannelId' => null,
            ],
        ]);
    }

    public function testWithoutRequestAttributeContextObject(): void
    {
        $systemConfigRepositoryDecorationProcessRepositoryMock = $this->createMock(
            SystemConfigRepositoryDecorationProcessRepository::class
        );
        $requestStateRegistryMock = $this->createMock(RequestStateRegistry::class);

        // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
        $callMock = fn (...$args) => $this->createMock(EntityWrittenContainerEvent::class);

        $systemConfigRepositoryDecorationProcessRepositoryMock->expects(static::exactly(1))
            ->method('isEnabled')
            ->willReturn(true);

        $systemConfigRepositoryDecorationProcessRepositoryMock->expects(static::exactly(2))
            ->method('getValue')
            ->withConsecutive(['my.custom.systemConfig1', null])
            ->willReturnOnConsecutiveCalls(
                ['_value' => 'aaa'],
                ['_value' => 'bbb']
            );

        $request = Request::create('http://localhost', 'POST', [], [], [], []);

        $requestStateRegistryMock->method('getRequest')
            ->willReturn($request);

        $systemConfigServiceDecoration = new SystemConfigRepositoryDecorationProcess(
            $systemConfigRepositoryDecorationProcessRepositoryMock,
            $requestStateRegistryMock
        );

        $systemConfigServiceDecoration->process($callMock, [
            [
                'id' => 'c6316df22e754fe1af0eae305fd3a495',
                'configurationKey' => 'my.custom.systemConfig1',
                'configurationValue' => ['_value' => 'aaa'],
                'salesChannelId' => null,
            ],
        ]);
    }

    public function testWithoutRequestWithoutAdminApiSource(): void
    {
        $systemConfigRepositoryDecorationProcessRepositoryMock = $this->createMock(
            SystemConfigRepositoryDecorationProcessRepository::class
        );
        $requestStateRegistryMock = $this->createMock(RequestStateRegistry::class);

        // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
        $callMock = fn (...$args) => $this->createMock(EntityWrittenContainerEvent::class);

        $systemConfigRepositoryDecorationProcessRepositoryMock->expects(static::exactly(1))
            ->method('isEnabled')
            ->willReturn(true);

        $systemConfigRepositoryDecorationProcessRepositoryMock->expects(static::exactly(2))
            ->method('getValue')
            ->withConsecutive(['my.custom.systemConfig1', null])
            ->willReturnOnConsecutiveCalls(
                ['_value' => 'aaa'],
                ['_value' => 'bbb']
            );

        $request = Request::create('http://localhost', 'POST', [], [], [], []);

        $context = new Context(new SalesChannelApiSource('72e7593c3a374ddc9c864abdf31dc766'));

        $request->attributes->set(PlatformRequest::ATTRIBUTE_CONTEXT_OBJECT, $context);

        $requestStateRegistryMock->method('getRequest')
            ->willReturn($request);

        $systemConfigServiceDecoration = new SystemConfigRepositoryDecorationProcess(
            $systemConfigRepositoryDecorationProcessRepositoryMock,
            $requestStateRegistryMock
        );

        $systemConfigServiceDecoration->process($callMock, [
            [
                'id' => 'c6316df22e754fe1af0eae305fd3a495',
                'configurationKey' => 'my.custom.systemConfig1',
                'configurationValue' => ['_value' => 'aaa'],
                'salesChannelId' => null,
            ],
        ]);
    }

    public function testInvalidValues(): void
    {
        $systemConfigRepositoryDecorationProcessRepositoryMock = $this->createMock(
            SystemConfigRepositoryDecorationProcessRepository::class
        );
        $requestStateRegistryMock = $this->createMock(RequestStateRegistry::class);

        // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
        $callMock = fn (...$args) => $this->createMock(EntityWrittenContainerEvent::class);

        $systemConfigRepositoryDecorationProcessRepositoryMock->expects(static::exactly(1))
            ->method('isEnabled')
            ->willReturn(true);

        $systemConfigRepositoryDecorationProcessRepositoryMock->expects(static::never())
            ->method('generateId');

        $systemConfigRepositoryDecorationProcessRepositoryMock->expects(static::never())
            ->method('getValue');

        $systemConfigRepositoryDecorationProcessRepositoryMock->expects(static::never())
            ->method('insert');

        $requestStateRegistryMock->expects(static::never())
            ->method(static::anything());

        $systemConfigServiceDecoration = new SystemConfigRepositoryDecorationProcess(
            $systemConfigRepositoryDecorationProcessRepositoryMock,
            $requestStateRegistryMock
        );

        $systemConfigServiceDecoration->process($callMock, [[], []]);
    }
}
