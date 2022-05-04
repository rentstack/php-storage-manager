<?php

namespace App\Controllers;
use App\Exceptions\HttpException;
use App\Http\Template;

final class IndexController
{
    /**
     * @var Template|null
     */
    protected ?Template $template = null;

    /**
     * IndexController constructor.
     */
    public function __construct()
    {
        $this->template = new Template();
    }

    /**
     * @return void
     * @throws HttpException
     */
    public function index(): void
    {
        if ($this->template instanceof Template) {
            $this->template->setTemplate(__FUNCTION__)
                ->display([
                    'title' => 'Index',
                    'header' => 'Redis Storage',
                    'generate' => 'Generate Random Key',
                    'placeholder' => 'Enter key..',
                    'value' => 'Value',
                    'button' => 'Add to Redis',
                    'info' => 'Must be min 1 character long [a-z 0-9]',
                ]);
        } else {
            throw new HttpException('$this->template is not instance of Template class.');
        }
    }
}