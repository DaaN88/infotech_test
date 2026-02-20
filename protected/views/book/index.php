<?php
declare(strict_types=1);

/* @var $this BookController */
/* @var $dataProvider CActiveDataProvider */
/* @var $authors Author[] */

$this->pageTitle = 'Каталог книг';
$this->breadcrumbs = array('Каталог');

$authorOptions = CHtml::listData($authors, 'id', 'name');

Yii::app()->clientScript->registerCoreScript('jquery');
?>

<?php if (! Yii::app()->user->isGuest): ?>
  <section class="mb-6 rounded-xl border border-white/5 bg-slate-900/80">
    <div class="px-5 py-4 border-b border-white/10 flex items-center justify-between gap-3">
      <div>
        <h2 class="text-lg font-semibold text-sky-100">Добавить / редактировать книгу</h2>
        <p class="text-slate-300 text-sm">Все операции проходят без перезагрузки страницы.</p>
      </div>
      <span id="book-form-mode" class="text-xs uppercase tracking-wide text-emerald-300 font-semibold">Создание</span>
    </div>

    <form id="book-form" class="px-5 py-4 grid gap-4 sm:grid-cols-2" enctype="multipart/form-data">
      <input type="hidden" name="book_id" id="book_id" value="">

      <label class="flex flex-col gap-1">
        <span class="text-sm text-slate-200 font-medium">Название *</span>
        <input required type="text" name="Book[title]" id="book_title" class="rounded-lg bg-slate-800/70 border border-white/10 px-3 py-2 text-slate-100" />
      </label>

      <label class="flex flex-col gap-1">
        <span class="text-sm text-slate-200 font-medium">Год *</span>
        <input required type="number" name="Book[year]" id="book_year" class="rounded-lg bg-slate-800/70 border border-white/10 px-3 py-2 text-slate-100" min="1" />
      </label>

      <label class="flex flex-col gap-1 sm:col-span-2">
        <span class="text-sm text-slate-200 font-medium">ISBN *</span>
        <input required type="text" name="Book[isbn]" id="book_isbn" class="rounded-lg bg-slate-800/70 border border-white/10 px-3 py-2 text-slate-100" />
      </label>

      <label class="flex flex-col gap-1 sm:col-span-2">
        <span class="text-sm text-slate-200 font-medium">Описание</span>
        <textarea name="Book[description]" id="book_description" rows="2" class="rounded-lg bg-slate-800/70 border border-white/10 px-3 py-2 text-slate-100"></textarea>
      </label>

      <label class="flex flex-col gap-1">
        <span class="text-sm text-slate-200 font-medium">Авторы *</span>
        <select name="authors[]" id="book_authors" multiple size="5" class="rounded-lg bg-slate-800/70 border border-white/10 px-3 py-2 text-slate-100">
          <?php foreach ($authorOptions as $id => $name): ?>
            <option value="<?php echo (int) $id; ?>"><?php echo CHtml::encode($name); ?></option>
          <?php endforeach; ?>
        </select>
        <span class="text-xs text-slate-400">Можно выбрать нескольких авторов (Ctrl/Cmd + клик).</span>
      </label>

      <label class="flex flex-col gap-1">
        <span class="text-sm text-slate-200 font-medium">Фото (опционально)</span>
        <input type="file" name="cover" id="book_cover" accept="image/*" class="text-slate-200 text-sm" />
      </label>

      <div class="sm:col-span-2 flex items-center gap-3 pt-2">
        <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-emerald-500/80 text-sm font-semibold text-white hover:bg-emerald-400" id="book-submit-btn">
          Сохранить
        </button>
        <button type="button" class="hidden inline-flex items-center px-4 py-2 rounded-lg bg-slate-700 text-sm font-semibold text-white hover:bg-slate-600" id="book-cancel-edit">
          Отмена редактирования
        </button>
        <div id="book-form-status" class="text-sm text-slate-300"></div>
      </div>
      <div id="book-form-errors" class="sm:col-span-2 text-sm text-rose-300"></div>
    </form>
  </section>
<?php endif; ?>

<section class="rounded-xl border border-white/5 bg-slate-900/80">
  <div class="w-full">
    <table class="w-full table-auto text-left text-sm">
      <thead class="bg-slate-900 text-[11px] font-semibold uppercase tracking-wide text-slate-300 border-b border-white/10">
        <tr>
          <th class="px-5 py-3">Название</th>
          <th class="px-5 py-3 w-48">Авторы</th>
          <th class="px-5 py-3 w-16 whitespace-nowrap">Год</th>
          <th class="px-5 py-3 w-32">ISBN</th>
          <th class="px-5 py-3">Описание</th>
          <th class="px-5 py-3 w-32 whitespace-nowrap">Обновлено</th>
          <th class="px-5 py-3 w-36 text-right whitespace-nowrap">Действия</th>
        </tr>
      </thead>

      <tbody class="divide-y divide-white/10" id="book-table-body">
        <?php foreach ($dataProvider->getData() as $book): ?>
          <?php echo $this->renderPartial('_row', ['book' => $book]); ?>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="px-4 pb-4 flex items-center justify-between gap-4">
    <div>
      <?php $this->widget('CLinkPager', array(
          'pages' => $dataProvider->getPagination(),
          'htmlOptions' => array('class' => 'pager'),
          'firstPageLabel' => '« Первая',
          'lastPageLabel' => 'Последняя »',
          'nextPageLabel' => 'Следующая ›',
          'prevPageLabel' => '‹ Предыдущая',
      )); ?>
    </div>
    <div class="ml-auto">
      <?php echo CHtml::link('ТОП-10', array('/report/top'), array(
          'class'=>'inline-flex items-center justify-center rounded-lg bg-emerald-500/15 px-4 py-2 text-sm font-semibold text-emerald-200 ring-1 ring-inset ring-emerald-400/30 hover:bg-emerald-500/25 hover:text-emerald-100 focus:outline-none focus:ring-2 focus:ring-emerald-400/40 whitespace-nowrap shrink-0'
      )); ?>
    </div>
  </div>
</section>

<?php if (! Yii::app()->user->isGuest): ?>
  <script>
    (function($) {
      const $form = $('#book-form');
      const $modeLabel = $('#book-form-mode');
      const $cancelBtn = $('#book-cancel-edit');
      const $status = $('#book-form-status');
      const $errors = $('#book-form-errors');
      const $tableBody = $('#book-table-body');
      let editingId = null;

      const urls = {
        create: '<?php echo $this->createUrl('/book/create'); ?>',
        update: '<?php echo $this->createUrl('/book/update'); ?>',
        delete: '<?php echo $this->createUrl('/book/delete'); ?>',
        load: '<?php echo $this->createUrl('/book/load'); ?>'
      };

      function withParams(url, params) {
        const hasQuery = url.indexOf('?') !== -1;
        const prefix = hasQuery ? '&' : '?';
        return url + prefix + $.param(params);
      }

      function setModeCreate() {
        editingId = null;
        $modeLabel.text('Создание');
        $cancelBtn.addClass('hidden');
        $form[0].reset();
        $('#book_id').val('');
        $errors.text('');
        $status.text('');
      }

      function setModeEdit(bookId) {
        editingId = bookId;
        $modeLabel.text('Редактирование');
        $cancelBtn.removeClass('hidden');
        $errors.text('');
        $status.text('Заполните форму и сохраните изменения.');
      }

      function renderErrors(resp) {
        if (resp && resp.errors) {
          const list = [];
          $.each(resp.errors, function(field, msgs) {
            list.push(msgs.join(', '));
          });
          $errors.text(list.join(' · '));
        } else if (resp && resp.message) {
          $errors.text(resp.message);
        } else {
          $errors.text('Произошла ошибка.');
        }
      }

      function handleAjaxError(xhr, action) {
        if (xhr && xhr.status === 401) {
          $errors.text('Сессия истекла или вы не авторизованы. Перезайдите и повторите.');
          return;
        }

        const txt = (xhr && xhr.responseText) ? xhr.responseText : '';
        if (txt.indexOf('login-form') !== -1 || txt.indexOf('LoginForm') !== -1) {
          $errors.text('Сессия истекла или вы не авторизованы. Перезайдите и повторите действие.');
        } else {
          $errors.text('Сетевая ошибка при ' + action + '. Попробуйте ещё раз.');
        }
      }

      function bindRowEvents($row) {
        $row.find('.js-book-edit').off('click').on('click', function(e) {
          e.preventDefault();
          const id = $(this).data('book-id');
          $.getJSON(urls.load, {id}, function(resp) {
            if (!resp.success) {
              alert(resp.message || 'Книга не найдена');
              return;
            }
            setModeEdit(id);
            $('#book_id').val(id);
            $('#book_title').val(resp.book.title);
            $('#book_year').val(resp.book.year);
            $('#book_isbn').val(resp.book.isbn);
            $('#book_description').val(resp.book.description);
            $('#book_authors').val(resp.book.authors.map(String));
          });
        });

        $row.find('.js-book-delete').off('click').on('click', function(e) {
          e.preventDefault();
          const id = $(this).data('book-id');
          if (!confirm('Удалить книгу?')) {
            return;
          }
          $.ajax({
            url: withParams(urls.delete, {id}),
            method: 'POST',
            dataType: 'json',
            success: function(resp) {
              if (resp.success) {
                $('#book-row-' + id).remove();
                if (editingId === id) {
                  setModeCreate();
                }
              } else {
                alert(resp.message || 'Не удалось удалить книгу');
              }
            },
            error: function(xhr) {
              handleAjaxError(xhr, 'удалении');
            }
          });
        });
      }

      // Инициализируем существующие строки
      bindRowEvents($tableBody);

      $cancelBtn.on('click', function() {
        setModeCreate();
      });

      $form.on('submit', function(e) {
        e.preventDefault();
        $errors.text('');
        $status.text('Сохраняем...');

        const fd = new FormData(this);
        const url = editingId ? withParams(urls.update, {id: editingId}) : urls.create;

        $.ajax({
          url,
          method: 'POST',
          data: fd,
          processData: false,
          contentType: false,
          dataType: 'json',
          success: function(resp) {
            if (!resp.success) {
              renderErrors(resp);
              $status.text('');
              return;
            }

            const $row = $(resp.row);
            if (editingId) {
              $('#book-row-' + resp.id).replaceWith($row);
              setModeCreate();
            } else {
              $tableBody.prepend($row);
              $form[0].reset();
              $status.text('Книга добавлена.');
            }

            bindRowEvents($row);
          },
          error: function(xhr) {
            $status.text('');
            handleAjaxError(xhr, editingId ? 'сохранении' : 'создании');
          }
        });
      });
    })(jQuery);
  </script>
<?php endif; ?>
