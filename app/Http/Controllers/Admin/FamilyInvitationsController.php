<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FamilyInvitation;
use Illuminate\Http\Request;

class FamilyInvitationsController extends Controller
{
    public function index(Request $request)
    {
        $query = FamilyInvitation::with(['inviter', 'subscription.plan', 'coupon']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('invitee_email', 'like', "%{$search}%")
                  ->orWhere('invitee_name',  'like', "%{$search}%")
                  ->orWhereHas('inviter', fn ($u) => $u->where('name', 'like', "%{$search}%")
                                                        ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        $invitations = $query->orderByDesc('sent_at')->paginate(20)->withQueryString();

        $stats = [
            'total'    => FamilyInvitation::count(),
            'pending'  => FamilyInvitation::where('status', 'pending')->count(),
            'redeemed' => FamilyInvitation::where('status', 'redeemed')->count(),
            'expired'  => FamilyInvitation::where('status', 'expired')->count(),
        ];

        return view('app.admin.family_invitations.index', compact('invitations', 'stats'));
    }
}
