<?php

namespace Tests\Feature;

use App\Filament\Resources\Categories\Pages\ManageCategories;
use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Filament\Resources\Tags\Pages\ManageTags;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class BlogAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_publish_and_edit_an_article_with_tags(): void
    {
        $this->freezeTime();
        $admin = User::factory()->create(['email' => 'editor@gmail.com']);
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();
        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(CreatePost::class)->fillForm([
            'category_id' => $category->id, 'user_id' => $admin->id,
            'title' => 'My first article', 'slug' => 'my-first-article',
            'content' => 'Hello readers!', 'tags' => [$tag->id],
            'is_published' => true, 'is_featured' => true, 'published_at' => now()->toDateTimeString(),
        ])->call('create')->assertHasNoFormErrors();

        $this->assertDatabaseHas('posts', ['slug' => 'my-first-article', 'user_id' => $admin->id, 'is_published' => true]);
        $post = Post::where('slug', 'my-first-article')->firstOrFail();
        $this->assertDatabaseHas('post_tag', ['post_id' => $post->id, 'tag_id' => $tag->id]);
        $this->get('/blog/my-first-article')->assertOk()->assertSee('Hello readers!');

        Livewire::test(EditPost::class, ['record' => $post->id])->fillForm(['is_published' => false, 'tags' => []])
            ->call('save')->assertHasNoFormErrors();

        $this->assertDatabaseHas('posts', ['id' => $post->id, 'is_published' => false]);
        $this->assertDatabaseMissing('post_tag', ['post_id' => $post->id, 'tag_id' => $tag->id]);
        $this->get('/blog/my-first-article')->assertNotFound();
    }

    public function test_admin_can_manage_categories_and_tags(): void
    {
        $this->actingAs(User::factory()->create(['email' => 'editor@gmail.com']));
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(ManageCategories::class)->callAction('create', data: [
            'name' => 'Tutorials', 'slug' => 'tutorials', 'description' => 'Learn something new.',
        ])->assertHasNoFormErrors();
        Livewire::test(ManageTags::class)->callAction('create', data: [
            'name' => 'Laravel', 'slug' => 'laravel',
        ])->assertHasNoFormErrors();

        $this->assertDatabaseHas('categories', ['name' => 'Tutorials', 'slug' => 'tutorials']);
        $this->assertDatabaseHas('tags', ['name' => 'Laravel', 'slug' => 'laravel']);
    }

    public function test_admin_form_rejects_missing_fields_and_duplicate_or_invalid_slugs(): void
    {
        $this->actingAs(User::factory()->create(['email' => 'editor@gmail.com']));
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $post = Post::factory()->create(['slug' => 'existing']);

        Livewire::test(CreatePost::class)->fillForm([
            'title' => null, 'slug' => 'existing', 'content' => null,
            'category_id' => null, 'user_id' => null,
        ])->call('create')->assertHasFormErrors([
            'title' => 'required', 'slug' => 'unique', 'content' => 'required',
            'category_id' => 'required', 'user_id' => 'required',
        ]);
        Livewire::test(CreatePost::class)->fillForm(['slug' => 'Bad / slug'])->call('create')->assertHasFormErrors(['slug' => 'regex']);
        Livewire::test(EditPost::class, ['record' => $post->id])->fillForm(['is_published' => true, 'published_at' => null])
            ->call('save')->assertHasFormErrors(['published_at' => 'required_if']);

        $this->assertDatabaseCount('posts', 1);
    }

    public function test_blog_admin_routes_require_an_approved_account(): void
    {
        $this->get('/admin/posts')->assertRedirect('/admin/login');
        $this->actingAs(User::factory()->create(['email' => 'reader@example.com']));
        foreach (['/admin/posts', '/admin/posts/create', '/admin/categories', '/admin/tags'] as $path) {
            $this->get($path)->assertForbidden();
        }
    }

    public function test_admin_can_upload_a_featured_image_and_delete_the_article(): void
    {
        $this->freezeTime();
        Storage::fake('public');
        $admin = User::factory()->create(['email' => 'editor@gmail.com']);
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();
        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::test(CreatePost::class)->fillForm([
            'category_id' => $category->id, 'user_id' => $admin->id,
            'title' => 'An illustrated article', 'slug' => 'illustrated',
            'content' => 'A story with a cover.', 'tags' => [$tag->id],
            'featured_image' => UploadedFile::fake()->image('cover.png'),
            'is_published' => true, 'published_at' => now()->toDateTimeString(),
        ])->call('create')->assertHasNoFormErrors();

        $post = Post::where('slug', 'illustrated')->firstOrFail();
        Storage::disk('public')->assertExists($post->featured_image);
        $this->get('/blog/illustrated')->assertOk()->assertSee('/storage/blog/');

        Livewire::test(EditPost::class, ['record' => $post->id])->callAction('delete');

        $this->assertModelMissing($post);
        $this->assertDatabaseMissing('post_tag', ['post_id' => $post->id]);
        $this->get('/blog/illustrated')->assertNotFound();
    }
}
