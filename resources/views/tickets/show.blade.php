<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $ticket->ticket_no }}
            </h2>
            <a href="{{ route('tickets.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Back to My Tickets</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto space-y-6 sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Title</p>
                        <p class="mt-1 text-gray-900">{{ $ticket->title }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Status</p>
                        <p class="mt-1 text-gray-900">{{ $ticket->status }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Category</p>
                        <p class="mt-1 text-gray-900">{{ $ticket->category->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Created</p>
                        <p class="mt-1 text-gray-900">{{ $ticket->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>

                <div class="mt-6">
                    <p class="text-sm font-medium text-gray-500">Description</p>
                    <p class="mt-2 whitespace-pre-line text-gray-900">{{ $ticket->description }}</p>
                </div>
            </div>

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900">Ticket History</h3>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Time</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Action</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Note</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">By</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($ticket->histories as $history)
                                <tr>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $history->created_at->format('d M Y H:i') }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ ucfirst(str_replace('_', ' ', $history->action)) }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                                        {{ $history->old_status ? $history->old_status . ' to ' . $history->new_status : $history->new_status }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $history->note }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700">{{ $history->user?->name ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
