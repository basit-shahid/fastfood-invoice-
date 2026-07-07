<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\MenuItem;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GuestAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Prevent collisions from dirty database states in local test runner
        User::where('email', 'guest@fastfood.com')->delete();
    }

    /**
     * Test guest user can login via the guest login route without OTP.
     */
    public function test_guest_can_login_without_otp(): void
    {
        $response = $this->withoutMiddleware([
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
        ])->post('/login/guest');

        $response->assertRedirect(route('owner.dashboard'));
        $this->assertAuthenticated();

        $user = auth()->user();
        $this->assertEquals('guest', $user->role);
        $this->assertEquals('guest@fastfood.com', $user->email);
    }

    /**
     * Test guest user can access various screens/dashboards.
     */
    public function test_guest_can_access_general_dashboards(): void
    {
        $guest = User::factory()->create(['role' => 'guest', 'is_active' => true]);
        $this->actingAs($guest);

        $this->get(route('owner.dashboard'))->assertStatus(200);
        $this->get(route('manager.dashboard'))->assertStatus(200);
        $this->get(route('cashier.dashboard'))->assertStatus(200);
        $this->get(route('menu.index'))->assertStatus(200);
    }

    /**
     * Test guest user is blocked from managing staff (403 Forbidden).
     */
    public function test_guest_cannot_access_staff_management(): void
    {
        $guest = User::factory()->create(['role' => 'guest', 'is_active' => true]);
        $this->actingAs($guest);

        // GET requests should be 403 (RoleMiddleware blocks, no CSRF needed)
        $this->get(route('staff.index'))->assertStatus(403);
        $this->get(route('staff.create'))->assertStatus(403);
    }

    /**
     * Test guest user cannot upload files during menu creation.
     */
    public function test_guest_cannot_upload_files_when_creating_menu_items(): void
    {
        $guest = User::factory()->create(['role' => 'guest', 'is_active' => true]);

        $response = $this->actingAs($guest)
            ->withoutMiddleware([
                \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
                \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            ])
            ->post(route('menu.store'), [
                'name' => 'Pizza Slice',
                'category' => 'Sides',
                'price' => 150.00,
                'image' => UploadedFile::fake()->create('photo.jpg', 100, 'image/jpeg'),
                'is_available' => 1,
                'preparation_time' => 5
            ]);

        $response->assertSessionHasErrors('image');
        $this->assertCount(0, MenuItem::where('name', 'Pizza Slice')->get());
    }

    /**
     * Test owner can update guest user status (e.g. enable/disable is_active) successfully.
     */
    public function test_owner_can_toggle_guest_account_active_status(): void
    {
        $owner = User::factory()->create(['role' => 'owner', 'is_active' => true]);
        $guest = User::factory()->create(['role' => 'guest', 'is_active' => true]);

        $this->actingAs($owner);

        // Disable guest
        $disableResponse = $this->withoutMiddleware([
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
        ])->put(route('staff.update', $guest), [
            'name' => 'Guest User Updated',
            'email' => $guest->email,
            'role' => 'guest',
            // 'is_active' not sent in checkbox means false
        ]);

        $disableResponse->assertSessionHasNoErrors();
        $disableResponse->assertRedirect(route('staff.index'));
        $guest->refresh();
        $this->assertFalse($guest->is_active);
        $this->assertEquals('Guest User Updated', $guest->name);

        // Re-enable guest
        $enableResponse = $this->withoutMiddleware([
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
        ])->put(route('staff.update', $guest), [
            'name' => 'Guest User Updated',
            'email' => $guest->email,
            'role' => 'guest',
            'is_active' => 1
        ]);

        $enableResponse->assertSessionHasNoErrors();
        $guest->refresh();
        $this->assertTrue($guest->is_active);
    }
}

