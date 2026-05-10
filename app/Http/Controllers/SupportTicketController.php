<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SupportTicketController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeSupport();

        $filters = $request->validate([
            'status' => ['nullable', 'in:' . implode(',', Ticket::STATUSES)],
            'created_date' => ['nullable', 'date'],
            'category_id' => ['nullable', 'exists:categories,id'],
        ]);

        $categories = Category::orderBy('name')->get();

        $tickets = Ticket::with(['user', 'category'])
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($filters['created_date'] ?? null, function ($query, $createdDate) {
                $query->whereDate('created_at', $createdDate);
            })
            ->when($filters['category_id'] ?? null, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('support.tickets.index', compact('tickets', 'categories', 'filters'));
    }

    public function show(Ticket $ticket): View
    {
        $this->authorizeSupport();

        $ticket->load(['user', 'category', 'histories.user']);

        return view('support.tickets.show', compact('ticket'));
    }

    public function updateStatus(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorizeSupport();

        $data = $request->validate([
            'status' => ['required', 'in:' . implode(',', [
                Ticket::STATUS_ON_PROGRESS,
                Ticket::STATUS_RESOLVED,
                Ticket::STATUS_CLOSED,
            ])],
            'note' => ['required', 'string', 'min:3', 'max:1000'],
        ]);

        if (! $ticket->canMoveTo($data['status'])) {
            return back()
                ->withErrors([
                    'status' => 'Status can only move from ' . $ticket->status . ' to ' . $ticket->nextStatus() . '.',
                ])
                ->withInput();
        }

        $oldStatus = $ticket->status;

        $ticket->update([
            'status' => $data['status'],
        ]);

        $ticket->histories()->create([
            'user_id' => Auth::id(),
            'action' => 'status_updated',
            'old_status' => $oldStatus,
            'new_status' => $data['status'],
            'note' => $data['note'],
        ]);

        return back()->with('success', 'Ticket status updated successfully.');
    }

    private function authorizeSupport(): void
    {
        abort_unless(Auth::user()->isItSupport(), 403);
    }
}
