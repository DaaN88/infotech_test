<?php
declare(strict_types=1);

/* @var $this ReportController */
/* @var $topAuthors array */
/* @var $year int */
/* @var $years array */

$this->pageTitle = 'ТОП-10 авторов';
$this->breadcrumbs = ['Отчет', 'ТОП-10'];

$selectClass = 'rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm text-slate-100 '
    . 'focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-400/40';
$cardClass = 'rounded-2xl border border-white/10 bg-white/[0.03] shadow-[0_10px_40px_-20px_rgba(0,0,0,0.8)] '
    . 'backdrop-blur';
$theadClass = 'bg-slate-950/40 text-[11px] font-semibold uppercase tracking-wide text-slate-300 border-b '
    . 'border-white/10';
?>

<section class="space-y-4 rounded-xl border border-white/5 bg-slate-900/80 p-4 sm:p-5">
  <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h1 class="text-xl font-semibold text-slate-50">ТОП-10 авторов</h1>
      <p class="text-sm text-slate-300">Авторы, выпустившие больше книг за выбранный год.</p>
    </div>

    <?php if ($years): ?>
      <?php $action = Yii::app()->request->scriptUrl . '?r=report/top'; ?>
      <form id="top-year-form" method="get" action="<?php echo $action; ?>" class="flex items-center gap-2">
        <input type="hidden" name="r" value="report/top">
        <label for="year" class="text-sm text-slate-300">Год</label>
        <select id="year" name="year" class="<?php echo $selectClass; ?>">
          <?php foreach ($years as $y): ?>
            <option value="<?php echo (int) $y; ?>" <?php echo ((int)$y === (int)$year) ? 'selected' : ''; ?>>
              <?php echo CHtml::encode($y); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </form>
    <?php endif; ?>
  </div>

  <div class="<?php echo $cardClass; ?>">
    <table class="w-full table-auto text-left text-sm">
      <thead class="<?php echo $theadClass; ?>">
        <tr>
          <th class="px-5 py-3 w-12">#</th>
          <th class="px-5 py-3">Автор</th>
          <th class="px-5 py-3 w-32">Книг за <?php echo CHtml::encode($year); ?></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-white/10">
        <?php if ($topAuthors): ?>
          <?php foreach ($topAuthors as $index => $author): ?>
            <tr class="bg-white/[0.02] hover:bg-white/[0.05] transition-colors">
              <td class="px-5 py-4 align-top font-semibold text-slate-200">
                <?php echo $index + 1; ?>
              </td>
              <td class="px-5 py-4 align-top text-slate-100">
                <?php echo CHtml::encode($author['name']); ?>
              </td>
              <td class="px-5 py-4 align-top text-slate-200">
                <?php echo (int) $author['books_count']; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="3" class="px-5 py-6 text-center text-slate-300">Нет данных за выбранный год.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
      var form = document.getElementById('top-year-form');
      var select = document.getElementById('year');
      if (form && select) {
        select.addEventListener('change', function () {
          form.submit();
        });
      }
    });
  </script>
</section>
