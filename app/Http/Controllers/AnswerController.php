<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/questions/{questionId}/answers",
     *     summary="답변 작성",
     *     tags={"Answers"},
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
     *     @OA\RequestBody(
     *         required=true,
     *         description="답변 정보",
     *         @OA\JsonContent(
     *             required={"content"},
     *             @OA\Property(property="content", type="string", example="답변 내용")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="답변 작성 성공",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="권한 없음",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="답변은 멘토만 작성가능합니다.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="답변 개수 초과",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="답변은 충분합니다.(3개 초과 작성 불가)")
     *         )
     *     )
     * )
     */

    public function store(Request $request, $questionId)
    {
        // 현재 로그인된 사용자의 아이디를 가져옵니다.
        $user = $request->user();

        //답변은 멘토만 작성가능
        if($user->type !== 'mentee') {
            return response()->json(['error' => '답변은 멘토만 작성가능합니다.'], 403);
        }

        //답변 개수가 3개 이상 달린 경우 작성 불가
        if (Question::findOrFail($questionId)->answers->count() >= 3) {
            return response()->json(['error' => '답변은 충분합니다.(3개 초과 작성 불가)'], 403);
        }

        // 답변 저장
        $answer = new Answer();
        $answer->content = $request->input('content');
        $answer->user_id = $user->id;
        $answer->question_id = $questionId;
        $answer->save();

        // 저장된 답변 반환
        return response()->json($answer, 201);
    }

    /**
     * @OA\Delete(
     *     path="/api/answers/{answerId}",
     *     summary="답변 삭제",
     *     tags={"Answers"},
     *     security={{ "sanctum": {} }},
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
     *         description="답변 삭제 성공",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Answer deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="권한 없음",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="채택된 답변은 삭제가 불가합니다.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="답변을 찾을 수 없음",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Not Found")
     *         )
     *     )
     * )
     */

    public function delete(Request $request, $answerId)
    {
        // 현재 로그인된 사용자의 아이디를 가져옵니다.
        $userId = $request->user()->id;

        // 답변을 가져옵니다.
        $answer = Answer::findOrFail($answerId);

        // 답변이 채택되었는지 확인합니다.
        if ($answer->accepted) {
            return response()->json(['error' => '채택된 답변은 삭제가 불가합니다.'], 403);
        }

        // 답변 작성자인지 확인합니다.
        if ($answer->user_id !== $userId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // 답변을 삭제합니다.
        $answer->delete();

        return response()->json(['message' => 'Answer deleted successfully'], 200);
    }
}
