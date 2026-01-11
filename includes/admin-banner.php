<?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>
    <div class="alert alert-warning text-center rounded-0 mb-0">
        <i class="bi bi-shield-lock-fill me-2"></i>
        You are browsing CineGrid as an Administrator.
    </div>
<?php endif; ?>
