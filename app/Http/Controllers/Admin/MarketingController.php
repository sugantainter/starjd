<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MarketingCampaign;
use App\Models\MarketingLog;
use App\Models\Role;
use App\Models\User;
use App\Services\MarketingCampaignService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarketingController extends Controller
{
    protected $service;

    public function __construct(MarketingCampaignService $service)
    {
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        $campaigns = MarketingCampaign::orderBy('created_at', 'desc')->get();
        return response()->json($campaigns);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:email,push,both',
            'target_type' => 'required|in:all,individual,role,category',
            'target_id' => 'nullable|integer',
            'scheduled_at' => 'nullable|date',
        ]);

        $campaign = MarketingCampaign::create($data);

        return response()->json([
            'message' => 'Campaign created successfully',
            'campaign' => $campaign
        ]);
    }

    public function show(MarketingCampaign $marketing): JsonResponse
    {
        $marketing->loadCount(['logs as sent_count' => function ($query) {
            $query->where('status', 'sent');
        }]);
        
        $marketing->loadCount(['logs as failed_count' => function ($query) {
            $query->where('status', 'failed');
        }]);

        return response()->json($marketing);
    }

    public function send(MarketingCampaign $marketing): JsonResponse
    {
        if ($marketing->status !== 'draft') {
            return response()->json(['message' => 'Campaign already processed or in progress'], 400);
        }

        $this->service->dispatchCampaign($marketing);

        return response()->json([
            'message' => 'Campaign queued for delivery',
            'campaign' => $marketing->fresh()
        ]);
    }

    public function stats(): JsonResponse
    {
        $totalCampaigns = MarketingCampaign::count();
        $totalSent = MarketingLog::where('status', 'sent')->count();
        $totalFailed = MarketingLog::where('status', 'failed')->count();
        
        $recentLogs = MarketingLog::with(['user:id,name', 'campaign:id,title'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'total_campaigns' => $totalCampaigns,
            'total_sent' => $totalSent,
            'total_failed' => $totalFailed,
            'recent_logs' => $recentLogs
        ]);
    }

    public function getFilters(): JsonResponse
    {
        $roles = Role::select('id', 'name', 'slug')->get();
        $categories = DB::table('categories')->select('id', 'name')->get();
        
        return response()->json([
            'roles' => $roles,
            'categories' => $categories
        ]);
    }
}
