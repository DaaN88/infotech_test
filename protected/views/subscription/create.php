<?php
declare(strict_types=1);

/* @var $this SubscriptionController */
/* @var $model Subscription */
/* @var $author Author */

$this->pageTitle = 'Подписка на автора';
$this->breadcrumbs = array('Каталог' => array('/book/index'), 'Подписка');
?>

<section class="rounded-xl border border-white/5 bg-slate-900/80 max-w-xl">
  <div class="px-6 py-5 border-b border-white/10">
    <h1 class="text-lg font-semibold text-sky-100">Подписка на автора</h1>
    <?php if ($author): ?>
      <p class="text-slate-300 text-sm mt-1">Автор: <?php echo CHtml::encode($author->name); ?></p>
    <?php elseif ($book): ?>
      <p class="text-slate-300 text-sm mt-1">Книга: <?php echo CHtml::encode($book->title); ?></p>
      <p class="text-slate-300 text-xs">Выберите автора, на которого хотите подписаться.</p>
    <?php else: ?>
      <p class="text-slate-300 text-sm mt-1">Выберите автора, чтобы получать уведомления о новых книгах.</p>
    <?php endif; ?>
  </div>

  <div class="px-6 py-5">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'subscription-form',
        'enableClientValidation' => true,
        'clientOptions' => array('validateOnSubmit' => true),
        'htmlOptions' => array('class' => 'space-y-4'),
    )); ?>

      <div>
        <?php echo $form->labelEx($model, 'phone', array('class'=>'block text-sm font-medium text-slate-200')); ?>
        <?php echo $form->textField($model, 'phone', array('class'=>'mt-1 w-full rounded-lg bg-slate-800/70 border border-white/10 px-3 py-2 text-slate-100')); ?>
        <?php echo $form->error($model, 'phone', array('class'=>'text-rose-400 text-sm mt-1')); ?>
      </div>

      <div>
        <?php echo $form->labelEx($model, 'name', array('class'=>'block text-sm font-medium text-slate-200')); ?>
        <?php echo $form->textField($model, 'name', array('class'=>'mt-1 w-full rounded-lg bg-slate-800/70 border border-white/10 px-3 py-2 text-slate-100')); ?>
        <?php echo $form->error($model, 'name', array('class'=>'text-rose-400 text-sm mt-1')); ?>
      </div>

      <?php if (!empty($authorOptions)): ?>
        <div>
          <?php echo $form->labelEx($model, 'author_id', array('class'=>'block text-sm font-medium text-slate-200')); ?>
          <?php echo $form->dropDownList($model, 'author_id', $authorOptions, array(
              'class'=>'mt-1 w-full rounded-lg bg-slate-800/70 border border-white/10 px-3 py-2 text-slate-100'
          )); ?>
          <?php echo $form->error($model, 'author_id', array('class'=>'text-rose-400 text-sm mt-1')); ?>
        </div>
      <?php else: ?>
        <?php echo $form->hiddenField($model, 'author_id'); ?>
      <?php endif; ?>

      <div class="flex items-center gap-3 pt-2">
        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-sky-500/80 text-sm font-semibold text-white hover:bg-sky-400">
          Подписаться
        </button>
        <a href="<?php echo $this->createUrl('/book/index'); ?>" class="text-slate-300 hover:text-white text-sm">Отмена</a>
      </div>

    <?php $this->endWidget(); ?>
  </div>
</section>
