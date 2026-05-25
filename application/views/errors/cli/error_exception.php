<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

Đã gặp ngoại lệ chưa được xử lý

Loại:        <?php echo get_class($exception), "\n"; ?>
Thông báo:     <?php echo $message, "\n"; ?>
Tên file:    <?php echo $exception->getFile(), "\n"; ?>
Số dòng: <?php echo $exception->getLine(); ?>

<?php if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE === TRUE): ?>

Dấu vết lỗi:
<?php	foreach ($exception->getTrace() as $error): ?>
<?php		if (isset($error['file']) && strpos($error['file'], realpath(BASEPATH)) !== 0): ?>
	File: <?php echo $error['file'], "\n"; ?>
	Line: <?php echo $error['line'], "\n"; ?>
	Function: <?php echo $error['function'], "\n\n"; ?>
<?php		endif ?>
<?php	endforeach ?>

<?php endif ?>
