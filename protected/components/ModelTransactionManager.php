<?php

declare(strict_types=1);

class ModelTransactionManager extends CApplicationComponent
{
    public function run(callable $callback)
    {
        $tx = Yii::app()->db->beginTransaction();

        try {
            $result = $callback();

            $tx->commit();

            return $result;
        } catch (Throwable $e) {
            $tx->rollback();

            throw $e;
        }
    }

    public function save( $models): void
    {
        $this->run(function () use ($models) {
            foreach ($models as $model) {
                if (! $model instanceof CActiveRecord) {
                    throw new InvalidArgumentException('All items must be CActiveRecord instances');
                }

                if (! $model->save()) {
                    throw new ValidationException(
                        Yii::t('app', 'model.save_failed'),

                        $model->getErrors()
                    );
                }
            }
        });
    }
}
