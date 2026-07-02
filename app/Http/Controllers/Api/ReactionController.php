<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Entry;
use App\Models\Publication;
use App\Models\Reaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class ReactionController extends Controller
{
    public function toggle(Request $request, string $type, int $id)
    {
        abort_unless(in_array($type, ['entry', 'publication']), 404);

        $model = $type === 'entry' ? Entry::findOrFail($id) : Publication::findOrFail($id);

        $token = $request->cookie('reaction_token');
        if (!$token) {
            $token = (string) Str::uuid();
        }

        $tokenHash = hash('sha256', $token);
        $ipHash    = hash('sha256', $request->ip().date('Y-m-d'));

        $existing = Reaction::where('reactionable_type', $type)
            ->where('reactionable_id', $id)
            ->where('token_hash', $tokenHash)
            ->first();

        if ($existing) {
            $existing->delete();
            $liked = false;
        } else {
            Reaction::create([
                'reactionable_type' => $type,
                'reactionable_id'   => $id,
                'token_hash'        => $tokenHash,
                'ip_hash'           => $ipHash,
            ]);
            $liked = true;
        }

        $count = $model->reactions()->count();

        return response()->json(['liked' => $liked, 'count' => $count])
            ->cookie(Cookie::make('reaction_token', $token, 60 * 24 * 365, null, null, true, true, false, 'strict'));
    }
}
