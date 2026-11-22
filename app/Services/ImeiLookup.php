<?php

namespace App\Services;

use App\Models\Device;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Hot path for IMEI / serial checks.
 *
 * - Two point lookups on the unique imei and imei2 indexes (O(log n), a few pages even at
 *   tens of millions of rows) instead of an OR that depends on the optimiser picking index_merge.
 * - The owner's name comes from the same query via a join, so there is no second query.
 * - Results (including "not found") are cached in an in-memory store when one is configured,
 *   and cleared by Device / User model events, so status changes show up immediately.
 */
class ImeiLookup
{
    private const KEY_PREFIX = 'imei:v1:';
    private const MISS = '__none__';

    public static function normalize(?string $identifier): string
    {
        // IMEIs are digits; serials are stored upper-cased with no spaces.
        return strtoupper(preg_replace('/\s+/', '', (string) $identifier));
    }

    public function find(string $identifier): ?ImeiLookupResult
    {
        $identifier = self::normalize($identifier);
        if ($identifier === '') {
            return null;
        }

        $cache = $this->cache();
        if (! $cache) {
            return $this->query($identifier);
        }

        $key = self::KEY_PREFIX.$identifier;
        $cached = $cache->get($key);
        if ($cached === self::MISS) {
            return null;
        }
        if (is_array($cached)) {
            return ImeiLookupResult::fromRow((object) $cached);
        }

        $result = $this->query($identifier);
        $result
            ? $cache->put($key, $result->toArray(), config('imei.cache_ttl'))
            : $cache->put($key, self::MISS, config('imei.negative_ttl'));

        return $result;
    }

    /** Clear cached results for every identifier this device has (or had before the change). */
    public static function forgetDevice(Device $device): void
    {
        self::forget([
            $device->imei,
            $device->imei2,
            $device->getOriginal('imei'),
            $device->getOriginal('imei2'),
        ]);
    }

    /** @param  array<int, string|null>  $identifiers */
    public static function forget(array $identifiers): void
    {
        $cache = app(self::class)->cache();
        if (! $cache) {
            return;
        }

        foreach (array_unique(array_filter($identifiers)) as $identifier) {
            $cache->forget(self::KEY_PREFIX.self::normalize($identifier));
        }
    }

    private function query(string $identifier): ?ImeiLookupResult
    {
        $row = $this->baseQuery()->where('devices.imei', $identifier)->first()
            ?? $this->baseQuery()->where('devices.imei2', $identifier)->first();

        return $row ? ImeiLookupResult::fromRow($row) : null;
    }

    private function baseQuery()
    {
        return DB::table('devices')
            ->leftJoin('users', 'users.id', '=', 'devices.current_owner_id')
            ->whereNull('devices.deleted_at')
            ->select([
                'devices.id', 'devices.imei', 'devices.imei2', 'devices.brand', 'devices.model',
                'devices.status', 'devices.purchase_type', 'devices.created_at',
                'users.name as owner_name',
            ])
            ->limit(1);
    }

    private function cache(): ?Repository
    {
        $store = config('imei.cache_store');

        return $store && $store !== 'none' ? Cache::store($store) : null;
    }
}
