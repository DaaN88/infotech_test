<?php
declare(strict_types=1);

/* @var $this BookController */
/* @var $dataProvider CActiveDataProvider */

$this->pageTitle = 'Каталог книг';
$this->breadcrumbs = array('Каталог');
?>

<section class="rounded-2xl border border-white/10 bg-white/[0.03] shadow-[0_10px_40px_-20px_rgba(0,0,0,0.8)] backdrop-blur">
  <div class="w-full">
    <table class="w-full table-auto text-left text-sm">
      <thead class="bg-slate-950/40 text-[11px] font-semibold uppercase tracking-wide text-slate-300 border-b border-white/10">
        <tr>
          <th class="px-5 py-3">Название</th>
          <th class="px-5 py-3">Авторы</th>
          <th class="px-5 py-3 w-16 whitespace-nowrap">Год</th>
          <th class="px-5 py-3 w-32">ISBN</th>
          <th class="px-5 py-3">Описание</th>
          <th class="px-5 py-3 w-32 whitespace-nowrap">Обновлено</th>
          <th class="px-5 py-3 w-40 text-right whitespace-nowrap">Действия</th>
        </tr>
      </thead>

      <tbody class="divide-y divide-white/10">
        <?php foreach ($dataProvider->getData() as $book): ?>
          <tr class="bg-white/[0.02] hover:bg-white/[0.05] transition-colors">
            <td class="px-5 py-4 align-top">
              <span class="font-semibold text-sky-200 hover:text-sky-100">
                <?php echo CHtml::encode($book->title); ?>
              </span>
            </td>

            <td class="px-5 py-4 align-top">
              <div class="truncate text-slate-200">
                <?php echo CHtml::encode(implode(', ', CHtml::listData($book->authors, 'id', 'name'))); ?>
              </div>
            </td>

            <td class="px-5 py-4 align-top text-slate-300"><?php echo CHtml::encode($book->year); ?></td>

            <td class="px-5 py-4 align-top">
              <span class="font-mono text-xs text-slate-300 break-words">
                <?php echo CHtml::encode($book->isbn); ?>
              </span>
            </td>

            <td class="px-5 py-4 align-top text-slate-300 break-words">
              <?php if ($book->description): ?>
                <?php echo CHtml::encode($book->description); ?>
              <?php endif; ?>
            </td>

            <td class="px-5 py-4 align-top">
              <span class="block font-mono text-xs text-slate-300 whitespace-nowrap overflow-hidden text-ellipsis">
                <?php echo CHtml::encode($book->updated_at); ?>
              </span>
            </td>

            <td class="px-5 py-4 align-top text-right whitespace-nowrap">
              <?php if (Yii::app()->user->isGuest): ?>
                <?php echo CHtml::link('Подписаться', array('/subscription/create', 'author'=>$book->authors ? $book->authors[0]->id : null), array(
                    'class'=>'inline-flex items-center justify-center rounded-lg bg-sky-500/15 px-4 py-2 text-sm font-semibold text-sky-200 ring-1 ring-inset ring-sky-400/30 hover:bg-sky-500/25 hover:text-sky-100 focus:outline-none focus:ring-2 focus:ring-sky-400/40 whitespace-nowrap shrink-0'
                )); ?>
              <?php else: ?>
                <?php echo CHtml::link('Редактировать', array('/book/update', 'id'=>$book->id), array(
                    'class'=>'inline-flex items-center justify-center rounded-lg bg-slate-200 px-3 py-2 text-sm font-semibold text-slate-900 hover:bg-white transition whitespace-nowrap shrink-0'
                )); ?>
                <?php echo CHtml::link('Удалить', array('/book/delete', 'id'=>$book->id), array(
                    'class'=>'inline-flex items-center justify-center rounded-lg bg-rose-500 px-3 py-2 text-sm font-semibold text-white hover:bg-rose-400 transition whitespace-nowrap shrink-0'
                )); ?>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="px-4 pb-4">
    <?php $this->widget('CLinkPager', array(
        'pages' => $dataProvider->getPagination(),
        'htmlOptions' => array('class' => 'pager'),
    )); ?>
  </div>
</section>
