<?php /** @noinspection PhpUndefinedClassInspection */

namespace App\Storage;
use App\Models\RedisData;
use App\Models\MemcachedData;
use JetBrains\PhpStorm\ArrayShape;
use RedisClient\RedisClient;
use Memcached;

class Storage
{
    /**
     * @var DataProvider|null
     */
    protected ?DataProvider $redis = null;

    /**
     * @var DataProvider|null
     */
    protected ?DataProvider $memcached = null;

    /**
     * Storage constructor.
     */
    public function __construct()
    {
        if (is_null($this->memcached) && class_exists('Memcached')) {
            $memcached = new Memcached();
            $memcached->addServer('127.0.0.1', 11211);
            $this->memcached = DataProvider::connection($memcached);
        }
        if (is_null($this->redis)) {
            $this->redis = DataProvider::connection(@new RedisClient([
                'server' => '127.0.0.1:6379',
                'timeout' => 2
            ]));
        }
    }

    /**
     * @return array
     */
    #[ArrayShape(['redis' => "\App\Models\RedisData", 'memcached' => "\App\Models\MemcachedData"])]
    public static function storages(): array
    {
        return [
            'redis' => new RedisData(),
            'memcached' => new MemcachedData()
        ];
    }
}