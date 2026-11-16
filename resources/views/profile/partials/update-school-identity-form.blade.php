<section>
    <header>
        <h2 class="text-lg font-bold text-ink">
            Identitas sekolah
        </h2>

        <p class="mt-1 text-sm text-muted">
            Perbarui informasi identitas sekolah Anda.
        </p>
    </header>

    <form method="post" action="{{ route('profile.school.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <x-input-label for="school_name" value="Nama sekolah" />
                <x-text-input id="school_name" name="school_name" type="text" class="mt-1 block w-full" :value="old('school_name', $school->name)" required autofocus />
                <x-input-error class="mt-2" :messages="$errors->get('school_name')" />
            </div>

            <div>
                <x-input-label for="school_npsn" value="NPSN" />
                <x-text-input id="school_npsn" name="school_npsn" type="text" class="mt-1 block w-full" :value="old('school_npsn', $school->npsn)" maxlength="8" pattern="\d{8}" inputmode="numeric" />
                <x-input-error class="mt-2" :messages="$errors->get('school_npsn')" />
                <p class="mt-1 text-xs text-muted">Opsional. 8 digit kode unik sekolah.</p>
            </div>

            <div>
                <x-input-label for="school_email" value="Email sekolah" />
                <x-text-input id="school_email" name="school_email" type="email" class="mt-1 block w-full" :value="old('school_email', $school->email)" required />
                <x-input-error class="mt-2" :messages="$errors->get('school_email')" />
            </div>

            <div>
                <x-input-label for="school_phone" value="Telepon sekolah" />
                <x-text-input id="school_phone" name="school_phone" type="text" class="mt-1 block w-full" :value="old('school_phone', $school->phone)" />
                <x-input-error class="mt-2" :messages="$errors->get('school_phone')" />
            </div>

            <div>
                <x-input-label for="school_city" value="Kota" />
                <x-text-input id="school_city" name="school_city" type="text" class="mt-1 block w-full" :value="old('school_city', $school->city)" />
                <x-input-error class="mt-2" :messages="$errors->get('school_city')" />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Simpan</x-primary-button>

            @if (session('status') === 'school-identity-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-muted"
                >Tersimpan.</p>
            @endif
        </div>
    </form>
</section>
