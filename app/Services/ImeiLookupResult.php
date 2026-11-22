<?php

namespace App\Services;

use Carbon\CarbonImmutable;

/**
 * The public-safe snapshot of a device returned by an IMEI check.
 * Plain data so it caches cheaply and never lazy-loads relations.
 */
final class ImeiLookupResult
{
    public function __construct(
        public readonly int $id,
        public readonly string $imei,
        public readonly ?string $imei2,
        public readonly ?string $brand,
        public readonly ?string $model,
        public readonly string $status,
        public readonly ?string $purchaseType,
        public readonly ?string $ownerName,
        public readonly ?CarbonImmutable $registeredAt,
    ) {
    }

    public static function fromRow(object $row): self
    {
        return new self(
            id: (int) $row->id,
            imei: (string) $row->imei,
            imei2: $row->imei2,
            brand: $row->brand,
            model: $row->model,
            status: (string) $row->status,
            purchaseType: $row->purchase_type,
            ownerName: $row->owner_name,
            registeredAt: $row->created_at ? CarbonImmutable::parse($row->created_at) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'imei' => $this->imei,
            'imei2' => $this->imei2,
            'brand' => $this->brand,
            'model' => $this->model,
            'status' => $this->status,
            'purchase_type' => $this->purchaseType,
            'owner_name' => $this->ownerName,
            'created_at' => $this->registeredAt?->toIso8601String(),
        ];
    }
}
