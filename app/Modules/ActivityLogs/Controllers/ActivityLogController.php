<?php

namespace App\Modules\ActivityLogs\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $authUser = auth()->user();

        $query = ActivityLog::with(['user'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        // Admins see only logs for users in their scope
        if ($authUser->isAdmin()) {
            $scopedUserIds = User::where('created_by', $authUser->id)
                ->orWhere('id', $authUser->id)
                ->pluck('id');

            $query->whereIn('user_id', $scopedUserIds);
        }

        $logs = $query->paginate(25);

        $users = $authUser->isSysAdmin()
            ? User::orderBy('name')->get(['id', 'name'])
            : User::where(function ($q) use ($authUser) {
                $q->where('created_by', $authUser->id)->orWhere('id', $authUser->id);
            })->orderBy('name')->get(['id', 'name']);

        $actions = [
            ActivityLog::ACTION_CREATED,
            ActivityLog::ACTION_UPDATED,
            ActivityLog::ACTION_DELETED,
            ActivityLog::ACTION_RESTORED,
            ActivityLog::ACTION_VIEWED,
        ];

        return view('activity_logs.index', compact('logs', 'users', 'actions'));
    }
}
