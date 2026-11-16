<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SchoolSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_school_settings_page_is_displayed_for_authenticated_school_user(): void
    {
        $user = User::factory()->create();
        $school = School::factory()->create(['owner_user_id' => $user->id]);
        $user->forceFill(['school_id' => $school->id])->save();

        $response = $this
            ->actingAs($user)
            ->get('/pengaturan');

        $response->assertOk();
        $response->assertSee($school->name);
        $response->assertSee('Profil & Identitas', false);
        $response->assertSee('Tampilan & Tema', false);
        $response->assertSee('Akun & Keamanan', false);
        $response->assertSee('Langganan & Modul', false);
    }

    public function test_school_identity_can_be_updated(): void
    {
        $user = User::factory()->create();
        $school = School::factory()->create(['owner_user_id' => $user->id]);
        $user->forceFill(['school_id' => $school->id])->save();

        $response = $this
            ->actingAs($user)
            ->patch('/pengaturan/school', [
                'name' => 'SMAN 1 Nusantara Hebat',
                'npsn' => '20109988',
                'email' => 'info@sman1nusantara.sch.id',
                'phone' => '021-998877',
                'city' => 'Jakarta Selatan',
                'district' => 'Kebayoran Baru',
                'postal_code' => '12130',
                'address' => 'Jl. Pendidikan Bangsa No. 1',
                'level' => 'SMA / MA',
                'status' => 'Negeri',
                'headmaster' => 'Drs. H. Mulyadi, M.Pd.',
                'academic_year' => '2026/2027',
                'semester' => 'Ganjil',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('school.settings', ['tab' => 'profil']));

        $school->refresh();

        $this->assertSame('SMAN 1 Nusantara Hebat', $school->name);
        $this->assertSame('20109988', $school->npsn);
        $this->assertSame('info@sman1nusantara.sch.id', $school->email);
        $this->assertSame('Jakarta Selatan', $school->city);
        $this->assertSame('SMA / MA', $school->meta['level']);
        $this->assertSame('Negeri', $school->meta['status']);
        $this->assertSame('Drs. H. Mulyadi, M.Pd.', $school->meta['headmaster']);
    }

    public function test_school_logo_can_be_uploaded(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $school = School::factory()->create(['owner_user_id' => $user->id]);
        $user->forceFill(['school_id' => $school->id])->save();

        $file = UploadedFile::fake()->image('school_logo.png', 400, 400);

        $response = $this
            ->actingAs($user)
            ->post('/pengaturan/logo', [
                'logo' => $file,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('school.settings', ['tab' => 'profil']));

        $school->refresh();

        $this->assertNotNull($school->logo_path);
        Storage::disk('public')->assertExists($school->logo_path);
    }

    public function test_school_logo_rejects_svg_format(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $school = School::factory()->create(['owner_user_id' => $user->id]);
        $user->forceFill(['school_id' => $school->id])->save();

        $svgContent = '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>';
        $file = UploadedFile::fake()->createWithContent('logo.svg', $svgContent);

        $response = $this
            ->actingAs($user)
            ->post('/pengaturan/logo', [
                'logo' => $file,
            ]);

        $response->assertSessionHasErrors('logo');
    }

    public function test_school_admin_profile_can_be_updated(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Admin Name',
            'email' => 'oldadmin@school.id',
        ]);
        $school = School::factory()->create(['owner_user_id' => $user->id]);
        $user->forceFill(['school_id' => $school->id])->save();

        $response = $this
            ->actingAs($user)
            ->patch('/pengaturan/profile', [
                'name' => 'Admin Baru Sekolah',
                'email' => 'adminbaru@school.id',
                'phone' => '081234567890',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('school.settings', ['tab' => 'akun']));

        $user->refresh();

        $this->assertSame('Admin Baru Sekolah', $user->name);
        $this->assertSame('adminbaru@school.id', $user->email);
        $this->assertSame('081234567890', $user->phone);
    }

    public function test_school_admin_password_can_be_updated(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password-123'),
        ]);
        $school = School::factory()->create(['owner_user_id' => $user->id]);
        $user->forceFill(['school_id' => $school->id])->save();

        $response = $this
            ->actingAs($user)
            ->patch('/pengaturan/password', [
                'current_password' => 'old-password-123',
                'password' => 'new-secure-password-456',
                'password_confirmation' => 'new-secure-password-456',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('school.settings', ['tab' => 'akun']));

        $user->refresh();

        $this->assertTrue(Hash::check('new-secure-password-456', $user->password));
    }

    public function test_school_can_add_team_member(): void
    {
        $user = User::factory()->create();
        $school = School::factory()->create(['owner_user_id' => $user->id]);
        $user->forceFill(['school_id' => $school->id])->save();

        $response = $this
            ->actingAs($user)
            ->post('/pengaturan/tim', [
                'name' => 'Budi Santoso, S.Pd.',
                'email' => 'budi.santoso@sekolah.sch.id',
                'role' => 'teacher',
                'phone' => '081234567890',
                'password' => 'password123',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('school.settings', ['tab' => 'tim']));

        $this->assertDatabaseHas('users', [
            'school_id' => $school->id,
            'email' => 'budi.santoso@sekolah.sch.id',
            'role' => 'teacher',
        ]);
    }

    public function test_school_can_delete_team_member_but_not_owner(): void
    {
        $owner = User::factory()->create();
        $school = School::factory()->create(['owner_user_id' => $owner->id]);
        $owner->forceFill(['school_id' => $school->id])->save();

        $member = User::factory()->create([
            'school_id' => $school->id,
            'role' => 'teacher',
        ]);

        // Attempt to delete owner (should be prevented)
        $responseOwner = $this
            ->actingAs($owner)
            ->delete("/pengaturan/tim/{$owner->id}");

        $responseOwner->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $owner->id]);

        // Delete member (should succeed)
        $responseMember = $this
            ->actingAs($owner)
            ->delete("/pengaturan/tim/{$member->id}");

        $responseMember->assertSessionHas('status');
        $this->assertDatabaseMissing('users', ['id' => $member->id]);
    }

    public function test_school_can_download_dapodik_templates(): void
    {
        $user = User::factory()->create();
        $school = School::factory()->create(['owner_user_id' => $user->id]);
        $user->forceFill(['school_id' => $school->id])->save();

        $responseSiswa = $this
            ->actingAs($user)
            ->get('/pengaturan/template-dapodik/siswa');

        $responseSiswa->assertOk();
        $this->assertStringContainsString('template_dapodik_siswa.csv', (string) $responseSiswa->headers->get('content-disposition'));
        $this->assertStringContainsString('NISN,NIK,Nama Siswa', $responseSiswa->streamedContent());

        $responseGuru = $this
            ->actingAs($user)
            ->get('/pengaturan/template-dapodik/guru');

        $responseGuru->assertOk();
        $this->assertStringContainsString('template_dapodik_guru.csv', (string) $responseGuru->headers->get('content-disposition'));
        $this->assertStringContainsString('NUPTK,NIP,Nama Lengkap', $responseGuru->streamedContent());
    }
}
