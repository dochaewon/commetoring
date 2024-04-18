<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class QuestionController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/questions",
     *     summary="질문 목록 조회",
     *     tags={"Questions"},
     *     @OA\Response(
     *         response=200,
     *         description="성공적으로 조회된 질문 목록",
     *
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="내부 서버 오류"
     *     )
     * )
     */

    public function index()
    {
        $questions = Question::with(['user' => function ($query) {
            $query->select('id', 'breed');
        }])->select('id', 'title', 'content', 'created_at','user_id')->paginate(6);
        // 내용은 처음 20글자만 가져옵니다.
        $questions->getCollection()->transform(function ($question) {
            $question->content = mb_substr($question->content, 0, 20);
            return $question;
        });

        return response()->json($questions);
    }

    /**
     * @OA\Get(
     *     path="/api/questions/{id}",
     *     summary="질문 상세 정보 조회",
     *     tags={"Questions"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="질문 ID",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="성공적으로 조회된 질문 상세 정보",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="요청한 질문을 찾을 수 없음"
     *     )
     * )
     */
    public function show($id)
    {
        $question = Question::with('category', 'answers', 'user')->findOrFail($id);;

        return response()->json(['question' => $question], 200);
    }


    /**
     * @OA\Post(
     *     path="/api/questions",
     *     summary="질문 등록",
     *     tags={"Questions"},
     *     security={{ "sanctum": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         description="질문 정보",
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="질문 등록 성공",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="유효성 검사 오류",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'category_id' => 'required'
        ]);

        $user = $request->user();
        $request->merge(['user_id' => $user->id]);

        $question = Question::create($request->all());
        return response()->json($question, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/questions/{questionId}/answers/{answerId}/select",
     *     summary="답변 채택",
     *     tags={"Questions"},
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="questionId",
     *         in="path",
     *         required=true,
     *         description="질문 ID",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="answerId",
     *         in="path",
     *         required=true,
     *         description="답변 ID",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="답변 채택 성공",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Answer accepted successfully"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="권한 없음",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="질문 또는 답변을 찾을 수 없음",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Not Found"),
     *         )
     *     )
     * )
     */
    public function selectAnswer(Request $request, $questionId, $answerId)
    {
//        dd($request->user(), $questionId, $answerId);
        // 현재 로그인된 사용자의 아이디를 가져옵니다.
        $userId = $request->user()->id;

        // 질문 작성자인지 확인합니다.
        $question = Question::findOrFail($questionId);
        if ($question->user_id !== $userId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // 답변을 가져와 채택합니다.
        $answer = Answer::findOrFail($answerId);
        $answer->accepted = true;
        $answer->save();

        return response()->json(['message' => 'Answer accepted successfully'], 200);
    }
}
