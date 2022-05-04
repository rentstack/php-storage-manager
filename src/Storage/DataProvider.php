<?php /** @noinspection PhpUndefinedClassInspection */

namespace App\Storage;
use Memcached;
use RedisClient\RedisClient;

class DataProvider implements InterfaceProvider
{
    /**
     * @var Memcached|RedisClient
     */
    protected static Memcached|RedisClient $instance;

    /**
     * Database constructor.
     * @param Memcached|RedisClient $connection
     */
    public function __construct(Memcached|RedisClient $connection)
    {
        if (!empty($connection)) {
            self::$instance = $connection;
        }
    }

    /**
     * @return Memcached|RedisClient
     */
    public static function instance(): Memcached|RedisClient
    {
        return self::$instance;
    }

    /**
     * @param Memcached|RedisClient $connection
     * @return DataProvider
     */
    public static function connection(Memcached|RedisClient $connection): DataProvider
    {
        return new DataProvider($connection);
    }
}