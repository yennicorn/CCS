<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthRoleRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_redirects_to_password_change_form_after_login_when_forced(): void
    {
        User::query()->create([
            'full_name' => 'Super Admin User',
            'username' => 'superadminuser',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('Password123'),
            'role' => 'super_admin',
            'is_active' => true,
            'force_password_change' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'superadmin@example.com',
            'password' => 'Password123',
        ]);

        $response->assertRedirect(route('password.change.form'));
    }

    public function test_admin_redirects_to_password_change_form_after_login_when_forced(): void
    {
        User::query()->create([
            'full_name' => 'Admin User',
            'username' => 'adminuser',
            'email' => 'admin@example.com',
            'password' => Hash::make('Password123'),
            'role' => 'admin',
            'is_active' => true,
            'force_password_change' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'adminuser',
            'password' => 'Password123',
        ]);

        $response->assertRedirect(route('password.change.form'));
    }

    public function test_super_admin_must_change_password_before_accessing_dashboard(): void
    {
        $user = User::query()->create([
            'full_name' => 'Super Admin User',
            'username' => 'superadminuser',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('Password123'),
            'role' => 'super_admin',
            'is_active' => true,
            'force_password_change' => true,
        ]);

        $this->post('/login', [
            'email' => 'superadmin@example.com',
            'password' => 'Password123',
        ])->assertRedirect(route('password.change.form'));

        $this->get(route('master.dashboard'))->assertRedirect(route('password.change.form'));

        $this->post(route('password.change.update'), [
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ])->assertRedirect(route('master.dashboard'));

        $user->refresh();
        $this->assertFalse((bool) $user->force_password_change);
    }

    public function test_admin_must_change_password_before_accessing_dashboard(): void
    {
        $user = User::query()->create([
            'full_name' => 'Admin User',
            'username' => 'adminuser',
            'email' => 'admin@example.com',
            'password' => Hash::make('Password123'),
            'role' => 'admin',
            'is_active' => true,
            'force_password_change' => true,
        ]);

        $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'Password123',
        ])->assertRedirect(route('password.change.form'));

        $this->get(route('admin.dashboard'))->assertRedirect(route('password.change.form'));

        $this->post(route('password.change.update'), [
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ])->assertRedirect(route('admin.dashboard'));

        $user->refresh();
        $this->assertFalse((bool) $user->force_password_change);
    }

    public function test_parent_and_student_redirect_to_parent_student_homepage_after_login(): void
    {
        foreach (['parent', 'student'] as $role) {
            User::query()->create([
                'full_name' => ucfirst($role).' User',
                'username' => $role.'user',
                'email' => $role.'@example.com',
                'password' => Hash::make('Password123'),
                'role' => $role,
                'is_active' => true,
                'force_password_change' => false,
            ]);

            $response = $this->post('/login', [
                'email' => $role.'@example.com',
                'password' => 'Password123',
            ]);

            $response->assertRedirect(route('homepage.feed'));
            $this->post('/logout');
        }
    }
}
