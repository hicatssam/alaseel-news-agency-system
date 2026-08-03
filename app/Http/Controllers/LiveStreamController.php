<?php

namespace App\Http\Controllers;

use App\Models\LiveStream;

class LiveStreamController extends Controller
{
    public function show()
    {
        $stream = LiveStream::active();
        return view('live', compact('stream'));
    }
}
