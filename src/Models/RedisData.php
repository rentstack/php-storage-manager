<?php

namespace App\Models;
use Throwable;
use App\Storage\Storage;
use App\Storage\InterfaceStorage;

class RedisData extends Storage implements InterfaceStorage
{
    /**
     * @return array
     */
    public function get(): array
    {
        try {
            $list = [];
            $found_keys = false;
            $keys = @$this->redis::instance()->keys('keys:*');
            foreach ($keys as $value) {
                $found_keys = true;
                $list['query'][] = $value;
                $list['response'][] = str_replace('keys:', '', $value);
            }
            if ($found_keys) {
                $values = $this->redis::instance()->mget($list['query']);
                $result = array_combine($list['response'], $values);
            }
            return [
                'status' => true,
                'query' => true,
                'keys' => $found_keys,
                'data' => ($found_keys) ? $result : []
            ];
        } catch (Throwable) {
            return ['status' => false];
        }
    }

    /**
     * @param string $key
     * @param string $value
     * @return array
     */
    public function set(string $key, string $value): array
    {
        try {
            $ttl = 60 * 60; # 1 hour
            $query = @$this->redis::instance()->set('keys:'.$key, $value, $ttl);
            return [
                'status' => true,
                'query' => $query,
                'key' => $key
            ];
        } catch (Throwable) {
            return ['status' => false];
        }
    }

    /**
     * @param string $key
     * @return array
     */
    public function delete(string $key): array
    {
        try {
            $query = @$this->redis::instance()->del('keys:'.$key);
            return [
                'status' => true,
                'query' => $query,
                'key' => $key
            ];
        } catch (Throwable) {
            return ['status' => false];
        }
    }
}