<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatMessage;
use App\Services\ActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $status = (string) $request->query('status', '');

        $chats = Chat::query()
            ->where('user_id', $user->id)
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Chat $chat) => [
                'id' => $chat->id,
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

        return Inertia::render('Chats/Index', [
            'chats' => $chats,
            'filters' => ['status' => $status],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Chats/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'category' => ['required', 'string', Rule::in(['registry', 'bursary', 'exams_records', 'complaints'])],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:5'],
        ]);

        $chat = Chat::create([
            'user_id' => $request->user()->id,
            'category' => $request->input('category'),
            'subject' => $request->input('subject'),
            'status' => 'open',
        ]);

        ChatMessage::create([
            'chat_id' => $chat->id,
            'user_id' => $request->user()->id,
            'message' => $request->input('message'),
        ]);

        $this->activities->log('created_chat', 'Started a new chat', Chat::class, $chat->id, [
            'category' => $chat->category,
            'subject' => $chat->subject,
        ]);

        return to_route('chats.index');
    }

    public function show(Request $request, Chat $chat): Response
    {
        $this->authorizeChat($chat);

        $chat->load(['messages.user', 'assignedTo']);

        $chat->messages()
            ->where('user_id', '!=', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return Inertia::render('Chats/Show', [
            'chat' => $chat,
        ]);
    }

    public function sendMessage(Request $request, Chat $chat): RedirectResponse
    {
        $this->authorizeChat($chat);

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

        $chat->update(['status' => 'in_progress']);

        return back();
    }

    private function authorizeChat(Chat $chat): void
    {
        $user = auth()->user();

        if ($user->hasRole('student')) {
            if ($chat->user_id !== $user->id) {
                abort(403);
            }
            return;
        }

        if ($user->hasAnyRole(['admin', 'registry', 'busary', 'exams&records'])) {
            $permission = match ($chat->category) {
                'registry' => 'registry access',
                'bursary' => 'bursary access',
                'exams_records' => 'exams&records access',
                'complaints' => 'atteend to complains',
                default => null,
            };

            if ($permission && ! $user->can($permission)) {
                abort(403);
            }

            return;
        }

        abort(403);
    }
}
