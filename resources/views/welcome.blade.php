<x-marketing-layout>
    @php
        try {
            $activeSections = (isset($sections) && $sections instanceof \Illuminate\Support\Collection && $sections->isNotEmpty())
                ? $sections
                : \App\Models\HomepageSection::getActiveOrdered();
        } catch (\Throwable) {
            $activeSections = new \Illuminate\Database\Eloquent\Collection();
        }
    @endphp

    @if ($activeSections->isNotEmpty())
        @foreach ($activeSections as $section)
            @includeIf('partials.homepage.' . $section->section_type, ['section' => $section])
        @endforeach
    @else
        {{-- Fallback: Jika tabel homepage_sections belum diisi data, tampilkan susunan bawaan --}}
        @include('partials.homepage.hero', ['section' => new \App\Models\HomepageSection(['section_type' => 'hero', 'content' => []])])
        @include('partials.homepage.trust_strip', ['section' => new \App\Models\HomepageSection(['section_type' => 'trust_strip', 'content' => []])])
        @include('partials.homepage.features', ['section' => new \App\Models\HomepageSection(['section_type' => 'features', 'content' => []])])
        @include('partials.homepage.app_showcase', ['section' => new \App\Models\HomepageSection(['section_type' => 'app_showcase', 'content' => []])])
        @include('partials.homepage.workflow', ['section' => new \App\Models\HomepageSection(['section_type' => 'workflow', 'content' => []])])
        @include('partials.homepage.ecosystem', ['section' => new \App\Models\HomepageSection(['section_type' => 'ecosystem', 'content' => []])])
        @include('partials.homepage.pricing', ['section' => new \App\Models\HomepageSection(['section_type' => 'pricing', 'content' => []])])
        @include('partials.homepage.testimonials', ['section' => new \App\Models\HomepageSection(['section_type' => 'testimonials', 'content' => []])])
        @include('partials.homepage.faq', ['section' => new \App\Models\HomepageSection(['section_type' => 'faq', 'content' => []])])
        @include('partials.homepage.cta', ['section' => new \App\Models\HomepageSection(['section_type' => 'cta', 'content' => []])])
    @endif
</x-marketing-layout>