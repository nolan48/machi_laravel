<?php
namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    // GET 獲得所有資料，加入分頁與搜尋字串功能，單一資料表處理
    public function index(Request $request)
    {
        $sort = $request->input('sort', 'date');
        $order = $request->input('order', 'desc');
        $search = $request->input('search', '');
        $category = $request->input('category', '');
        $min = (int) $request->input('min', 0);
        $max = (int) $request->input('max', 3000);

        // 分頁用
        $page = (int) $request->input('page', 1);
        $perPage = (int) $request->input('perpage', 16);
        $offset = ($page - 1) * $perPage;

        // 排序用
        $orderDirection = $order ?? 'ASC';
        $sortMap = [
            'date' => 'course_start_time',
            'price' => 'course_price',
        ];
        $sortField = $sortMap[$sort] ?? 'course_start_time';

        // where 各條件(以 AND 相連)
        $conditions = [];

        if ($search) {
            $conditions[] = ['course_name', 'like', '%' . $search . '%'];
        }

        if ($category) {
            $conditions[] = ['course_category', '=', $category];
        }

        if ($min && $max) {
            $conditions[] = ['course_price', 'between', [$min, $max]];
        }

        // 避免 sql 查詢錯誤導致後端當掉，使用 try/catch 語句
        try {
            $query = Course::where($conditions)
                ->orderBy($sortField, $orderDirection)
                ->offset($offset)
                ->limit($perPage);

            $count = $query->count();
            $courses = $query->get();

            // 計算總頁數
            $pageCount = ceil($count / $perPage) ?? 0;

            return response()->json([
                'status' => 'success',
                'data' => [
                    'total' => $count,
                    'pageCount' => $pageCount,
                    'page' => $page,
                    'perpage' => $perPage,
                    'courses' => $courses,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => '無法查詢到資料，查詢字串可能有誤',
            ]);
        }
    }

    // 獲得單筆資料
    public function show($id)
    {
        // 只會回傳單筆資料
        $course = Course::find($id);

        if ($course) {
            return response()->json(['status' => 'success', 'data' => ['course' => $course]]);
        } else {
            return response()->json(['status' => 'error', 'message' => '資料未找到'], 404);
        }
    }
}
