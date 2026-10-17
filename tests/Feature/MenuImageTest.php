<?php

namespace Tests\Feature;

use App\Models\MenuItem;
use App\Models\MenuItemImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MenuImageTest extends TestCase
{
    use RefreshDatabase;

    private function png(): string
    {
        return base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+a9HcAAAAASUVORK5CYII=');
    }

    private function upload(string $contents, string $name = 'burger.png'): UploadedFile
    {
        return UploadedFile::fake()->createWithContent($name, $contents);
    }

    private function fields(array $changes = []): array
    {
        return array_merge(['name' => 'Burger', 'category' => 'Burgers', 'price' => 250,
            'is_available' => 1, 'preparation_time' => 5], $changes);
    }

    public function test_uploaded_photo_is_stored_in_database_and_served_without_a_local_file(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create(['role' => 'owner']));
        $this->post(route('menu.store'), $this->fields(['image' => $this->upload($this->png())]))
            ->assertSessionHasNoErrors()->assertRedirect(route('menu.index'));
        $item = MenuItem::sole();
        $this->assertSame('db:'.hash('sha256', $this->png()), $item->image);
        $this->assertSame($this->png(), base64_decode(MenuItemImage::sole()->contents));
        $this->assertSame([], Storage::disk('public')->allFiles());
        auth()->logout();
        $response = $this->get($item->image_url)->assertOk()->assertHeader('Content-Type', 'image/png')
            ->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->assertSame($this->png(), $response->getContent());
        $this->get($item->image_url, ['If-None-Match' => $response->headers->get('ETag')])->assertStatus(304);
    }

    public function test_menu_and_register_use_the_database_photo_url(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'owner']));
        $this->post(route('menu.store'), $this->fields(['image' => $this->upload($this->png())]))->assertSessionHasNoErrors();
        $url = MenuItem::sole()->image_url;
        $this->get(route('menu.index'))->assertOk()->assertSee($url);
        $this->get(route('orders.create'))->assertOk()->assertSee($url);
    }

    public function test_replacement_changes_the_url_and_invalidates_the_previous_version(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'manager']));
        $this->post(route('menu.store'), $this->fields(['image' => $this->upload($this->png())]))->assertSessionHasNoErrors();
        $item = MenuItem::sole();
        $oldUrl = $item->image_url;
        $gif = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
        $this->put(route('menu.update', $item), $this->fields(['image' => $this->upload($gif, 'burger.gif')]))->assertSessionHasNoErrors();
        $this->assertDatabaseCount('menu_item_images', 1);
        $this->get($oldUrl)->assertNotFound();
        $this->assertSame($gif, $this->get($item->fresh()->image_url)->assertOk()->assertHeader('Content-Type', 'image/gif')->getContent());
    }

    public function test_editing_without_a_photo_keeps_the_existing_photo_and_deletion_cascades(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'owner']));
        $this->post(route('menu.store'), $this->fields(['image' => $this->upload($this->png())]))->assertSessionHasNoErrors();
        $item = MenuItem::sole();
        $url = $item->image_url;
        $this->put(route('menu.update', $item), $this->fields(['name' => 'Updated burger']))->assertSessionHasNoErrors();
        $this->assertSame($url, $item->fresh()->image_url);
        $this->get($url)->assertOk();
        $this->delete(route('menu.destroy', $item))->assertRedirect(route('menu.index'));
        $this->assertDatabaseCount('menu_item_images', 0);
        $this->get($url)->assertNotFound();
    }

    public function test_oversized_upload_cannot_replace_an_existing_photo(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'owner']));
        $this->post(route('menu.store'), $this->fields(['image' => $this->upload($this->png())]))->assertSessionHasNoErrors();
        $item = MenuItem::sole();
        $oldImage = $item->image;
        $this->put(route('menu.update', $item), $this->fields([
            'image' => $this->upload($this->png().str_repeat('x', 2048 * 1024)),
        ]))->assertSessionHasErrors('image');
        $this->assertSame($oldImage, $item->fresh()->image);
        $this->get($item->image_url)->assertOk();
    }
}
