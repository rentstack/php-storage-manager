<?php

namespace App\Storage;

interface InterfaceStorage
{
    /**
     * @return array
     */
    public function get(): array;

    /**
     * @param string $key
     * @param string $value
     * @return array
     */
    public function set(string $key, string $value): array;

    /**
     * @param string $key
     * @return array
     */
    public function delete(string $key): array;
}