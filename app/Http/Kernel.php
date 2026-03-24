<?php
// app/Http/Kernel.php

protected $routeMiddleware = [
    // ... other middleware
    'role' => \App\Http\Middleware\RoleMiddleware::class,
];