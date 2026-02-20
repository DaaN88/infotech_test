<?php
declare(strict_types=1);

/* @var $book Book */

$primaryPhoto = $book->primaryPhoto;
$authorsList = implode(', ', CHtml::listData($book->authors, 'id', 'name'));
$authorIds = implode(',', CHtml::listData($book->authors, 'id', 'id'));
?>
<tr
  id="book-row-<?php echo (int) $book->id; ?>"
  class="bg-white/[0.02] hover:bg-white/[0.06] transition-colors"
  data-book-id="<?php echo (int) $book->id; ?>"
  data-title="<?php echo CHtml::encode($book->title); ?>"
  data-year="<?php echo CHtml::encode($book->year); ?>"
  data-isbn="<?php echo CHtml::encode($book->isbn); ?>"
  data-description="<?php echo CHtml::encode($book->description); ?>"
  data-authors="<?php echo CHtml::encode($authorIds); ?>"
>
  <td class="px-5 py-4 align-top">
    <?php if ($primaryPhoto): ?>
      <?php echo CHtml::link(CHtml::encode($book->title),
        Yii::app()->request->baseUrl . '/images/' . $primaryPhoto->file_name,
        array(
            'class'=>'font-semibold text-sky-200 hover:text-sky-100 underline decoration-sky-400/70',
            'target'=>'_blank',
            'rel'=>'noopener',
            'title'=>'Откроется фото в новой вкладке',
        )
      ); ?>
    <?php else: ?>
      <span class="font-semibold text-sky-200">
        <?php echo CHtml::encode($book->title); ?>
      </span>
    <?php endif; ?>
  </td>

  <td class="px-5 py-4 align-top">
    <div class="truncate text-slate-200 max-w-[14rem]" title="<?php echo CHtml::encode($authorsList); ?>">
      <?php echo CHtml::encode($authorsList); ?>
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
          'class'=>'js-book-edit inline-flex items-center justify-center rounded-lg bg-slate-200 px-3 py-2 text-sm font-semibold text-slate-900 hover:bg-white transition whitespace-nowrap shrink-0',
          'data-book-id' => $book->id,
      )); ?>
      <?php echo CHtml::link('Удалить', array('/book/delete', 'id'=>$book->id), array(
          'class'=>'js-book-delete inline-flex items-center justify-center rounded-lg bg-rose-500 px-3 py-2 text-sm font-semibold text-white hover:bg-rose-400 transition whitespace-nowrap shrink-0',
          'data-book-id' => $book->id,
      )); ?>
    <?php endif; ?>
  </td>
</tr>
