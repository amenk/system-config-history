<?php declare(strict_types=1);

namespace MatheusGontijo\SystemConfigHistory\View\Admin\MatheusGontijoSystemConfig;

use MatheusGontijo\SystemConfigHistory\System\MatheusGontijoSystemConfigHistory\MatheusGontijoSystemConfigHistoryEntity;
use Shopware\Core\Defaults;

class HistoryTab
{
    /**
     * @return array<string, mixed>
     */
    public function formatModalData(
        string $defaultSalesChannelName,
        MatheusGontijoSystemConfigHistoryEntity $matheusGontijoSystemConfigHistory
    ): array {
        $data = [];

        $configurationKey = $matheusGontijoSystemConfigHistory->getConfigurationKey();
        \assert($configurationKey !== null);

        $data['configuration_key'] = $configurationKey;

        $configurationValueOld = null;

        if ($matheusGontijoSystemConfigHistory->getConfigurationValueOld() !== null) {
            $configurationValueOldArray = $matheusGontijoSystemConfigHistory->getConfigurationValueOld();

            \assert(isset($configurationValueOldArray['_value']));
            $configurationValueOld = $configurationValueOldArray['_value'];
        }

        $data['configuration_value_old'] = $configurationValueOld;

        $data['configuration_value_old_type'] = $this->typeOf($configurationValueOld);

        $configurationValueNew = null;

        if ($matheusGontijoSystemConfigHistory->getConfigurationValueNew() !== null) {
            $configurationValueNewArray = $matheusGontijoSystemConfigHistory->getConfigurationValueNew();

            \assert(isset($configurationValueNewArray['_value']));
            $configurationValueNew = $configurationValueNewArray['_value'];
        }

        $data['configuration_value_new'] = $configurationValueNew;

        $data['configuration_value_new_type'] = $this->typeOf($configurationValueNew);

        $data['sales_channel_name'] = $defaultSalesChannelName;

        $data['username'] = $matheusGontijoSystemConfigHistory->getUsername();

        $createdAt = $matheusGontijoSystemConfigHistory->getCreatedAt();
        \assert($createdAt instanceof \DateTimeInterface);

        $data['modified_at'] = $createdAt->format(Defaults::STORAGE_DATE_TIME_FORMAT);

        return $data;
    }

    /**
     * @param array<string, mixed>|bool|float|int|string|null $value
     */
    private function typeOf($value): string
    {
        if ($value === null) {
            return 'null';
        }

        if (\is_array($value)) {
            return 'array';
        }

        if (\is_int($value)) {
            return 'integer';
        }

        if (\is_float($value)) {
            return 'float';
        }

        if (\is_bool($value)) {
            return 'boolean';
        }

        return 'string';
    }
}
