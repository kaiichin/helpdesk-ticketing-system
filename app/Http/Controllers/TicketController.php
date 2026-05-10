<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (Auth::user()->isItSupport()) {
            return redirect()->route('support.tickets.index');
        }

        $tickets = Auth::user()
            ->tickets()
            ->with('category')
            ->latest()
            ->paginate(10);

        return view('tickets.index', compact('tickets'));
    }

    public function create(): View|RedirectResponse
    {
        if (Auth::user()->isItSupport()) {
            return redirect()->route('support.tickets.index');
        }

        $categories = Category::orderBy('name')->get();

        return view('tickets.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        if (Auth::user()->isItSupport()) {
            return redirect()->route('support.tickets.index');
        }

        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ]);

        $ticket = Ticket::create([
            'ticket_no' => Ticket::generateTicketNo(),
            'user_id' => Auth::id(),
            'category_id' => $data['category_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'status' => Ticket::STATUS_OPEN,
        ]);

        $ticket->histories()->create([
            'user_id' => Auth::id(),
            'action' => 'created',
            'new_status' => Ticket::STATUS_OPEN,
            'note' => 'Ticket submitted by employee.',
        ]);

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Ticket created successfully.');
    }

    public function show(Ticket $ticket): View|RedirectResponse
    {
        if (Auth::user()->isItSupport()) {
            return redirect()->route('support.tickets.show', $ticket);
        }

        abort_unless($ticket->user_id === Auth::id(), 403);

        $ticket->load(['category', 'user', 'histories.user']);

        return view('tickets.show', compact('ticket'));
    }
}
