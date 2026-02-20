<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="language" content="en">

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection">
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print">
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection">
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css">
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css">
	<script src="https://cdn.tailwindcss.com"></script>

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body class="min-h-screen bg-slate-800 text-slate-100">
<div class="relative">
	<header class="border-b border-white/10 backdrop-blur">
		<div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6">
			<div class="flex items-center gap-3">
				<div id="logo" class="text-lg font-semibold tracking-tight"><?php echo CHtml::encode(Yii::app()->name); ?></div>
				<?php echo CHtml::link('Каталог', array('/book/index'), array(
					'class'=>'inline-flex items-center rounded-lg border border-sky-400/30 bg-white/5 px-3 py-1.5 text-sm font-medium text-sky-200 hover:bg-white/10 hover:border-sky-400/50 focus:outline-none focus:ring-2 focus:ring-sky-400/40'
				)); ?>
			</div>
			<?php if (Yii::app()->user->isGuest): ?>
				<?php echo CHtml::link('Войти', array('/auth/login'), array(
					'class'=>'inline-flex items-center rounded-lg bg-white/5 px-4 py-2 text-sm font-medium text-slate-100 hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white/20'
				)); ?>
			<?php else: ?>
				<?php echo CHtml::link('Выход ('.Yii::app()->user->name.')', array('/site/logout'), array(
					'class'=>'inline-flex items-center rounded-lg bg-white/5 px-4 py-2 text-sm font-medium text-slate-100 hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white/20'
				)); ?>
			<?php endif; ?>
		</div>
	</header>

	<main class="mx-auto max-w-7xl px-4 py-8 sm:px-6">
		<?php if(isset($this->breadcrumbs)):?>
			<nav class="mb-6 text-sm text-slate-300">
				<?php $this->widget('zii.widgets.CBreadcrumbs', array(
					'links'=>$this->breadcrumbs,
					'htmlOptions'=>array('class'=>'flex items-center gap-2'),
					'separator'=>'<span class="text-slate-500">›</span>',
				)); ?>
			</nav>
		<?php endif?>

		<div class="rounded-xl border border-white/5 bg-slate-900/80 p-4 sm:p-6">
			<?php echo $content; ?>
		</div>

		<footer class="mt-10 text-sm text-slate-400">
			<p>Copyright &copy; <?php echo date('Y'); ?> by My Company. All Rights Reserved.</p>
			<p class="mt-1"><?php echo Yii::powered(); ?></p>
		</footer>
	</main>
</div>
</body>
</html>
