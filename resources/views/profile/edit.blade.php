<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-ink">
            Profil
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="container-site space-y-6 dark:text-slate-200">
            <div class="card p-4 sm:p-8 dark:bg-slate-850 dark:border-slate-700/50">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="card p-4 sm:p-8 dark:bg-slate-850 dark:border-slate-700/50">
                <div class="max-w-xl">
                    @include('profile.partials.update-school-identity-form')
                </div>
            </div>

            <div class="card p-4 sm:p-8 dark:bg-slate-850 dark:border-slate-700/50">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="card p-4 sm:p-8 dark:bg-slate-850 dark:border-slate-700/50">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
