<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Services\ActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ChatController extends Controller
{
    public function __construct(protected ActivityService $activities) {}

    public function index(Request $request): Response
    {
        $user = $request->user();
        $category = (string) $request->query('category', '');
        $status = (string) $request->query('status', '');

        $allowedCategories = [];
        if ($user->can('registry access')) {
            $allowedCategories[] = 'registry';
        }
        if ($user->can('bursary access')) {
            $allowedCategories[] = 'bursary';
        }
        if ($user->can('exams&records access')) {
            $allowedCategories[] = 'exams_records';
        }
        if ($user->can('atteend to complains')) {
            $allowedCategories[] = 'complaints';
        }

        $chats = Chat::query()
            ->when(! empty($allowedCategories), fn ($query) => $query->whereIn('category', $allowedCategories))
            ->when($category !== '', fn ($query) => $query->where('category', $category))
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Chat $chat) => [
                'id' => $chat->id,
                'user' => $chat->user?->name,
                'category' => $chat->category,
                'subject' => $chat->subject,
                'status' => $chat->status,
                'assigned_to' => $chat->assignedTo?->name,
                'last_message_at' => $chat->messages()->latest()->first()?->created_at,
                'unread_count' => $chat->messages()
                    ->where('user_id', '!=', $user->id)
                    ->where('is_read', false)
                    ->count(),
                'created_at' => $chat->created_at?->toDateString(),
            ]);

        $categories = array_values(array_unique(array_merge($allowedCategories, ['registry', 'bursary', 'exams_records', 'complaints'])));

        return Inertia::render('Admin/Chats/Index', [
            'chats' => $chats,
            'categories' => $categories,
            'filters' => ['category' => $category, 'status' => $status],
            'allowedCategories' => $allowedCategories,
        ]);
    }

    public function show(Request $request, Chat $chat): Response
    {
        $this->authorizeChat($chat, $request->user());

        $chat->load(['messages.user', 'assignedTo', 'user']);

        $chat->messages()
            ->where('user_id', '!=', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return Inertia::render('Admin/Chats/Show', [
            'chat' => $chat,
        ]);
    }

    public function sendMessage(Request $request, Chat $chat): RedirectResponse
    {
        $this->authorizeChat($chat, $request->user());

        $request->validate([
            'message' => ['required', 'string', 'min:2'],
        ]);

        if ($chat->status === 'closed') {
            throw ValidationException::withMessages(['message' => 'This chat is closed.']);
        }

        ChatMessage::create([
            'chat_id' => $chat->id,
            'user_id' => $request->user()->id,
            'message' => $request->input('message'),
        ]);

        if ($chat->status === 'closed') {
            throw ValidationException::withMessages(['message' => 'This chat is closed.']);
        }

        $updateData = [
            'status' => 'in_progress',
        ];

        if ($chat->assigned_to === null) {
            $updateData['assigned_to'] = $request->user()->id;
        }

        $chat->update($updateData);

        return back();
    }

    public function updateStatus(Request $request, Chat $chat): RedirectResponse
    {
        $this->authorizeChat($chat, $request->user());

        $request->validate([
            'status' => ['required', 'string', Rule::in(['open', 'in_progress', 'resolved', 'closed'])],
        ]);

        $chat->update([
            'status' => $request->input('status'),
            'closed_at' => $request->input('status') === 'closed' ? now() : null,
        ]);

        $this->activities->log('updated_chat_status', 'Updated chat status', Chat::class, $chat->id, [
            'status' => $request->input('status'),
        ]);

        return back();
    }

    private function authorizeChat(Chat $chat, $user): void
    {
        $permission = match ($chat->category) {
            'registry' => 'registry access',
            'bursary' => 'bursary access',
            'exams_records' => 'exams&records access',
            'complaints' => 'atteend to complains',
            default => null,
        };

        if (! $permission || ! $user->can($permission)) {
            abort(403);
        }
    }
}
