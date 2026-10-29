<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BiscuitBlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $author = User::query()->orderBy('id')->firstOrFail();

        DB::transaction(function () use ($author): void {
            $manufacturing = Category::firstOrCreate(['slug' => 'biscuit-manufacturing'], [
                'name' => 'Biscuit Manufacturing',
                'description' => 'From mixing dough to baking crisp, golden biscuits.',
            ]);
            $quality = Category::firstOrCreate(['slug' => 'quality-and-packaging'], [
                'name' => 'Quality & Packaging',
                'description' => 'Keeping every batch consistent, crisp, and ready for the shelf.',
            ]);

            $tags = collect([
                'ingredients' => 'Ingredients',
                'dough-mixing' => 'Dough Mixing',
                'baking' => 'Baking',
                'process-control' => 'Process Control',
                'quality-control' => 'Quality Control',
                'packaging' => 'Packaging',
            ])->map(fn (string $name, string $slug): Tag => Tag::firstOrCreate(['slug' => $slug], ['name' => $name]));

            $articles = [
                [
                    'slug' => 'biscuit-manufacturing-starts-with-the-dough',
                    'title' => 'Great Biscuits Start with Consistent Dough',
                    'category_id' => $manufacturing->id,
                    'excerpt' => 'Accurate weighing and controlled mixing lay the foundation for a reliable biscuit batch.',
                    'content' => "A biscuit production line begins long before the oven. Flour, sugar, fat, and other ingredients are weighed to the recipe, then combined in a planned sequence. Consistent ingredient quantities help each batch behave predictably.\n\nMixing time and dough temperature matter too. Too much mixing can change the texture, while dough that is too warm may become difficult to shape. Operators check the dough before it moves to the forming stage, keeping the process steady from batch to batch.",
                    'tags' => ['ingredients', 'dough-mixing', 'process-control'],
                    'is_featured' => true,
                ],
                [
                    'slug' => 'shaping-and-baking-biscuits',
                    'title' => 'From Dough to Golden Biscuits',
                    'category_id' => $manufacturing->id,
                    'excerpt' => 'Uniform shapes and a controlled bake help deliver the colour and crunch customers expect.',
                    'content' => "Once the dough is ready, it is formed into individual biscuits. Depending on the recipe, manufacturers may use rotary moulding, sheeting and cutting, or wire cutting. Keeping the pieces uniform helps them bake at a similar rate.\n\nIn the oven, heat develops colour, structure, and flavour while moisture is removed. Oven settings and conveyor speed are adjusted for the product rather than relying on one universal baking temperature. Regular checks on colour, weight, and texture help the team spot changes early.",
                    'tags' => ['baking', 'process-control', 'quality-control'],
                    'is_featured' => false,
                ],
                [
                    'slug' => 'cooling-and-packaging-crisp-biscuits',
                    'title' => 'Keeping the Crunch: Cooling and Packaging',
                    'category_id' => $quality->id,
                    'excerpt' => 'Careful cooling and sound packaging protect biscuits after they leave the oven.',
                    'content' => "Freshly baked biscuits need time to cool before packing. Packing them while they are still too warm can cause condensation inside the wrapper, affecting their crispness. Cooling conveyors give the product time to reach a suitable packing condition.\n\nBefore dispatch, the team checks for broken pieces, verifies pack weights, and inspects seals. Packaging selected for the product helps limit moisture pickup during storage. Clear batch codes make it easier to trace a pack back to its production run.",
                    'tags' => ['quality-control', 'packaging', 'process-control'],
                    'is_featured' => false,
                ],
            ];

            foreach ($articles as $index => $article) {
                $articleTags = $article['tags'];
                unset($article['tags']);

                $post = Post::firstOrCreate(['slug' => $article['slug']], [
                    ...$article,
                    'user_id' => $author->id,
                    'is_published' => true,
                    'published_at' => now()->subDays(3 - $index),
                ]);
                $post->tags()->syncWithoutDetaching(
                    collect($articleTags)->map(fn (string $slug): int => $tags[$slug]->id)->all(),
                );
            }
        });
    }
}
