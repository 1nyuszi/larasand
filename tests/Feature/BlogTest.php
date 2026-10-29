<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_displays_only_published_articles_and_links(): void
    {
        $this->freezeTime();
        $post = Post::factory()->published()->create(['title' => 'Visible article', 'slug' => 'visible-article', 'is_featured' => true]);
        Post::factory()->create(['title' => 'Private draft', 'is_featured' => true]);
        Post::factory()->published()->create(['title' => 'Scheduled article', 'published_at' => now()->addDay(), 'is_featured' => true]);
        Post::factory()->create(['title' => 'Undated article', 'is_published' => true]);

        $this->get('/blog')->assertOk()->assertSee('Visible article')->assertSee('/blog/visible-article')
            ->assertSee($post->user->name)->assertSee($post->category->name)
            ->assertDontSee('Private draft')->assertDontSee('Scheduled article')->assertDontSee('Undated article');
    }

    public function test_article_renders_escaped_content_and_tag_links(): void
    {
        $this->freezeTime();
        $tag = Tag::factory()->create(['name' => 'Ideas', 'slug' => 'ideas']);
        $post = Post::factory()->published()->hasAttached($tag)->create([
            'slug' => 'safe-article', 'title' => '<script>bad title</script>',
            'content' => '<script>alert(1)</script>', 'excerpt' => '<img src=x onerror=alert(1)>',
        ]);

        $this->get('/blog/safe-article')->assertOk()->assertSee($post->title)->assertSee($post->excerpt)
            ->assertSee($post->content)->assertDontSee($post->content, false)->assertSee('/blog/tag/ideas');
    }

    public function test_drafts_scheduled_undated_and_missing_articles_return_not_found(): void
    {
        $this->freezeTime();
        Post::factory()->create(['slug' => 'draft']);
        Post::factory()->published()->create(['slug' => 'scheduled', 'published_at' => now()->addHour()]);
        Post::factory()->create(['slug' => 'undated', 'is_published' => true]);

        foreach (['draft', 'scheduled', 'undated', 'missing'] as $slug) {
            $this->get('/blog/'.$slug)->assertNotFound();
        }
    }

    public function test_categories_and_tags_filter_published_articles(): void
    {
        $this->freezeTime();
        $category = Category::factory()->create(['slug' => 'tutorials']);
        $tag = Tag::factory()->create(['slug' => 'php']);
        Post::factory()->published()->for($category)->hasAttached($tag)->create(['title' => 'Matching article']);
        Post::factory()->published()->create(['title' => 'Unrelated article']);
        Post::factory()->for($category)->hasAttached($tag)->create(['title' => 'Hidden article']);

        $this->get('/blog/category/tutorials')->assertOk()->assertViewHas('posts', fn ($posts): bool => $posts->total() === 1 && $posts->first()->title === 'Matching article');
        $this->get('/blog/tag/php')->assertOk()->assertViewHas('posts', fn ($posts): bool => $posts->total() === 1 && $posts->first()->title === 'Matching article');
        $this->get('/blog/category/missing')->assertNotFound();
        $this->get('/blog/tag/missing')->assertNotFound();
    }

    public function test_search_matches_titles_and_content_and_preserves_pagination(): void
    {
        $this->freezeTime();
        Post::factory()->published()->count(11)->create(['title' => 'Needle title']);
        Post::factory()->published()->create(['title' => 'Different title', 'content' => 'A needle in the content.']);
        Post::factory()->published()->create(['title' => 'Unrelated', 'content' => 'Nothing to find']);

        $this->get('/blog?q=needle')->assertOk()
            ->assertViewHas('posts', fn ($posts): bool => $posts->total() === 12 && $posts->count() === 10)
            ->assertSee('q=needle&amp;page=2', false);
        $this->get('/blog?q=needle&page=2')->assertOk()->assertViewHas('posts', fn ($posts): bool => $posts->count() === 2);
    }

    public function test_empty_blog_and_invalid_search_are_handled(): void
    {
        $this->get('/blog')->assertOk()->assertSee('No articles found.');
        $this->from('/blog')->get('/blog?q[]=invalid')->assertRedirect('/blog')->assertSessionHasErrors('q');
        $this->from('/blog')->get('/blog?q='.str_repeat('a', 201))->assertRedirect('/blog')->assertSessionHasErrors('q');
    }

    public function test_scheduled_article_becomes_visible_at_publication_time(): void
    {
        $this->freezeTime();
        Post::factory()->published()->create(['slug' => 'scheduled-release', 'published_at' => now()->addHour()]);
        $this->get('/blog/scheduled-release')->assertNotFound();

        $this->travel(1)->hours();

        $this->get('/blog/scheduled-release')->assertOk();
    }
}
