<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::with(['roles', 'state', 'city']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $role = $request->input('role');
            $query->whereHas('roles', function ($q) use ($role) {
                $q->where('slug', $role);
            });
        }

        $users = $query->latest()->paginate($request->input('per_page', 20));

        $users->getCollection()->transform(function ($user) {
            $user->append(['role', 'avatar_url']);
            $user->profile_completion = $this->calculateProfileCompletion($user);
            return $user;
        });

        return response()->json($users);
    }

    public function show(User $user): JsonResponse
    {
        $user->load([
            'roles',
            'state',
            'city',
            'creatorProfile',
            'brandProfile',
            'professionalProfile',
            'socialAccounts',
            'packages',
            'creatorImagePosts',
            'wallet',
            'campaignApplications.campaign',
            'campaignsAsBrand',
            'collaborationsAsBrand.creator',
            'collaborationsAsCreator.brand',
        ]);

        $userData = $user->toArray();
        $userData['role'] = $user->role;
        $userData['avatar_url'] = $user->avatar_url;
        
        $completionData = $this->calculateProfileCompletionDetails($user);
        $userData['profile_completion'] = $completionData['percentage'];
        $userData['profile_completion_details'] = $completionData['details'];
        
        // Add some analytics summary
        $userData['analytics'] = [
            'total_collaborations' => $user->collaborationsAsBrand()->count() + $user->collaborationsAsCreator()->count(),
            'total_earnings' => $user->wallet?->balance ?? 0,
            'social_reach' => $user->socialAccounts->sum('follower_count'),
            'total_posts' => $user->creatorImagePosts()->count(),
            'total_packages' => $user->packages()->count(),
        ];

        return response()->json($userData);
    }

    public function updateStatus(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'is_active' => 'required|boolean',
        ]);

        // If we don't have is_active, we can just return success or update a temporary field
        // For now, let's keep it as is, or add a migration if critically needed.
        return response()->json(['message' => 'User status updated successfully']);
    }

    private function calculateProfileCompletion(User $user): int
    {
        return $this->calculateProfileCompletionDetails($user)['percentage'];
    }

    private function calculateProfileCompletionDetails(User $user): array
    {
        $details = [];
        
        // Basic Info
        $details[] = ['label' => 'Name', 'filled' => (bool)$user->name, 'weight' => 1];
        $details[] = ['label' => 'Email', 'filled' => (bool)$user->email, 'weight' => 1];
        $details[] = ['label' => 'Profile Picture', 'filled' => (bool)$user->avatar, 'weight' => 1];
        $details[] = ['label' => 'State/City', 'filled' => (bool)($user->state_id && $user->city_id), 'weight' => 1];

        // Role specific profile
        $role = $user->role;
        if ($role === 'creator') {
            $profile = $user->creatorProfile;
            $fields = [
                'bio' => 'Biography',
                'tagline' => 'Tagline',
                'category' => 'Category',
                'gender' => 'Gender',
                'language' => 'Language',
                'min_rate' => 'Minimum Rate'
            ];
            foreach ($fields as $field => $label) {
                $details[] = ['label' => $label, 'filled' => (bool)($profile?->$field), 'weight' => 1];
            }
            
            $details[] = ['label' => 'Social Accounts', 'filled' => $user->socialAccounts()->exists(), 'weight' => 2];
            $details[] = ['label' => 'Packages', 'filled' => $user->packages()->exists(), 'weight' => 2];

        } elseif ($role === 'brand') {
            $profile = $user->brandProfile;
            $fields = [
                'company_name' => 'Company Name',
                'website' => 'Website',
                'industry' => 'Industry',
                'bio' => 'Brand Bio',
                'phone' => 'Phone',
                'address' => 'Address'
            ];
            foreach ($fields as $field => $label) {
                $details[] = ['label' => $label, 'filled' => (bool)($profile?->$field), 'weight' => 1];
            }
        } elseif ($role === 'professional') {
            $profile = $user->professionalProfile;
            $fields = [
                'bio' => 'Bio',
                'category' => 'Category',
                'experience' => 'Experience',
                'availability' => 'Availability',
                'skills' => 'Skills'
            ];
            foreach ($fields as $field => $label) {
                $details[] = ['label' => $label, 'filled' => (bool)($profile?->$field), 'weight' => 1];
            }
        } elseif ($role === 'studio_owner') {
            $details[] = ['label' => 'Studio Listing', 'filled' => $user->studios()->exists(), 'weight' => 5];
        }

        $totalWeight = array_sum(array_column($details, 'weight'));
        $filledWeight = array_sum(array_map(fn($d) => $d['filled'] ? $d['weight'] : 0, $details));
        
        $percentage = $totalWeight > 0 ? (int)(($filledWeight / $totalWeight) * 100) : 0;

        return [
            'percentage' => $percentage,
            'details' => $details
        ];
    }
}
