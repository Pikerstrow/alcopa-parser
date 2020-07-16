<?php

namespace App\Http\Controllers\Parser;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;


class ParserController extends Controller
{
    public function startParser()
    {
        try {
            Artisan::command('parser:parse_alcopa', function(){});
            return response()->json([
                'success' => true
            ]);
        } catch (\Throwable $exception) {
            Log::info(['error' => $exception->getMessage() . ' ' . $exception->getFile() . ' ' . $exception->getLine()]);
            return response()->json([
                'error' => 'Something went wrong',
            ], 500);
        }
    }


    public function getParserProgress(Request $request)
    {
        try {
            $current_log = $request->log;
            $current_log = json_decode($current_log);

            $progress_info = Cache::get('parser');
            $new_logs = !empty($progress_info) ? json_decode($progress_info, true) : [];

            $new_entries = array_diff($new_logs, $current_log);

            return response()->json([
                'status' => 'success',
                'data' => $new_entries
            ]);
        } catch (\Throwable $exception) {
            Log::info(['error' => $exception->getMessage() . ' ' . $exception->getFile() . ' ' . $exception->getLine()]);
            return response()->json([
                'error' => $exception->getMessage(),
            ], 500);
        }
    }
}
