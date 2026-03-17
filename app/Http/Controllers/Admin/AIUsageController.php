<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AIUsage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AIUsageController extends Controller
{
    public function index(): JsonResponse
    {
        $stats = [
            'total_tokens' => AIUsage::sum('total_tokens'),
            'total_prompt_tokens' => AIUsage::sum('prompt_tokens'),
            'total_completion_tokens' => AIUsage::sum('completion_tokens'),
            'usage_by_provider' => AIUsage::select('provider', DB::raw('sum(total_tokens) as tokens'))
                ->groupBy('provider')
                ->get(),
            'usage_by_type' => AIUsage::select('type', DB::raw('sum(total_tokens) as tokens'))
                ->groupBy('type')
                ->get(),
            'daily_usage' => AIUsage::select(DB::raw('DATE(created_at) as date'), DB::raw('sum(total_tokens) as tokens'))
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->limit(30)
                ->get()
        ];

        return response()->json($stats);
    }

    public function userStats(): JsonResponse
    {
        $users = AIUsage::with('user:id,name,email')
            ->select('user_id', DB::raw('count(*) as requests'), DB::raw('sum(total_tokens) as tokens'))
            ->groupBy('user_id')
            ->orderBy('tokens', 'desc')
            ->get();

        return response()->json($users);
    }

    public function recentLogs(): JsonResponse
    {
        $logs = AIUsage::with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json($logs);
    }
}
