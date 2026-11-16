<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Console\Scheduling\Event as ScheduledEvent;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class ScheduleMonitorController extends Controller
{
    public function index(): View
    {
        $tasks = $this->gatherScheduledTasks();

        return view('admin.system.schedule', [
            'tasks' => $tasks,
        ]);
    }

    public function runNow(Request $request): JsonResponse
    {
        $signature = $request->input('signature');

        if (empty($signature)) {
            return response()->json(['error' => 'Signature required'], 400);
        }

        $exitCode = 0;

        try {
            $exitCode = Artisan::call($signature);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'exit_code' => -1,
            ], 500);
        }

        $cacheKey = 'schedule.last_run.'.md5($signature);
        Cache::put($cacheKey, now()->toIso8601String(), now()->addDay());

        return response()->json([
            'exit_code' => $exitCode,
            'ok' => $exitCode === 0,
        ]);
    }

    /**
     * Gather scheduled task information from Laravel's console scheduler.
     */
    private function gatherScheduledTasks(): array
    {
        $tasks = [];
        $scheduler = app(Schedule::class);

        foreach ($scheduler->events() as $event) {
            $command = $this->resolveCommand($event);
            $description = $this->resolveDescription($event);
            $expression = $this->resolveExpression($event);
            $lastRunAt = $this->getLastRunAt($command);
            $nextRunAt = $this->getNextRunAt($event);
            $status = $this->getStatus($lastRunAt);

            $tasks[] = [
                'command' => $command,
                'description' => $description,
                'expression' => $expression,
                'last_run_at' => $lastRunAt,
                'next_run_at' => $nextRunAt,
                'status' => $status,
            ];
        }

        return $tasks;
    }

    private function resolveCommand(ScheduledEvent $event): string
    {
        if (property_exists($event, 'command') && $event->command !== null) {
            return $event->command;
        }

        return $event->getSummaryForDisplay();
    }

    private function resolveDescription(ScheduledEvent $event): string
    {
        if (method_exists($event, 'description') && isset($event->description)) {
            return $event->description;
        }

        return $event->getSummaryForDisplay();
    }

    private function resolveExpression(ScheduledEvent $event): string
    {
        $parts = [];

        if (property_exists($event, 'expression')) {
            $parts[] = $event->expression;
        }

        if (method_exists($event, 'evenWeekdays') && $event->evenWeekdays) {
            $parts[] = 'every even weekday';
        }
        if (method_exists($event, 'oddWeekdays') && $event->oddWeekdays) {
            $parts[] = 'every odd weekday';
        }
        if (method_exists($event, 'monthly') && $event->monthly) {
            $parts[] = 'monthly';
        }
        if (method_exists($event, 'daily') && $event->daily) {
            $parts[] = 'daily';
        }
        if (method_exists($event, 'twiceDaily') && $event->twiceDaily) {
            $parts[] = 'twice daily';
        }
        if (method_exists($event, 'hourly') && $event->hourly) {
            $parts[] = 'hourly';
        }

        return implode(', ', $parts) ?: '* * * * *';
    }

    private function getLastRunAt(string $command): ?Carbon
    {
        $key = 'schedule.last_run.'.md5($command);
        $raw = Cache::get($key);

        if ($raw === null) {
            return null;
        }

        try {
            return Carbon::parse($raw);
        } catch (\Throwable) {
            return null;
        }
    }

    private function getNextRunAt(ScheduledEvent $event): string
    {
        try {
            $next = $event->nextRun();
            if ($next instanceof CarbonInterface) {
                return $next->diffForHumans();
            }
        } catch (\Throwable $e) {
            return '—';
        }

        return '—';
    }

    private function getStatus(?Carbon $lastRunAt): string
    {
        if ($lastRunAt === null) {
            return 'never';
        }

        $hoursSinceLastRun = now()->diffInHours($lastRunAt, false);

        if ($hoursSinceLastRun < 2) {
            return 'ok';
        }

        if ($hoursSinceLastRun < 24) {
            return 'warning';
        }

        return 'error';
    }
}
