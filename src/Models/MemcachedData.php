<?php

namespace App\Models;
use App\Storage\Storage;
use App\Storage\InterfaceStorage;
use JetBrains\PhpStorm\ArrayShape;

class MemcachedData extends Storage implements InterfaceStorage
{
    /**
     * @return array
     */
    #[ArrayShape(['status' => "false"])]
    public function get(): array
    {
        # todo => implement getting data from Memcached
        return ['status' => false];
    }

    /**
     * @param string $key
     * @param string $value
     * @return array
     */
    #[ArrayShape(['status' => "false"])]
    public function set(string $key, string $value): array
    {
        # todo => implement add data to Memcached
        return ['status' => false];
    }

    /**
     * @param string $key
     * @return array
     */
    #[ArrayShape(['status' => "false"])]
    public function delete(string $key): array
    {
        # todo => implement remove data from Memcached
        return ['status' => false];
    }
}