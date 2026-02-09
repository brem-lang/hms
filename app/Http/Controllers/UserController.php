<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        // Verify the hash matches the user's email
        if (! hash_equals((string) $hash, sha1($user->email))) {
            abort(403, 'Invalid verification link.');
        }

        // Check if already verified
        if ($user->email_verified_at) {
            return redirect()->route('index')->with('message', 'Email already verified.');
        }

        // Mark email as verified
        $user->email_verified_at = now();
        $user->save();

        return redirect()->route('index')->with('message', 'Email verified successfully! You can now log in.');
    }
}
