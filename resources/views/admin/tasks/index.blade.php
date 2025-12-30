@extends('admin.layouts.app')

@section('page_heading', 'Task Queue')

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Open Tasks</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $taskStats['open'] }}</p>
                <x-ui.badge class="mt-3" status="suspicious" label="Operational" />
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">High Priority</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $taskStats['high'] }}</p>
                <x-ui.badge class="mt-3" status="lost" label="Critical" />
            </x-ui.card>
            <x-ui.card>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Completed Today</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">{{ $taskStats['done'] }}</p>
                <x-ui.badge class="mt-3" status="active" label="Resolved" />
            </x-ui.card>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-6 py-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Queued tasks</h2>
                    <p class="text-sm text-slate-500">Prioritize admin work and track completion.</p>
                </div>
                <button class="rounded-lg bg-[color:var(--brand-600)] px-3 py-2 text-xs font-semibold text-white">Create Task</button>
            </div>
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Task</th>
                        <th class="px-4 py-3">Assigned</th>
                        <th class="px-4 py-3">Priority</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($tasks as $task)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $task['task'] }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $task['assigned'] }}</td>
                            <td class="px-4 py-3">
                                <x-ui.badge status="{{ $task['priority'] === 'High' ? 'lost' : ($task['priority'] === 'Medium' ? 'suspicious' : 'active') }}" label="{{ $task['priority'] }}" />
                            </td>
                            <td class="px-4 py-3">
                                <x-ui.badge status="{{ $task['status'] === 'Completed' ? 'active' : 'suspicious' }}" label="{{ $task['status'] }}" />
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button class="text-xs font-semibold text-[color:var(--brand-600)]">View</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
