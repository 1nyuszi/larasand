<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $author = User::query()->first();

        if ($author === null) {
            $this->command?->warn('Create a user before seeding sample blog posts.');

            return;
        }

        $category = Category::firstOrCreate(
            ['slug' => 'general'],
            ['name' => 'General', 'description' => 'Notes and updates.'],
        );
        $tag = Tag::firstOrCreate(['slug' => 'welcome'], ['name' => 'Welcome']);
        $post = Post::firstOrCreate(['slug' => 'welcome-to-the-blog'], [
            'category_id' => $category->id,
            'user_id' => $author->id,
            'title' => 'Welcome to the blog',
            'excerpt' => 'A new place for stories, discoveries, and ideas.',
            'content' => "Welcome! This is the first article on the blog.\n\nYou can manage articles, categories, and tags from the admin panel.",
            'is_featured' => true,
            'is_published' => true,
            'published_at' => now(),
        ]);
        $post->tags()->syncWithoutDetaching([$tag->id]);
    }
}
