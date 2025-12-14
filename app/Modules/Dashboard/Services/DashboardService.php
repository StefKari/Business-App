<?php

namespace App\Modules\Dashboard\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Get dashboard statistics
     */
    public function getStatistics(): array
    {
        $user = auth()->user();

        $stats = [
            'total_users' => $this->getTotalUsers(),
            'active_users' => $this->getActiveUsers(),
            'total_activities' => $this->getTotalActivities(),
            'my_created_users' => 0,
        ];

        if (!$user->isModerator()) {
            $stats['my_created_users'] = User::where('created_by', $user->id)->count();
        }

        return $stats;
    }

    /**
     * Get total users count
     */
    private function getTotalUsers(): int
    {
        $user = auth()->user();

        if ($user->isSysAdmin()) {
            return User::count();
        }

        if ($user->isAdmin()) {
            return User::where('created_by', $user->id)
                ->orWhere('id', $user->id)
                ->count();
        }

        return User::where('is_active', true)->count();
    }

    /**
     * Get active users count
     */
    private function getActiveUsers(): int
    {
        $user = auth()->user();

        if ($user->isSysAdmin()) {
            return User::where('is_active', true)->count();
        }

        if ($user->isAdmin()) {
            return User::where('is_active', true)
                ->where(function ($q) use ($user) {
                    $q->where('created_by', $user->id)
                      ->orWhere('id', $user->id);
                })
                ->count();
        }

        return User::where('is_active', true)->count();
    }

    /**
     * Get total activities count
     */
    private function getTotalActivities(): int
    {
        return ActivityLog::count();
    }

    /**
     * Get recent activities
     */
    public function getRecentActivities(int $limit = 10)
    {
        return ActivityLog::with(['user'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent users
     */
    public function getRecentUsers(int $limit = 5)
    {
        $user = auth()->user();

        $query = User::with('role')
            ->orderBy('created_at', 'desc')
            ->limit($limit);

        if ($user->isAdmin()) {
            $query->where(function ($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhere('id', $user->id);
            });
        }

        return $query->get();
    }

    /**
     * Get user activities by day (last 7 days)
     */
    public function getActivityChart(): array
    {
        $data = ActivityLog::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = [];
        $values = [];

        foreach ($data as $item) {
            $labels[] = $item->date;
            $values[] = $item->count;
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }
}
