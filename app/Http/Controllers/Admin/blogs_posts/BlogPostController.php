<?php

namespace App\Http\Controllers\Admin\blogs_posts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;

class BlogPostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['category', 'author']);

        // Tìm kiếm theo tiêu đề
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
        }

        // Filter Status theo is_published
        if ($request->filled('status')) {
            if ($request->status === '1') {
                $query->where('is_published', true);
            } elseif ($request->status === '0') {
                $query->where('is_published', false);
            }
        }

        // Lọc bài viết đã xóa (is_deleted)
        if ($request->filled('is_deleted')) {
            if ($request->is_deleted === '1') {
                $query->where('is_deleted', true);
            } elseif ($request->is_deleted === '0') {
                $query->where('is_deleted', false);
            }
        } else {
            // Mặc định chỉ hiển thị bài chưa xóa nếu không filter
            $query->where('is_deleted', false);
        }

        $posts = $query->latest()->paginate(10)->withQueryString();

        return view('pages.admin.blogs-posts.blog-post-index', compact('posts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:posts,slug',
            'category_id' => 'required|exists:post_categories,id',
            'thumbnail' => 'nullable|url|max:2048', // Sử dụng URL thay vì file, nhưng có thể cho phép max len URL
            'summary' => 'nullable|string',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'is_published' => 'boolean',
        ], [
            'thumbnail.url' => 'Ảnh đại diện phải là một URL hợp lệ.'
        ]);

        $validated['is_published'] = $request->has('is_published');
        $validated['user_id'] = auth()->id();
        
        if ($validated['is_published']) {
            $validated['published_at'] = now();
        }

        Post::create($validated);

        return back()->with('success', 'Thêm bài viết mới thành công!');
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:posts,slug,' . $post->id,
            'category_id' => 'required|exists:post_categories,id',
            'thumbnail' => 'nullable|url|max:2048',
            'summary' => 'nullable|string',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'is_published' => 'boolean',
        ], [
            'thumbnail.url' => 'Ảnh đại diện phải là một URL hợp lệ.'
        ]);

        $wasPublished = $post->is_published;
        $validated['is_published'] = $request->has('is_published');
        
        if ($validated['is_published'] && !$wasPublished) {
            $validated['published_at'] = now();
        }

        $post->update($validated);

        return back()->with('success', 'Cập nhật bài viết thành công!');
    }

    public function destroy(Post $post)
    {
        // Có thể bổ sung check quyền xóa
        $post->update(['is_deleted' => true]);

        return back()->with('success', 'Xóa bài viết thành công!');
    }
}
