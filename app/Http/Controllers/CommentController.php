<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ArticleComment;
use App\Models\User;

class CommentController extends Controller
{
    // 提交评论
    public function commit(Request $request)
    {
        $request->validate([
            'message.article_comment_id' => 'required|integer',
            'message.article_id_fk' => 'required|exists:articles,id',
            'message.user_id_fk' => 'required|exists:users,id',
            'message.article_comment_content' => 'required|string',
            'message.article_comment_createtime' => 'required|date',
        ]);

        try {
            ArticleComment::create($request->input('message'));

            return response()->json(['status' => 'success', 'message' => '评论已接收']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => '评论保存失败'], 500);
        }
    }

    // 获取评论
    public function getCommentsByArticleId($id)
    {
        try {
            $comments = ArticleComment::with('user')->where('article_id_fk', $id)->get();

            return response()->json($comments, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => '无法获取评论'], 500);
        }
    }
}
