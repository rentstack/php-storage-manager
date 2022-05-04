<?php

namespace App\Controllers;
use App\Storage\Storage;
use App\Http\Endpoints;
use JetBrains\PhpStorm\ArrayShape;
use const FILTER_SANITIZE_FULL_SPECIAL_CHARS;
use const FILTER_FLAG_STRIP_HIGH;
use const FILTER_FLAG_STRIP_LOW;

final class ApiController
{
    /**
     * @var bool
     */
    protected bool $cli;

    /**
     * ApiController constructor.
     * @param bool $cli
     */
    public function __construct(bool $cli = false)
    {
        $this->cli = $cli;
    }
    /**
     * @param string|null $storage
     * @return array
     */
    #[ArrayShape(['status' => "bool", 'code' => "int", 'data' => "array"])]
    protected function checkStorage(string $storage = null): array
    {
        $validation = ['status' => true];
        $supported = array_keys(Storage::storages());
        if (!in_array($storage, $supported) || is_null($storage)) {
            $validation['status'] = false;
            $validation['code'] = 500;
            $validation['data']['message'] = 'Supported Storages => ' . implode(', ', $supported);
        }
        return $validation;
    }

    /**
     * @param string $storage
     * @param string $command
     * @param array|null $args
     * @return array
     */
    protected function runQuery(string $storage, string $command, array $args = null): array
    {
        $response = [];
        $validate = $this->checkStorage($storage);
        if ($validate['status']) {
            $data = Storage::storages()[$storage];
            $result = (is_array($args))
                ? $data->{$command}(...$args)
                : $data->{$command}();
            $response['status'] = $result['status'];
            $response['code'] = 500;
            if ($result['status']) {
                if ($result['query']) {
                    $response['code'] = 200;
                    if (isset($result['keys']) && !isset($result['key'])) {
                        $response['data'] = $result['data'];
                    } else {
                        $response['data']['key'] = $result['key'];
                        $response['data']['message'] = 'The key ['.$result['key'].'] was affected in the Storage.';
                    }
                } else {
                    $response['status'] = (bool) $result['query'];
                    $response['data']['message'] = 'The key ['.$result['key'].'] has not been affected in the Storage.';
                }
            } else {
                $response['data']['message'] = 'Error Storage Connection.';
            }
        } else {
            $response = $validate;
        }
        return $response;
    }

    /**
     * @param string $storage
     * @return array|null
     */
    public function get(string $storage): array|null
    {
        $response = $this->runQuery($storage, 'get');
        if (!$this->cli) {
            Endpoints::json($response);
            return null;
        } else {
            return $response;
        }
    }

    /**
     * @param string $storage
     * @param string $key
     * @param string $value
     * @return array|null
     */
    public function add(string $storage, string $key, string $value): array|null
    {
        $response = [];
        $validate = true;
        $key = trim($key);
        $value = trim($value);
        if (mb_strlen($value) < 1) {
            $validate = false;
            $response = $this->checkStorage();
            $response['data']['message'] = 'Value must be at least 1 character.';
        }
        if (!ctype_alnum($key) || mb_strlen($key) < 1) {
            $validate = false;
            $response = $this->checkStorage();
            $response['data']['message'] = 'The key must contain only latin letters and numbers (min 1 character).';
        }
        if ($validate) {
            $response = $this->runQuery($storage, 'set', [$key, $value]);
        }
        if (!$this->cli) {
            Endpoints::json($response);
            return null;
        } else {
            return $response;
        }
    }

    /**
     * @param string $storage
     * @param string $key
     * @return array|null
     */
    public function delete(string $storage, string $key): array|null
    {
        $response = [];
        $validate = true;
        $key = trim($key);
        if (!ctype_alnum($key) || mb_strlen($key) < 1) {
            $validate = false;
            $response = $this->checkStorage();
            $response['data']['message'] = 'The key must contain only latin letters and numbers (min 1 character).';
        }
        if ($validate) {
            $response = $this->runQuery($storage, 'delete', [$key]);
        }
        if (!$this->cli) {
            Endpoints::json($response);
            return null;
        } else {
            return $response;
        }
    }

    /**
     * @param string $storage
     * @return void
     */
    public function create(string $storage): void
    {
        $key = filter_input(INPUT_POST, 'key', FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_STRIP_HIGH);
        $val = filter_input(INPUT_POST, 'value', FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_STRIP_LOW);
        if (!is_null($key) && !is_null($val)) {
            $this->add($storage, $key, $val);
        } else {
            $response = $this->checkStorage();
            $response['data']['message'] = 'You must specify a key and value.';
            Endpoints::json($response);
        }
    }
}