<x-admin-layout title="Edit {{ $school->name }} — Admin {{ config('app.name') }}">
    <x-slot name="header">
        <div>
            <p class="eyebrow">Edit sekolah</p>
            <h2 class="mt-1 text-xl font-bold leading-tight text-ink dark:text-slate-100">{{ $school->name }}</h2>
            <p class="mt-1 text-sm text-muted dark:text-slate-400"><a href="{{ route('admin.schools.show', $school) }}" class="link-muted">← Kembali ke detail</a></p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="container-site max-w-2xl">
            <form method="POST" action="{{ route('admin.schools.update', $school) }}" class="card-minimal space-y-5 p-6 stagger-in">
                @csrf
                @method('PUT')

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2 form-group-minimal">
                        <x-input-label for="name" value="Nama sekolah" />
                        <x-text-input id="name" class="mt-1 form-input-minimal" name="name" :value="old('name', $school->name)" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="form-group-minimal">
                        <x-input-label for="npsn" value="NPSN" />
                        <x-text-input id="npsn" class="mt-1 form-input-minimal" name="npsn" :value="old('npsn', $school->npsn)" />
                    </div>

                    <div class="form-group-minimal">
                        <x-input-label for="email" value="Email sekolah" />
                        <x-text-input id="email" class="mt-1 form-input-minimal" type="email" name="email" :value="old('email', $school->email)" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="form-group-minimal">
                        <x-input-label for="phone" value="Telepon" />
                        <x-text-input id="phone" class="mt-1 form-input-minimal" name="phone" :value="old('phone', $school->phone)" />
                    </div>

                    <div class="form-group-minimal">
                        <x-input-label for="city" value="Kota" />
                        <x-text-input id="city" class="mt-1 form-input-minimal" name="city" :value="old('city', $school->city)" />
                    </div>

                    <div class="form-group-minimal">
                        <x-input-label for="district" value="Kecamatan" />
                        <x-text-input id="district" class="mt-1 form-input-minimal" name="district" :value="old('district', $school->district)" />
                    </div>

                    <div class="form-group-minimal">
                        <x-input-label for="postal_code" value="Kode Pos" />
                        <x-text-input id="postal_code" class="mt-1 form-input-minimal" name="postal_code" :value="old('postal_code', $school->postal_code)" maxlength="10" />
                    </div>

                    <div class="form-group-minimal">
                        <x-input-label for="headmaster" value="Nama Kepala Sekolah" />
                        <x-text-input id="headmaster" class="mt-1 form-input-minimal" name="headmaster" :value="old('headmaster', $school->headmaster)" placeholder="Nama lengkap & gelar" />
                    </div>

                    <div class="sm:col-span-2 form-group-minimal">
                        <x-input-label for="address" value="Alamat Lengkap Sekolah" />
                        <x-text-input id="address" class="mt-1 form-input-minimal" name="address" :value="old('address', $school->address)" placeholder="Jl. Pendidikan No. 1..." />
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('admin.schools.show', $school) }}" class="btn-minimal-secondary">Batal</a>
                    <x-primary-button class="btn-minimal-primary">Simpan perubahan</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
