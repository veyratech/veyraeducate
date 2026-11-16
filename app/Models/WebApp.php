<?php

namespace App\Models;

use App\Models\Traits\Auditable;
use Database\Factories\WebAppFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class WebApp extends Model
{
    /** @use HasFactory<WebAppFactory> */
    use Auditable, HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'description',
        'category',
        'icon',
        'access_url',
        'version',
        'source_file',
        'file_count',
        'enabled',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'file_count' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function schools(): BelongsToMany
    {
        return $this->belongsToMany(School::class, 'school_web_apps')
            ->withTimestamps();
    }

    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(Plan::class, 'plan_web_apps')
            ->withTimestamps();
    }

    public function guides(): HasMany
    {
        return $this->hasMany(AppGuide::class, 'web_app_id');
    }

    public function isAccessibleByPlan(?Plan $plan): bool
    {
        // If app has no plan restrictions, any plan can access it
        if (! $this->plans()->exists()) {
            return true;
        }

        if (! $plan) {
            return false;
        }

        return $this->plans()->where('plans.id', $plan->id)->exists();
    }

    /**
     * URL used by schools to open this app — always routed through SSO so users log in via Veyra Educate first.
     */
    public function accessLink(): string
    {
        return route('sso.launch', $this->slug);
    }

    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public static function iconOptions(): array
    {
        return [
            'book-open' => 'Buku Terbuka',
            'calculator' => 'Kalkulator',
            'chart-bar' => 'Grafik',
            'chat-bubble-left-right' => 'Chat',
            'clipboard' => 'Clipboard',
            'cog' => 'Pengaturan',
            'document-text' => 'Dokumen',
            'academic-cap' => 'Akademik',
            'user-group' => 'Pengguna',
            'wallet' => 'Keuangan',
            'bookmark' => 'Bookmark',
            'calendar' => 'Kalender',
            'chart-pie' => 'Pie Chart',
            'cube' => 'Kube',
            'desktop-computer' => 'Desktop',
            'exclamation-circle' => 'Perhatian',
            'gift' => 'Hadiah',
            'home' => 'Beranda',
            'inbox' => 'Inbox',
            'light-bulb' => 'Lampu',
            'lock-closed' => 'Kunci',
            'mail' => 'Surat',
            'map' => 'Peta',
            'moon' => 'Bulan',
            'newspaper' => 'Koran',
            'pencil' => 'Pensil',
            'phone' => 'Telepon',
            'photo' => 'Foto',
            'plug' => 'Stopkontak',
            'refresh' => 'Segarkan',
            'server' => 'Server',
            'shield-check' => 'Perisai',
            'star' => 'Bintang',
            'switch-horizontal' => 'Switch',
            'tag' => 'Tag',
            'template' => 'Template',
            'trophy' => 'Piala',
            'truck' => 'Truk',
            'user' => 'Pengguna',
            'video' => 'Video',
            'wifi' => 'WiFi',
            'x-circle' => 'Bulat X',
            'graduation-cap' => 'Wisuda',
            'app' => 'Aplikasi',
        ];
    }

    public static function categoryOptions(): array
    {
        return [
            'akademik' => 'Akademik',
            'keuangan' => 'Keuangan',
            'perpustakaan' => 'Perpustakaan',
            'kesiswaan' => 'Kesiswaan',
            'kepegawaian' => 'Kepegawaian',
            'ekskul' => 'Ekstrakurikuler',
            'komunikasi' => 'Komunikasi',
            'umum' => 'Umum',
            'lainnya' => 'Lainnya',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('school_app.list'));
        static::deleted(fn () => Cache::forget('school_app.list'));
    }
}
