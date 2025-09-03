<?php

return [
    App\Providers\AppServiceProvider::class,
    OwenIt\Auditing\AuditingServiceProvider::class, // ← أضف دي لو عامل dont-discover
];
