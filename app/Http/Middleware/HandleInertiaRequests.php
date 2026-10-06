<?php

namespace App\Http\Middleware;

use App\Models\Chat;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        $chatUnreadCount = 0;
        $chatRoute = null;

        if ($user) {
            if ($user->hasRole('student')) {
                $chatUnreadCount = Chat::query()
                    ->where('user_id', $user->id)
                    ->whereHas('messages', fn ($query) => $query->where('user_id', '!=', $user->id)->where('is_read', false))
                    ->count();

                $chatRoute = route('chats.index');
            } else {
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

                if (! empty($allowedCategories)) {
                    $chatUnreadCount = Chat::query()
                        ->whereIn('category', $allowedCategories)
                        ->whereHas('messages', fn ($query) => $query->where('user_id', '!=', $user->id)->where('is_read', false))
                        ->count();
                }

                $chatRoute = route('admin.chats.index');
            }
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
                'canManageUsers' => $user?->can('manage users') ?? false,
                'canManageRoles' => $user?->can('manage roles') ?? false,
                'canEditStudents' => $user?->can('edit students') ?? false,
                'canCreateStaff' => $user?->can('create staff') ?? false,
            ],
            'sidebar_menu' => fn () => $user ? $this->sidebarMenu($request) : [],
            'chat_unread_count' => $chatUnreadCount,
            'chat_route' => $chatRoute,
        ];
    }

    private function sidebarMenu(Request $request): array
    {
        $user = $request->user();
        $visibleItems = MenuItem::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->filter(fn (MenuItem $item) => (! $item->route || Route::has($item->route))
                        && ($item->permission
                            ? $request->user()->can($item->permission)
                            : (! $item->roles || $request->user()->hasAnyRole($item->roles))))
            ->values();

        return $visibleItems
            ->whereNull('parent_id')
            ->map(function (MenuItem $item) use ($visibleItems) {
                $children = $visibleItems
                    ->where('parent_id', $item->id)
                    ->map(fn (MenuItem $child) => [
                        'key' => $child->key,
                        'label' => $child->label,
                        'route' => $child->route,
                    ])
                    ->values();

                if (! $item->route && $children->isEmpty()) {
                    return null;
                }

                return [
                    'key' => $item->key,
                    'label' => $item->label,
                    'route' => $item->route,
                    'children' => $children,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }
}
