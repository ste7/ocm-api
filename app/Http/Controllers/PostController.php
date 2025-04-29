<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\IndexRequest;
use App\Services\PostService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    public function __construct(
        private readonly PostService $postService
    ) {}

    public function fetchAndStore(): JsonResponse
    {
        try {
            return response()->json([
                'status' => 'success',
                'message' => $this->postService->fetchAndStore(),
                'data' => [],
            ], 201);
        } catch (Exception $e) {
            Log::error('Failed to fetch and store posts: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred',
            ]);
        }
    }

    public function index(IndexRequest $request): JsonResponse
    {
        try {
            return response()->json([
                'status' => 'success',
                'message' => 'Posts fetched successfully',
                'data' => $this->postService->list($request->get('query')),
            ], 200);
        } catch (Exception $e) {
            Log::error('Failed to fetch posts: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred',
            ], 500);
        }
    }
}
