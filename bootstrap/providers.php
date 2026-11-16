<?php

use Illuminate\Auth\AuthServiceProvider;
use Illuminate\Broadcasting\BroadcastServiceProvider;
use Illuminate\Bus\BusServiceProvider;
use Illuminate\Cache\CacheServiceProvider;
use Illuminate\Foundation\Providers\ConsoleSupportServiceProvider;
use Illuminate\Concurrency\ConcurrencyServiceProvider;
use Illuminate\Cookie\CookieServiceProvider;
use Illuminate\Database\DatabaseServiceProvider;
use Illuminate\Encryption\EncryptionServiceProvider;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\Image\ImageServiceProvider;
use Illuminate\Foundation\Providers\FoundationServiceProvider;
use Illuminate\Hashing\HashServiceProvider;
use Illuminate\Mail\MailServiceProvider;
use Illuminate\Notifications\NotificationServiceProvider;
use Illuminate\Pagination\PaginationServiceProvider;
use Illuminate\Auth\Passwords\PasswordResetServiceProvider;
use Illuminate\Pipeline\PipelineServiceProvider;
use Illuminate\Queue\QueueServiceProvider;
use Illuminate\Redis\RedisServiceProvider;
use Illuminate\Session\SessionServiceProvider;
use Illuminate\Translation\TranslationServiceProvider;
use Illuminate\Validation\ValidationServiceProvider;
use Illuminate\View\ViewServiceProvider;
use App\Providers\AppServiceProvider;
use App\Providers\SchoolScopeServiceProvider;
use App\Providers\SchoolAppViewServiceProvider;

return [
    // Core framework providers (required for Laravel 13 without full vendor discovery)
    AuthServiceProvider::class,
    BroadcastServiceProvider::class,
    BusServiceProvider::class,
    CacheServiceProvider::class,
    ConsoleSupportServiceProvider::class,
    ConcurrencyServiceProvider::class,
    CookieServiceProvider::class,
    DatabaseServiceProvider::class,
    EncryptionServiceProvider::class,
    FilesystemServiceProvider::class,
    ImageServiceProvider::class,
    FoundationServiceProvider::class,
    HashServiceProvider::class,
    MailServiceProvider::class,
    NotificationServiceProvider::class,
    PaginationServiceProvider::class,
    PasswordResetServiceProvider::class,
    PipelineServiceProvider::class,
    QueueServiceProvider::class,
    RedisServiceProvider::class,
    SessionServiceProvider::class,
    TranslationServiceProvider::class,
    ValidationServiceProvider::class,
    ViewServiceProvider::class,

    // App-specific providers
    AppServiceProvider::class,
    SchoolScopeServiceProvider::class,
    SchoolAppViewServiceProvider::class,
];
