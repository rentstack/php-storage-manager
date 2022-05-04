<?php

namespace App\Http;
use App\Exceptions\HttpException;

class Template
{
    /**
     * @var string
     */
    protected string $file;

    /**
     * @var array
     */
    protected array $values = [];

    /**
     * Template constructor.
     * @param string|null $file
     */
    public function __construct(string $file = null)
    {
        if(!is_null($file)) {
            $this->setTemplate($file);
        }
    }

    /**
     * @param string $file
     * @return $this
     */
    public function setTemplate(string $file): Template
    {
        $this->file = dirname(__FILE__, 3) . "/public/html/{$file}.html";
        return $this;
    }

    /**
     * @param string $key
     * @param string $value
     */
    protected function set(string $key, string $value): void
    {
        $this->values[$key] = $value;
    }

    /**
     * @return string
     * @throws HttpException
     */
    protected function output(): string
    {
        if (!file_exists($this->file)) {
            throw new HttpException('Template file "' . $this->file . '" is not found.');
        }
        $tpl = file_get_contents($this->file);
        foreach ($this->values as $key => $value) {
            $tpl = str_replace("[@$key]", $value, $tpl);
        }

        return $tpl;
    }

    /**
     * @param array|null $data
     * @throws HttpException
     */
    public function display(array $data = null): void
    {
        if ($data && count($data) > 0) {
            foreach ($data as $key => $value) {
                $this->set($key, $value);
            }
        }
        echo $this->output();
    }
}