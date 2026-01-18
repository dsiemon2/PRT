<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\ContactMessage;
use App\Models\Setting;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function about()
    {
        return view('pages.about');
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function contactSubmit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        ContactMessage::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'status' => 'unread',
        ]);

        return back()->with('success', 'Thank you for your message! We will get back to you soon.');
    }

    public function faq(Request $request)
    {
        $categories = FaqCategory::with(['faqs' => function ($query) {
            $query->orderBy('display_order');
        }])->orderBy('display_order')->get();

        $search = $request->input('search');
        if ($search) {
            $faqs = Faq::where('question', 'like', "%{$search}%")
                ->orWhere('answer', 'like', "%{$search}%")
                ->get();
        } else {
            $faqs = null;
        }

        return view('pages.faq', compact('categories', 'faqs', 'search'));
    }

    public function giftCards()
    {
        return view('pages.gift-cards');
    }

    public function privacy()
    {
        return view('pages.privacy');
    }

    public function terms()
    {
        return view('pages.terms');
    }

    public function returns()
    {
        return view('pages.returns');
    }

    public function shipping()
    {
        return view('pages.shipping');
    }
}
