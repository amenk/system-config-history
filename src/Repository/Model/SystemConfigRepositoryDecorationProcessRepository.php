<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Repository\Model;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ForwardCompatibility\Result;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\User\UserEntity;

class SystemConfigRepositoryDecorationProcessRepository
{
    private Connection $connection;

    private EntityRepository $matheusGontijoSystemConfigHistoryRepository;

    private EntityRepository $userRepository;

    public function __construct(
        Connection $connection,
        EntityRepositoryInterface $matheusGontijoSystemConfigHistoryRepository,
        EntityRepositoryInterface $userRepository
    ) {
        \assert($matheusGontijoSystemConfigHistoryRepository instanceof EntityRepository);
        \assert($userRepository instanceof EntityRepository);

        $this->connection = $connection;
        $this->matheusGontijoSystemConfigHistoryRepository = $matheusGontijoSystemConfigHistoryRepository;
        $this->userRepository = $userRepository;
    }

    public function isEnabled(): bool
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select(['configuration_value']);
        $qb->from('system_config');
        $qb->where('configuration_key = :configuration_key');
        $qb->andWhere('sales_channel_id IS NULL');

        $qb->setParameter(':configuration_key', 'matheusGontijo.systemConfigHistory.enabled');

        $qb->setMaxResults(1);

        $executeResult = $qb->execute();
        \assert($executeResult instanceof Result);

        $value = $executeResult->fetchOne();

        if ($value === false || $value === null) {
            return false;
        }

        $decodedValue = json_decode($value, true);

        return $decodedValue === ['_value' => true];
    }

    public function generateId(): string
    {
        return Uuid::randomHex();
    }

    /**
     * @return array<mixed>|null
     */
    public function getValue(string $key, ?string $salesChannelId = null): ?array
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select(['configuration_value']);
        $qb->from('system_config');

        $qb->where('configuration_key = :configuration_key');
        $qb->setParameter(':configuration_key', $key);

        if ($salesChannelId === null) {
            $qb->andWhere('sales_channel_id IS NULL');
        } else {
            $qb->andWhere('sales_channel_id = :sales_channel_id');
            $qb->setParameter(':sales_channel_id', Uuid::fromHexToBytes($salesChannelId));
        }

        $executeResult = $qb->execute();
        \assert($executeResult instanceof Result);

        return $this->decodeValue($executeResult->fetchOne());
    }

    /**
     * @param array<mixed> $data
     */
    public function insert(array $data): void
    {
        $this->matheusGontijoSystemConfigHistoryRepository->create($data, Context::createDefaultContext());
    }

    public function loadUser(string $id): UserEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('id', $id));

        $searchResult = $this->userRepository->search($criteria, Context::createDefaultContext());

        $user = $searchResult->first();
        \assert($user instanceof UserEntity);

        return $user;
    }

    /**
     * @param string|bool $value
     *
     * @return array<mixed>|null
     */
    private function decodeValue($value): ?array
    {
        if ($value === false) {
            return null;
        }

        \assert(\is_string($value));

        return json_decode($value, true) ?? [];
    }
}
