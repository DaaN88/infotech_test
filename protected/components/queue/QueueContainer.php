<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class QueueContainer implements ContainerInterface
{
    public function get(string $id)
    {
        if ($this->has($id)) {
            if (Yii::app()->hasComponent($id)) {
                return Yii::app()->getComponent($id);
            }

            if (class_exists($id)) {
                return new $id();
            }
        }

        throw new class($id . ' not found') extends Exception implements NotFoundExceptionInterface {};
    }

    public function has(string $id): bool
    {
        return Yii::app()->hasComponent($id) || class_exists($id);
    }
}
