<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
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
