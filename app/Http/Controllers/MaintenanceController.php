<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class MaintenanceController extends Controller
{
    public function clearCache()
    {
        $exitCode = Artisan::call('cache:clear');

        if ($exitCode === 0) {
            return response()->json([
                'status' => 'success',
                'message' => 'Cache cleared successfully',
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to clear cache',
            ], 500);
        }
    }
}
