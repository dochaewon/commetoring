<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuestionController extends Controller
{
    public function index()
    {
        $questions = Question::with(['user' => function ($query) {
            $query->select('id', 'breed');
        }])->select('id', 'title', 'content', 'created_at','user_id')->paginate(6);
        // 내용은 처음 20글자만 가져옵니다.
        $questions->getCollection()->transform(function ($question) {
            $question->content = substr($question->content, 0, 20);
            return $question;
        });

        return response()->json($questions);
    }

    public function show($id)
    {
        $question = Question::with('category', 'answers', 'user')->findOrFail($id);;

        return response()->json(['question' => $question], 200);
    }

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
