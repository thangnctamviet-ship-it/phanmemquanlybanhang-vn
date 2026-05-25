<?php $footer_env = function_exists('env_load') ? env_load() : (file_exists(dirname(__DIR__, 2).'/.env') ? (parse_ini_file(dirname(__DIR__, 2).'/.env') ?: []) : []); ?>
<footer class="bg-white border-t mt-16">
  <div class="max-w-6xl mx-auto px-4 py-6 text-sm text-slate-500 text-center">
    © <?= date('Y') ?> Quản lý bán hàng SaaS
    · Hỗ trợ: <?= htmlspecialchars($footer_env['OWNER_EMAIL'] ?? 'support@quanlybanhang.shop') ?>
    · CK: <?= htmlspecialchars($footer_env['BANK_NAME'] ?? '') ?> <?= htmlspecialchars($footer_env['BANK_ACCOUNT'] ?? '') ?> <?= htmlspecialchars($footer_env['BANK_HOLDER'] ?? '') ?>
  </div>
</footer>
</body></html>
