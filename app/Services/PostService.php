<?php

namespace App\Services;

use App\Models\Post;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PostService
{
    protected $baseUrl = 'https://jsonplaceholder.typicode.com/posts';

    public function fetchAndStore(): string
    {
        $response = Http::get($this->baseUrl);

        if (! $response->successful()) {
            throw new Exception('Failed to fetch external API: '.$response->status());
        }

        $posts = $response->json();

        if (empty($posts) || ! is_array($posts)) {
            throw new Exception('No data received from API or invalid data format');
        }

        $savedCount = 0;
        $duplicateCount = 0;
        $batchSize = 100;
        $batch = [];

        DB::beginTransaction();

        try {
            foreach ($posts as $postData) {
                if (! isset($postData['id']) || ! isset($postData['title']) || ! isset($postData['body'])) {
                    Log::warning('Post is missing required fields', ['post' => $postData]);

                    continue;
                }

                if (Post::where('external_id', $postData['id'])->exists()) {
                    $duplicateCount++;

                    continue;
                }

                $batch[] = [
                    'external_id' => $postData['id'],
                    'title' => $postData['title'],
                    'body' => $postData['body'],
                    'user_id' => $postData['userId'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (count($batch) >= $batchSize) {
                    Post::insert($batch);
                    $savedCount += count($batch);
                    $batch = [];
                }
            }

            if (! empty($batch)) {
                Post::insert($batch);
                $savedCount += count($batch);
            }

            DB::commit();

            return "Posts fetched successfully. {$savedCount} new posts saved, {$duplicateCount} duplicates skipped.";
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error storing posts', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function list(?string $searchQuery): LengthAwarePaginator
    {
        $query = Post::query();

        if ($searchQuery) {
            $query->where(function ($q) use ($searchQuery) {
                $searchTerms = explode(' ', $searchQuery);

                foreach ($searchTerms as $term) {
                    if (strlen($term) >= 3) {
                        $q->where(function ($subQuery) use ($term) {
                            $subQuery->where('title', 'like', "%{$term}%")
                                ->orWhere('body', 'like', "%{$term}%");
                        });
                    }
                }
            });
        }

        return $query->latest()->paginate(20);
    }
}
