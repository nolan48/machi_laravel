<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use DOMDocument;

class ArticleController extends Controller
{
    public function publish(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|exists:users,id',
            'article' => 'required|string',
            'category' => 'required|array',
            'articleImage' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $category = implode(',', $request->category);

        $articleImage = $request->file('articleImage');
        if ($articleImage) {
            $filename = Str::uuid() . '.' . $articleImage->getClientOriginalExtension();
            $path = $articleImage->storeAs('public/images/blog/article', $filename);
        }

        try {
            $newArticle = Article::create([
                'user_id_fk' => $request->author,
                'article_title' => $request->title,
                'article_content' => $request->article,
                'article_status' => 1,
                'subcategory_id_fk' => 1,
                'category_id_fk' => 1,
                'article_category' => $category,
                'article_image' => $filename ?? null,
            ]);

            return response()->json($newArticle, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => '无法发布文章'], 500);
        }
    }

    public function upload(Request $request)
    {
        $request->validate([
            'articleImage' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            $articleImage = $request->file('articleImage');
            $filename = Str::uuid() . '.' . $articleImage->getClientOriginalExtension();
            $path = $articleImage->storeAs('public/images/blog/article', $filename);

            return response()->json(['message' => '文件上传成功', 'url' => Storage::url($path)], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => '服务器错误'], 500);
        }
    }

    public function getArticles()
    {
        try {
            $articles = Article::all();
            $articlesWithFirstImage = $articles->map(function ($article) {
                $dom = new DOMDocument();
                libxml_use_internal_errors(true); // 禁用 libxml 错误处理
                $dom->loadHTML($article->article_content);
                libxml_clear_errors();

                $images = $dom->getElementsByTagName('img');
                $firstImageUrl = '';

                if ($images->length > 0) {
                    $firstImage = $images->item(0); // 获取第一个 <img> 元素
                    if ($firstImage instanceof \DOMElement) {
                        $firstImageUrl = $firstImage->getAttribute('src');
                    }
                }

                // 将文章对象转换为数组，并添加 firstImageUrl 属性
                return array_merge($article->toArray(), ['firstImageUrl' => $firstImageUrl]);
            });

            return response()->json($articlesWithFirstImage, 200);
        } catch (\Exception $e) {
            Log::error('Error fetching articles: '.$e->getMessage());
            return response()->json(['message' => '伺服器錯誤'], 500);
        }
    }

    public function getFilteredArticles(Request $request)
    {
        $search = $request->input('search', '');
        $start = $request->input('start', '1970-01-01');
        $end = $request->input('end', '2050-01-01');
        $selectedCategories = $request->input('selectedCategories', '');

        try {
            $query = Article::query();

            if ($search) {
                $query->where('article_title', 'like', '%' . $search . '%');
            }

            if ($selectedCategories) {
                $query->where(function ($q) use ($selectedCategories) {
                    $categories = explode(',', $selectedCategories);
                    foreach ($categories as $category) {
                        $q->orWhere('article_category', 'like', '%,' . $category . ',%');
                    }
                });
            }

            if ($start && $end) {
                $query->whereBetween('article_createtime', [$start, $end]);
            }

            $page = $request->input('page', 1);
            $perpage = $request->input('perpage', 16);
            $articles = $query->paginate($perpage, ['*'], 'page', $page);

            $articlesWithFirstImage = $articles->getCollection()->map(function ($article) {
                $dom = new DOMDocument();
                libxml_use_internal_errors(true); // 禁用 libxml 错误处理

                $dom->loadHTML($article->article_content);
                $firstImage = $dom->getElementsByTagName('img')->item(0);
                $firstImageUrl = '';

                if ($firstImage) {
                    $firstImageUrl = $firstImage->getAttribute('src');
                    if (!$firstImageUrl) {
                        Log::error('The first image element does not have a src attribute for article ID: ' . $article->id);
                    }
                } else {
                    Log::warning('No image found in article content for article ID: ' . $article->id);
                }

                // 清除错误
                libxml_clear_errors();

                // 确保 array_merge 正确使用模型的属性
                return array_merge($article->toArray(), ['firstImageUrl' => $firstImageUrl]);
            });

            // 替换原始集合
            $articles->setCollection($articlesWithFirstImage);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'total' => $articles->total(),
                    'pageCount' => $articles->lastPage(),
                    'page' => $articles->currentPage(),
                    'perpage' => $articles->perPage(),
                    'articles' => $articlesWithFirstImage,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getFilteredArticles: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => '无法查询到数据，查询字符串可能有误'], 500);
        }
    }

    public function getArticleById($id)
    {
        try {
            $article = Article::with('user')->findOrFail($id);
            $articleData = $article->toArray();
            $articleData['userName'] = $article->user->name;

            return response()->json($articleData, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => '文章不存在'], 404);
        }
    }
}
