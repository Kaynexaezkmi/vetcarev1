<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userFeedback = Feedback::where('user_id', $user->id)->parentFeedback()->get();
        $allFeedback = Feedback::with('user', 'replies')->parentFeedback()->orderBy('created_at', 'desc')->paginate(10);

        return view('feedback.index', compact('userFeedback', 'allFeedback'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'message' => 'required|string|max:1000',
        ]);

        Feedback::create([
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'message' => $request->message,
            'parent_id' => null,
        ]);

        return redirect()->back()->with('success', 'Thank you for your feedback!');
    }

    public function update(Request $request, Feedback $feedback)
    {
        if ($feedback->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'message' => 'required|string|max:1000',
        ]);

        $feedback->update([
            'rating' => $request->rating,
            'message' => $request->message,
        ]);

        return redirect()->back()->with('success', 'Feedback updated successfully!');
    }

    public function destroy(Feedback $feedback)
    {
        if ($feedback->user_id !== Auth::id()) {
            abort(403);
        }

        $feedback->delete();

        return redirect()->back()->with('success', 'Feedback deleted successfully!');
    }

    public function reply(Request $request, Feedback $feedback)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        Feedback::create([
            'user_id' => Auth::id(),
            'rating' => null,
            'message' => $request->message,
            'parent_id' => $feedback->id,
        ]);

        return redirect()->back()->with('success', 'Reply added successfully!');
    }
}