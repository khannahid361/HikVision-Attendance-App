<?php

use App\Http\Controllers\ExcelImportController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/hik-test', function () {

    $url = "http://10.1.1.2/ISAPI/AccessControl/AcsEvent?format=json";

    $data = [
        "AcsEventCond" => [
            "searchID" => "1",
            "searchResultPosition" => 0,
            "maxResults" => 10,
            "major" => 5,
            "minor" => 0
        ]
    ];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // VERY IMPORTANT
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
    curl_setopt($ch, CURLOPT_USERPWD, "admin:Saimon@2025");

    curl_setopt($ch, CURLOPT_POST, true);

    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json"
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        dd(curl_error($ch));
    }

    curl_close($ch);

    dd($response);
});

Route::get('/hik-users', function () {

    $url = "http://10.1.1.2/ISAPI/AccessControl/UserInfo/Search?format=json";

    $data = [
        "UserInfoSearchCond" => [
            "searchID" => "1",
            "searchResultPosition" => 0,
            "maxResults" => 50
        ]
    ];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
    curl_setopt($ch, CURLOPT_USERPWD, "admin:Saimon@2025");

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json"
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        dd(curl_error($ch));
    }

    curl_close($ch);

    dd($response);
});

Route::get('/yesterday-attendance-old', function () {

    $url = 'http://10.1.1.2/ISAPI/AccessControl/AcsEvent?format=json';

    // $data=[
    //   "AcsEventCond"=>[
    //     "searchID"=>"1",
    //     "searchResultPosition"=>0,
    //     "maxResults"=>5000,
    //     "major"=>5,
    //     "minor"=>0,
    //     "startTime"=>now()->subDay()->startOfDay()->format('Y-m-d\TH:i:s'),
    //     "endTime"=>now()->subDay()->endOfDay()->format('Y-m-d\TH:i:s')
    //   ]
    // ];

    $data = [
        "AcsEventCond" => [
            "searchID" => "1",
            "searchResultPosition" => 0,
            "maxResults" => 100
        ]
    ];


    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
    curl_setopt($ch, CURLOPT_USERPWD, "admin:Saimon@2025");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json"
    ]);

    $response = json_decode(curl_exec($ch), true);
    curl_close($ch);

    $attendance = [];


    foreach ($response['AcsEvent']['InfoList'] as $log) {

        // usually cardNo or employeeNoString depending response
        $emp = $log['cardNo'] ?? $log['employeeNoString'] ?? null;
        $time = $log['time'];

        if (!$emp) continue;

        $attendance[$emp][] = $time;
    }

    $result = [];

    foreach ($attendance as $emp => $times) {

        sort($times);

        $result[] = [
            'employee' => $emp,
            'first_punch' => $times[0],
            'last_punch' => end($times)
        ];
    }

    dd($result);
    // return response()->json($result);
});

Route::get('/yesterday-attendance-2', function () {

    $url = "http://10.1.1.2/ISAPI/AccessControl/AcsEvent?format=json";

    $data = [
        "AcsEventCond" => [
            "searchID" => "1",
            "searchResultPosition" => 0,
            "maxResults" => 1000,
            "major" => 5,
            "minor" => 0
        ]
    ];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
    curl_setopt($ch, CURLOPT_USERPWD, 'admin:Saimon@2025');

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Accept: application/json"
    ]);

    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        dd(curl_error($ch));
    }

    curl_close($ch);

    dd($response);
});

Route::get('/yesterday-attendance-no-out-time', function () {

    $url = "http://10.1.1.2/ISAPI/AccessControl/AcsEvent?format=json";

    $data = [
        "AcsEventCond" => [
            "searchID" => "1",
            "searchResultPosition" => 0,
            "maxResults" => 1000,
            "major" => 5,
            "minor" => 0,
            "startTime" => "2026-05-03T00:00:00",
            "endTime" => "2026-05-03T23:59:59"
        ]
    ];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
    curl_setopt($ch, CURLOPT_USERPWD, 'admin:Saimon@2025');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Accept: application/json"
    ]);

    $raw = curl_exec($ch);

    if (curl_errno($ch)) {
        dd(curl_error($ch));
    }

    curl_close($ch);

    $response = json_decode($raw, true);

    // return $response;

    if (!$response) {
        $response = json_decode(stripslashes($raw), true);
    }

    $logs = $response['AcsEvent']['InfoList'] ?? [];

    if (empty($logs)) {
        return ['message' => 'No logs found'];
    }

    $attendance = [];

    // foreach ($logs as $log) {

    //     // IMPORTANT: use employeeNoString
    //     $emp = $log['employeeNoString'] ?? null;
    //     $time = $log['time'] ?? null;

    //     // skip invalid logs
    //     if (!$emp || !$time) continue;

    //     // skip invalid verification
    //     if (($log['currentVerifyMode'] ?? '') === 'invalid') continue;

    //     $attendance[$emp][] = $time;

    // }

    foreach ($logs as $log) {

        $emp = $log['employeeNoString'] ?? null;
        $time = $log['time'] ?? null;
        $minor = $log['minor'] ?? null;

        if (!$emp || !$time) continue;

        // 🔥 ONLY keep real attendance events
        if (!in_array($minor, [75, 104])) continue;

        $attendance[$emp][] = $time;
    }

    // return $attendance;

    $result = [];

    foreach ($attendance as $emp => $times) {

        sort($times);

        $result[] = [
            'employee_id' => $emp,
            'first_in' => reset($times),
            'last_out' => end($times),
        ];
    }

    return $result;
});

Route::get('/attendance-old', function () {

    $url = "http://10.1.1.2/ISAPI/AccessControl/AcsEvent?format=json";

    $position = 0;
    $limit = 500; // safer than 1000
    $allLogs = [];

    // $loopCount = 0;
    // $maxLoops = 2; // 🔥 safety (adjust if needed)
    do {

        $data = [
            "AcsEventCond" => [
                "searchID" => "1",
                "searchResultPosition" => $position,
                "maxResults" => $limit,
                "major" => 5,          // Access control
                "minor" => 0,         // Authentication event (IMPORTANT)
                "startTime" => "2026-04-30T00:00:00",
                "endTime" => "2026-04-30T23:59:59"
            ]
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
        curl_setopt($ch, CURLOPT_USERPWD, 'admin:Saimon@2025');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Accept: application/json"
        ]);

        $raw = curl_exec($ch);

        if (curl_errno($ch)) {
            dd(curl_error($ch));
        }

        curl_close($ch);

        $response = json_decode($raw, true);

        if (!$response) {
            $response = json_decode(stripslashes($raw), true);
        }

        $logs = $response['AcsEvent']['InfoList'] ?? [];

        // merge logs
        $allLogs = array_merge($allLogs, $logs);

        // move to next page
        $position += $limit;

        $status = $response['AcsEvent']['responseStatusStrg'] ?? 'NO MORE';
        // $loopCount++;
        // } while ($status === 'MORE' && $loopCount < $maxLoops);
    } while ($status === 'MORE');

    // $debugLogs = array_values(array_filter($allLogs, function ($log) {
    //     return ($log['employeeNoString'] ?? null) == 513;
    // }));

    // dd($debugLogs);

    // 🔥 Now process all logs (complete data)

    $attendance = [];

    foreach ($allLogs as $log) {

        $emp  = $log['employeeNoString'] ?? null;
        $time = $log['time'] ?? null;

        if (!$emp || !$time) continue;

        // skip invalid
        if (($log['currentVerifyMode'] ?? '') === 'invalid') continue;

        // 🔥 extract date
        $date = substr($time, 0, 10); // YYYY-MM-DD

        // 🔥 group by employee + date
        $attendance[$emp][$date][] = $time;
    }

    $result = [];

    foreach ($attendance as $emp => $dates) {

        foreach ($dates as $date => $times) {

            sort($times);
            $first = reset($times);
            $last  = end($times);

            $result[] = [
                'employee_id'   => $emp,
                'date'          => $date,
                'first_in'      => $first,
                'last_out'      => $first !== $last ? $last : null, // null only if same timestamp
                'total_punches' => count($times),
            ];
        }
    }
    return $result;
});


Route::get('/attendance', function () {

    $url = "http://10.1.1.2/ISAPI/AccessControl/AcsEvent?format=json";

    $position = 0;
    $limit = 500;
    $allLogs = [];

    $loopCount = 0;
    $maxLoops = 20; // safety

    do {

        $data = [
            "AcsEventCond" => [
                "searchID" => "1",
                "searchResultPosition" => $position,
                "maxResults" => $limit,
                "major" => 5,
                "minor" => 0, // 🔥 get ALL events
                "startTime" => "2026-05-03T00:00:00+06:00",
                "endTime"   => "2026-05-03T23:59:59+06:00"
            ]
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
        curl_setopt($ch, CURLOPT_USERPWD, 'admin:Saimon@2025');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Accept: application/json"
        ]);

        $raw = curl_exec($ch);

        if (curl_errno($ch)) {
            return curl_error($ch);
        }

        curl_close($ch);

        $response = json_decode($raw, true);

        if (!$response) {
            $response = json_decode(stripslashes($raw), true);
        }

        $logs = $response['AcsEvent']['InfoList'] ?? [];

        $allLogs = array_merge($allLogs, $logs);

        $position += $limit;

        $status = $response['AcsEvent']['responseStatusStrg'] ?? 'NO MORE';

        $loopCount++;

    } while ($status === 'MORE' && $loopCount < $maxLoops);

    // 🔥 PROCESS LOGS

    $attendance = [];

    foreach ($allLogs as $log) {

        $emp   = $log['employeeNoString'] ?? null;
        $time  = $log['time'] ?? null;
        $minor = $log['minor'] ?? null;
        $mode  = $log['currentVerifyMode'] ?? null;

        if (!$emp || !$time) continue;

        // ❌ skip invalid attempts
        if ($mode === 'invalid') continue;

        // ❌ skip system/noise events
        if (in_array($minor, [21, 22])) continue;

        // 🔥 detect method
        $type = 'unknown';

        if (str_contains($mode, 'face')) {
            $type = 'face';
        } elseif (str_contains($mode, 'card')) {
            $type = 'card';
        } elseif (str_contains($mode, 'fp')) {
            $type = 'fingerprint';
        }

        // 🔥 extract date
        $date = substr($time, 0, 10);

        $attendance[$emp][$date][] = [
            'time'   => $time,
            'type'   => $type,
            'mode'   => $mode,
            'minor'  => $minor
        ];
    }

    // 🔥 BUILD FINAL RESULT

    $result = [];

    foreach ($attendance as $emp => $dates) {

        foreach ($dates as $date => $logs) {

            // sort by time
            usort($logs, fn($a, $b) => strcmp($a['time'], $b['time']));

            $times = array_column($logs, 'time');
            $types = array_column($logs, 'type');

            $result[] = [
                'employee_id'   => $emp,
                'date'          => $date,
                'first_in'      => $times[0],
                'last_out'      => count($times) > 1 ? end($times) : null,
                'total_punches' => count($times),
                'methods'       => array_values(array_unique($types)) // face/card/fp
            ];
        }
    }

    return $result;
});


Route::get('/debug-minor-codes', function () {

    $url = "http://10.1.1.2/ISAPI/AccessControl/AcsEvent?format=json";

    $position = 0;
    $limit = 500;
    $allLogs = [];

    $loopCount = 0;
    $maxLoops = 20; // safety

    do {

        $data = [
            "AcsEventCond" => [
                "searchID" => "1",
                "searchResultPosition" => $position,
                "maxResults" => $limit,
                "major" => 5,      // Access control events
                "minor" => 0,      // 🔥 get ALL minors
                "startTime" => "2026-05-01T00:00:00+06:00",
                "endTime"   => "2026-05-03T23:59:59+06:00"
            ]
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
        curl_setopt($ch, CURLOPT_USERPWD, 'admin:Saimon@2025');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Accept: application/json"
        ]);

        $raw = curl_exec($ch);

        if (curl_errno($ch)) {
            dd(curl_error($ch));
        }

        curl_close($ch);

        $response = json_decode($raw, true);

        if (!$response) {
            $response = json_decode(stripslashes($raw), true);
        }

        $logs = $response['AcsEvent']['InfoList'] ?? [];

        // merge logs
        $allLogs = array_merge($allLogs, $logs);

        // move pagination
        $position += $limit;

        $status = $response['AcsEvent']['responseStatusStrg'] ?? 'NO MORE';

        $loopCount++;
    } while ($status === 'MORE' && $loopCount < $maxLoops);

    // 🔍 ANALYZE minor codes
    $minorCodes = [];

    foreach ($allLogs as $log) {

        $major = $log['major'] ?? 'N/A';
        $minor = $log['minor'] ?? 'N/A';

        $key = $major . '-' . $minor;

        if (!isset($minorCodes[$key])) {
            $minorCodes[$key] = [
                'major'       => $major,
                'minor'       => $minor,
                'description' => ($log['majorDesc'] ?? '') . ' / ' . ($log['minorDesc'] ?? ''),
                'sample_emp'  => $log['employeeNoString'] ?? 'N/A',
                'sample_time' => $log['time'] ?? 'N/A',
                'count'       => 0,
            ];
        }

        $minorCodes[$key]['count']++;
    }

    // sort by count (optional)
    uasort($minorCodes, function ($a, $b) {
        return $b['count'] <=> $a['count'];
    });

    return $minorCodes;
});


Route::get('attendance-form', [ExcelImportController::class, 'showImportForm'])->name('attendance.form');
Route::post('attendance-import', [ExcelImportController::class, 'importCsv'])->name('attendance.import.csv');