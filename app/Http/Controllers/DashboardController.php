<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Chat;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
   public function index(Request $request)
   {
        $user = $request->user();
    $recentActivities = Activity::query()
        ->where('user_id', $user->id)
        ->latest()
        ->limit(10)
        ->get();

    $analytics = [
        'chats' => null,
        'students' => null,
        'requests' => null,
        'staff' => null,
    ];

    if ($user->can('manage users')) {
        $analytics['students'] = [
            'total' => \App\Models\User::whereHas('roles', fn ($roles) => $roles->where('name', 'student'))->count(),
            'active' => \App\Models\User::whereHas('roles', fn ($roles) => $roles->where('name', 'student'))->where('email_verified_at', '!=', null)->count(),
        ];
        $analytics['staff'] = [
            'total' => \App\Models\User::whereHas('roles', fn ($roles) => $roles->where('name', '!=', 'student'))->count(),
        ];
    }

    if ($user->hasAnyRole(['registry', 'busary', 'exams&records', 'complaints']) || $user->can('manage users')) {
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

        $analytics['chats'] = Chat::query()
            ->when(! empty($allowedCategories), fn ($query) => $query->whereIn('category', $allowedCategories))
            ->count();

        $analytics['chats_open'] = Chat::query()
            ->when(! empty($allowedCategories), fn ($query) => $query->whereIn('category', $allowedCategories))
            ->where('status', 'open')
            ->count();

        $analytics['chats_in_progress'] = Chat::query()
            ->when(! empty($allowedCategories), fn ($query) => $query->whereIn('category', $allowedCategories))
            ->where('status', 'in_progress')
            ->count();
    }

    if ($user->can('manage requests') && class_exists(\App\Models\Request::class)) {
        $analytics['requests'] = [
            'total' => \App\Models\Request::count(),
            'pending' => \App\Models\Request::where('status', 'pending')->count(),
            'approved' => \App\Models\Request::where('status', 'approved')->count(),
            'rejected' => \App\Models\Request::where('status', 'rejected')->count(),
        ];
    }

    return Inertia::render('Dashboard', [
        'isAdmin' => $user->hasRole('admin'),
        'recentActivities' => $recentActivities,
        'analytics' => $analytics,
    ]);
   }
}
