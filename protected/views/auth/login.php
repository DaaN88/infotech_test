<?php
declare(strict_types=1);

/* @var $this AuthController */
/* @var $model LoginForm */
/* @var $form CActiveForm */

$panelClass = 'rounded-2xl border border-white/10 bg-white/[0.04] shadow-[0_20px_70px_-35px_rgba(0,0,0,0.85)] '
    . 'backdrop-blur px-6 py-7 sm:px-8';
$inputClass = 'block w-full rounded-xl border border-white/10 bg-slate-950/40 px-4 py-2.5 text-slate-100 '
    . 'placeholder:text-slate-500 outline-none ring-0 focus:border-sky-400/40 focus:ring-2 focus:ring-sky-400/30';
$buttonClass = 'w-full inline-flex items-center justify-center rounded-xl bg-sky-500/20 px-4 py-2.5 text-sm '
    . 'font-semibold text-sky-100 ring-1 ring-inset ring-sky-400/30 hover:bg-sky-500/30 hover:text-white '
    . 'focus:outline-none focus:ring-2 focus:ring-sky-400/40';
?>

<div class="auth-wrapper">
  <div class="mx-auto max-w-md">
    <div class="<?php echo $panelClass; ?>">

      <?php $form = $this->beginWidget('CActiveForm', [
          'id' => 'login-form',
          'enableClientValidation' => true,
          'clientOptions' => ['validateOnSubmit' => true],
      ]); ?>

      <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight text-sky-200">Вход</h1>
        <p class="mt-2 text-sm text-slate-300">
          Используйте учётные данные, чтобы продолжить.
        </p>
      </div>

      <?php if ($model->hasErrors()): ?>
        <div class="mb-5 rounded-xl border border-rose-400/25 bg-rose-500/10 px-4 py-3 text-sm text-rose-100">
          <?php echo $form->errorSummary($model, '', '', ['class'=>'m-0 list-disc pl-5 space-y-1']); ?>
        </div>
      <?php endif; ?>

      <div class="space-y-4">
        <div>
          <?php echo $form->labelEx(
              $model,
              'username',
              ['class'=>'mb-1.5 block text-sm font-medium text-slate-200']
          ); ?>
          <?php echo $form->textField(
              $model,
              'username',
              [
                  'class' => $inputClass,
                  'placeholder' => 'Логин',
                  'autocomplete' => 'username',
              ]
          ); ?>
          <?php echo $form->error($model, 'username', ['class'=>'mt-1 text-xs text-rose-200']); ?>
        </div>

        <div>
          <?php echo $form->labelEx(
              $model,
              'password',
              ['class'=>'mb-1.5 block text-sm font-medium text-slate-200']
          ); ?>
          <?php echo $form->passwordField(
              $model,
              'password',
              [
                  'class' => $inputClass,
                  'placeholder' => 'Пароль',
                  'autocomplete' => 'current-password',
              ]
          ); ?>
          <?php echo $form->error($model, 'password', ['class'=>'mt-1 text-xs text-rose-200']); ?>
        </div>

        <div class="flex items-center justify-between gap-3">
          <label class="inline-flex items-center gap-2 text-sm text-slate-300">
            <?php echo $form->checkBox($model, 'rememberMe', [
                'class' => 'h-4 w-4 rounded border-white/20 bg-slate-950/40 text-sky-400 focus:ring-sky-400/30',
            ]); ?>
            <span>Запомнить меня</span>
          </label>
          <a href="#" class="text-sm text-sky-200 hover:text-sky-100">Забыли пароль?</a>
        </div>

        <div class="text-xs text-slate-400">
          Демо: <span class="font-mono text-slate-300">admin/admin</span>
        </div>

        <div class="pt-2">
          <?php echo CHtml::submitButton('Войти', [
              'class'=>$buttonClass
          ]); ?>
        </div>
      </div>

      <?php $this->endWidget(); ?>
    </div>
  </div>
</div>
