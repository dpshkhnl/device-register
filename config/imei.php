<?php

return [
    /*
    | Cache store for IMEI lookup results. Use an in-memory store (redis / memcached)
    | in production. "none" queries the database every time, which is still a
    | unique-index lookup and fast, but adds load at high volume.
    | Defaults to redis when the app cache is redis, otherwise none (a database
    | cache would just trade one indexed query for another plus a write).
    */
    'cache_store' => env('IMEI_CACHE_STORE', env('CACHE_STORE') === 'redis' ? 'redis' : 'none'),

    // Seconds to keep a found device. Entries are also cleared whenever the device or its owner changes.
    'cache_ttl' => (int) env('IMEI_CACHE_TTL', 3600),

    // Seconds to remember "not registered", kept short so newly registered devices show up quickly
    // even if they were inserted without model events (e.g. a raw SQL import).
    'negative_ttl' => (int) env('IMEI_NEGATIVE_TTL', 60),

    // Lookups per minute per signed-in user (or per IP for guests).
    'rate_limit' => (int) env('IMEI_RATE_LIMIT', 30),
];
