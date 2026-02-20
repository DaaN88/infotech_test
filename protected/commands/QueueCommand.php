<?php
declare(strict_types=1);

class QueueCommand extends CConsoleCommand
{
    /**
     * Выполняет задачи из очереди и завершает работу.
     *
     * @param int $max сколько задач обработать (0 — все доступные)
     */
    public function actionRun(int $max = 0): void
    {
        Yii::app()->queue->run($max);
    }

    /**
     * Слушает очередь (используйте supervisor/systemd для постоянного процесса).
     */
    public function actionListen(): void
    {
        Yii::app()->queue->listen();
    }
}
