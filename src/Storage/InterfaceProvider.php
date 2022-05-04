<?php /** @noinspection PhpUndefinedClassInspection */

namespace App\Storage;
use Memcached;
use RedisClient\RedisClient;

interface InterfaceProvider
{
    /**
     * @return Memcached|RedisClient
     */
    public static function instance(): Memcached|RedisClient;

    /**
     * @param Memcached|RedisClient $connection
     * @return DataProvider
     */
    public static function connection(Memcached|RedisClient $connection): DataProvider;
}