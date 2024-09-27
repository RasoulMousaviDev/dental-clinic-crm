<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReorderSurvayQuestionRequest;
use App\Http\Requests\StoreSurvayQuestionRequest;
use App\Http\Requests\UpdateSurvayQuestionRequest;
use App\Models\Survay;
use App\Models\SurvayQuestion;
use Illuminate\Http\Request;

class SurvayQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Survay $survay)
    {
        $questions = $survay->questions()->orderBy('order')->paginate(10);

        return response()->json($this->paginate($questions));
    }


    public function store(StoreSurvayQuestionRequest $request, Survay $survay)
    {
        $form = $request->only(['title', 'order', 'status']);

        $survay->questions()->create($form)->save();

        $question = $survay->questions()->latest()->first();

        return response()->json($question);
    }

    public function update(UpdateSurvayQuestionRequest $request, SurvayQuestion $question)
    {
        $form = $request->only($question->fillable);

        $question->update($form);

        $question->refresh();

        return response()->json($question);
    }

    public function reorder(ReorderSurvayQuestionRequest $request, Survay $survay)
    {
        $rows = $request->get('rows');

        foreach ($rows as $row)
            SurvayQuestion::find($row['id'])->update(['order' => $row['order']]);

        return $this->index($survay);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SurvayQuestion $question)
    {
        $question->delete();

        return response()->json(['message' => __('messages.deleted-successfully')]);
    }
}