<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\Questionnaire;
use App\Models\QuestionnaireResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class QuestionnaireResponseController extends Controller
{
    /**
     * Show questionnaire for a child.
     */
    public function create(
        Questionnaire $questionnaire,
        Child $child
    ): View {
        abort_unless(
            $questionnaire->status === 'published',
            404
        );

        $alreadyAnswered = QuestionnaireResponse::where(
            'questionnaire_id',
            $questionnaire->id
        )
            ->where('child_id', $child->id)
            ->exists();

        $questionnaire->load([
            'event',
            'questions.options',
        ]);

        return view(
            'admin.events.questionnaires.responses.create',
            compact(
                'questionnaire',
                'child',
                'alreadyAnswered'
            )
        );
    }

    /**
     * Store questionnaire answers.
     */
    public function store(
        Request $request,
        Questionnaire $questionnaire,
        Child $child
    ): RedirectResponse {
        abort_unless(
            $questionnaire->status === 'published',
            404
        );

        // Check duplicate response.
        $alreadyAnswered = QuestionnaireResponse::where(
            'questionnaire_id',
            $questionnaire->id
        )
            ->where('child_id', $child->id)
            ->exists();

        if ($alreadyAnswered) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'This child has already answered this questionnaire.'
                );
        }

        $questionnaire->load('questions');

        // Build validation rules dynamically.
        $rules = [];

        foreach ($questionnaire->questions as $question) {
            $fieldName = 'question_' . $question->id;

            $rules[$fieldName] = $question->is_required
                ? ['required']
                : ['nullable'];
        }

        $validated = $request->validate($rules);

        DB::transaction(function () use (
            $questionnaire,
            $child,
            $validated
        ) {
            $response = QuestionnaireResponse::create([
                'questionnaire_id' => $questionnaire->id,
                'child_id' => $child->id,
                'submitted_by' => auth()->id(),
                'submitted_at' => now(),
                'response_uuid' => (string) Str::uuid(),
            ]);

            foreach ($questionnaire->questions as $question) {
                $fieldName = 'question_' . $question->id;

                $answer = $validated[$fieldName] ?? null;

                if (is_array($answer)) {
                    $response->answers()->create([
                        'question_id' => $question->id,
                        'answer_text' => null,
                        'answer_json' => $answer,
                    ]);
                } else {
                    $response->answers()->create([
                        'question_id' => $question->id,
                        'answer_text' => $answer,
                        'answer_json' => null,
                    ]);
                }
            }
        });

        return redirect()
            ->route(
                'questionnaires.responses.success',
                [
                    $questionnaire,
                    $child,
                ]
            )
            ->with(
                'success',
                'Questionnaire submitted successfully.'
            );
    }

    /**
     * Show submission success page.
     */
    public function success(
        Questionnaire $questionnaire,
        Child $child
    ): View {
        return view(
            'admin.events.questionnaires.responses.success',
            compact(
                'questionnaire',
                'child'
            )
        );
    }
}