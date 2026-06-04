@extends('layouts.app')

@section('title', 'Activity Logs')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 border-b border-gray-200">
            <h1 class="text-3xl font-bold text-gray-900">Activity Logs</h1>
            <p class="mt-2 text-sm text-gray-600">Complete audit trail of system activity</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-4">
            <form method="GET" action="{{ route('activity-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <!-- User filter -->
                <select name="user_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">All users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>

                <!-- Action filter -->
                <select name="action" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">All actions</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>
                            {{ ucfirst($action) }}
                        </option>
                    @endforeach
                </select>

                <!-- Date from -->
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">

                <!-- Date to -->
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition ease-in-out duration-150">
                        Filter
                    </button>
                    <a href="{{ route('activity-logs.index') }}" class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition ease-in-out duration-150">
                        Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Changes</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div>{{ $log->created_at->format('M d, Y') }}</div>
                            <div class="text-xs text-gray-400">{{ $log->created_at->format('H:i:s') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($log->user)
                                <div class="font-medium text-gray-900">{{ $log->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $log->user->email }}</div>
                            @else
                                <span class="text-gray-400 italic">System</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($log->action === 'created') bg-green-100 text-green-800
                                @elseif($log->action === 'updated') bg-blue-100 text-blue-800
                                @elseif($log->action === 'deleted') bg-red-100 text-red-800
                                @elseif($log->action === 'restored') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($log->action) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div>{{ class_basename($log->model_type) }}</div>
                            <div class="text-xs text-gray-400">#{{ $log->model_id }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $log->ip_address ?? '—' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs">
                            @if($log->changes && count($log->changes) > 0)
                                <div x-data="{ open: false }">
                                    <button @click="open = !open" class="text-indigo-600 hover:text-indigo-900 text-xs underline">
                                        {{ count($log->changes) }} field(s) changed
                                    </button>
                                    <div x-show="open" class="mt-2 text-xs bg-gray-50 rounded p-2 space-y-1">
                                        @foreach($log->changes as $field => $value)
                                            @if(!in_array($field, ['password', 'remember_token']))
                                            <div><span class="font-medium">{{ $field }}</span>:
                                                <span class="text-gray-400">{{ is_array($log->old_values) && isset($log->old_values[$field]) ? $log->old_values[$field] : '—' }}</span>
                                                → <span class="text-gray-700">{{ is_array($value) ? json_encode($value) : $value }}</span>
                                            </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @elseif($log->action === 'created')
                                <span class="text-xs text-gray-400">New record</span>
                            @elseif($log->action === 'deleted')
                                <span class="text-xs text-gray-400">Record removed</span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No activity logs found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $logs->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

    <div class="text-sm text-gray-500 text-right">
        Showing {{ $logs->firstItem() ?? 0 }}–{{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} entries
    </div>
</div>
@endsection
