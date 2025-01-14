<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\Decoration;

use MatheusGontijo\SystemConfigHistory\Model\SystemConfigRepositoryDecorationProcess;
use MatheusGontijo\SystemConfigHistory\Repository\Decoration\SystemConfigRepositoryDecorationRepository;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEventFactory;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\AggregationResult\AggregationResultCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Write\CloneBehavior;

/**
 * @psalm-suppress InvalidExtendClass
 */
class SystemConfigRepositoryDecoration extends EntityRepository
{
    private EntityRepository $entityRepository;

    private SystemConfigRepositoryDecorationProcess $systemConfigRepositoryDecorationProcess;

    private SystemConfigRepositoryDecorationRepository $systemConfigRepositoryDecorationRepository;

    public function __construct(
        EntityRepository $entityRepository,
        SystemConfigRepositoryDecorationProcess $systemConfigRepositoryDecorationProcess,
        SystemConfigRepositoryDecorationRepository $systemConfigRepositoryDecorationRepository
    ) {
        $this->entityRepository = $entityRepository;
        $this->systemConfigRepositoryDecorationProcess = $systemConfigRepositoryDecorationProcess;
        $this->systemConfigRepositoryDecorationRepository = $systemConfigRepositoryDecorationRepository;
    }

    public function setEntityLoadedEventFactory(EntityLoadedEventFactory $eventFactory): void
    {
        /**
         * @psalm-suppress DeprecatedMethod
         */
        $this->entityRepository->setEntityLoadedEventFactory($eventFactory);
    }

    /**
     * @psalm-suppress MissingImmutableAnnotation
     */
    public function getDefinition(): EntityDefinition
    {
        return $this->entityRepository->getDefinition();
    }

    public function search(Criteria $criteria, Context $context): EntitySearchResult
    {
        return $this->entityRepository->search($criteria, $context);
    }

    public function aggregate(Criteria $criteria, Context $context): AggregationResultCollection
    {
        return $this->entityRepository->aggregate($criteria, $context);
    }

    public function searchIds(Criteria $criteria, Context $context): IdSearchResult
    {
        return $this->entityRepository->searchIds($criteria, $context);
    }

    /**
     * @param array<mixed> $data
     *
     * @psalm-suppress MethodSignatureMismatch
     */
    public function update(array $data, Context $context): EntityWrittenContainerEvent
    {
        $call = fn (): EntityWrittenContainerEvent => $this->entityRepository->update($data, $context);

        return $this->systemConfigRepositoryDecorationProcess->process($call, $data);
    }

    /**
     * @param array<mixed> $data
     */
    public function upsert(array $data, Context $context): EntityWrittenContainerEvent
    {
        $call = fn (): EntityWrittenContainerEvent => $this->entityRepository->upsert($data, $context);

        return $this->systemConfigRepositoryDecorationProcess->process($call, $data);
    }

    /**
     * @param array<mixed> $data
     */
    public function create(array $data, Context $context): EntityWrittenContainerEvent
    {
        $call = fn (): EntityWrittenContainerEvent => $this->entityRepository->create($data, $context);

        return $this->systemConfigRepositoryDecorationProcess->process($call, $data);
    }

    /**
     * @param array<mixed> $ids
     */
    public function delete(array $ids, Context $context): EntityWrittenContainerEvent
    {
        $searchIds = [];

        foreach ($ids as $id) {
            $searchIds[] = $id['id'];
        }

        $systemConfigs = $this->systemConfigRepositoryDecorationRepository->search($this->entityRepository, $searchIds);

        $data = [];

        foreach ($systemConfigs as $systemConfig) {
            $data[] = [
                'configurationKey' => $systemConfig->getConfigurationKey(),
                'salesChannelId' => $systemConfig->getSalesChannelId(),
            ];
        }

        $call = fn (): EntityWrittenContainerEvent => $this->entityRepository->delete($ids, $context);

        return $this->systemConfigRepositoryDecorationProcess->process($call, $data);
    }

    public function createVersion(string $id, Context $context, ?string $name = null, ?string $versionId = null): string
    {
        return $this->entityRepository->createVersion($id, $context, $name, $versionId);
    }

    public function merge(string $versionId, Context $context): void
    {
        $this->entityRepository->merge($versionId, $context);
    }

    public function clone(
        string $id,
        Context $context,
        ?string $newId = null,
        ?CloneBehavior $behavior = null
    ): EntityWrittenContainerEvent {
        return $this->entityRepository->clone($id, $context, $newId, $behavior);
    }
}
