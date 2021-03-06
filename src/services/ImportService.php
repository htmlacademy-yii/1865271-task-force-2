<?php

namespace Taskforce\services;

use Taskforce\exceptions\BaseException;

class ImportService
{
    private $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function saveTo(string $file): void
    {
        if (!file_put_contents($file, $this->model->sql())) {
            throw new BaseException('Ошибка записи файла ' . $file);
        }
    }

}
