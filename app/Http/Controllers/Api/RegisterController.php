<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function destroy($email)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Không tìm thấy user',
            ], 404);
        }

        $user->delete();

        return response()->json([
            'message' => 'User đã được xóa từ Web A',
        ]);
    }
}
