<?php

namespace App\Http\Controllers;

use App\Models\EmployeeAttendance;
use App\Models\EmployeeTest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    public function records(Request $request)
    {
        $date = $request->query('date');
        $dateValue = $date ? Carbon::parse($date) : Carbon::today();
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $employee = $this->getEmployeeForUser($user);


        $rows = EmployeeAttendance::query()
            ->where('employee_id', $employee->id)
            ->when($date, function ($query) use ($dateValue) {
                $query->whereDate('attendance_date', $dateValue->toDateString());
            })
            ->orderByDesc('attendance_date')
            ->get()
            ->map(function ($row) {
                $dateValue = Carbon::parse($row->attendance_date);
                $status = 'time-in';
                if (!$row->time_in && !$row->time_out) {
                    $status = 'absent';
                } elseif ($row->time_out) {
                    $status = 'clocked-out';
                }
                $timeInPhoto = $row->time_in_photo_path
                    ? asset('storage/' . $row->time_in_photo_path)
                    : null;
                $timeOutPhoto = $row->time_out_photo_path
                    ? asset('storage/' . $row->time_out_photo_path)
                    : null;
                return [
                    'date' => $dateValue->toDateString(),
                    'dayOfWeek' => $dateValue->format('l'),
                    'timeIn' => $row->time_in,
                    'timeOut' => $row->time_out,
                    'status' => $status,
                    'timeInPhoto' => $timeInPhoto,
                    'timeOutPhoto' => $timeOutPhoto,
                    'timeInGeo' => [
                        'latitude' => $row->time_in_latitude,
                        'longitude' => $row->time_in_longitude,
                        'accuracy' => $row->time_in_geo_accuracy,
                        'label' => $row->time_in_location_label,
                    ],
                    'timeOutGeo' => [
                        'latitude' => $row->time_out_latitude,
                        'longitude' => $row->time_out_longitude,
                        'accuracy' => $row->time_out_geo_accuracy,
                        'label' => $row->time_out_location_label,
                    ],
                    'qrPayload' => $row->qr_payload
                ];
            });

        return response()->json([
            'data' => $rows,
            'meta' => [
                'date' => $dateValue->toDateString(),
                'dayOfWeek' => $dateValue->format('l'),
                'employee' => [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'position' => $employee->position
                ]
            ]
        ]);
    }

    public function updateRecord(Request $request, string $date)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $employee = $this->getEmployeeForUser($user);
        $validated = $request->validate([
            'time_in' => ['nullable', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'time_out' => ['nullable', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'qr_payload' => ['nullable', 'string'],
            'time_in_photo' => ['nullable', 'string'],
            'time_out_photo' => ['nullable', 'string'],
            'time_in_geo' => ['nullable', 'array'],
            'time_in_geo.latitude' => ['required_with:time_in_photo', 'numeric', 'between:-90,90'],
            'time_in_geo.longitude' => ['required_with:time_in_photo', 'numeric', 'between:-180,180'],
            'time_in_geo.accuracy' => ['nullable', 'numeric', 'min:0'],
            'time_out_geo' => ['nullable', 'array'],
            'time_out_geo.latitude' => ['required_with:time_out_photo', 'numeric', 'between:-90,90'],
            'time_out_geo.longitude' => ['required_with:time_out_photo', 'numeric', 'between:-180,180'],
            'time_out_geo.accuracy' => ['nullable', 'numeric', 'min:0']
        ]);

        if (!empty($validated['time_in_photo']) && empty($validated['time_in_geo'])) {
            return response()->json([
                'message' => 'Geolocation is required for time-in photo capture.'
            ], 422);
        }

        if (!empty($validated['time_out_photo']) && empty($validated['time_out_geo'])) {
            return response()->json([
                'message' => 'Geolocation is required for time-out photo capture.'
            ], 422);
        }

        $timeIn = $validated['time_in'] ?? null;
        $timeOut = $validated['time_out'] ?? null;

        if ($timeIn === '') {
            $timeIn = null;
        }

        if ($timeOut === '') {
            $timeOut = null;
        }

        if ($timeIn && strlen($timeIn) === 5) {
            $timeIn .= ':00';
        }

        if ($timeOut && strlen($timeOut) === 5) {
            $timeOut .= ':00';
        }

        $lastAttendance = EmployeeAttendance::query()
            ->where('employee_id', $employee->id)
            ->orderByDesc('attendance_date')
            ->first();

        $timeInPhotoPath = $this->saveBase64Image($validated['time_in_photo'] ?? null, 'time_in');
        $timeOutPhotoPath = $this->saveBase64Image($validated['time_out_photo'] ?? null, 'time_out');

        $updateData = [
            'payroll_start' => $lastAttendance?->payroll_start?->toDateString() ?? $date,
            'payroll_end' => $lastAttendance?->payroll_end?->toDateString() ?? $date
        ];

        if ($request->has('time_in')) {
            $updateData['time_in'] = $timeIn;
        }
        if ($request->has('time_out')) {
            $updateData['time_out'] = $timeOut;
        }

        if (!empty($validated['qr_payload'])) {
            $updateData['qr_payload'] = $validated['qr_payload'];
        }
        if ($timeInPhotoPath) {
            $timeInLabel = $this->reverseGeocodeLabel(
                $validated['time_in_geo']['latitude'] ?? null,
                $validated['time_in_geo']['longitude'] ?? null
            );

            $updateData['time_in_photo_path'] = $timeInPhotoPath;
            $updateData['time_in_latitude'] = $validated['time_in_geo']['latitude'] ?? null;
            $updateData['time_in_longitude'] = $validated['time_in_geo']['longitude'] ?? null;
            $updateData['time_in_geo_accuracy'] = $validated['time_in_geo']['accuracy'] ?? null;
            $updateData['time_in_location_label'] = $timeInLabel;
        }
        if ($timeOutPhotoPath) {
            $timeOutLabel = $this->reverseGeocodeLabel(
                $validated['time_out_geo']['latitude'] ?? null,
                $validated['time_out_geo']['longitude'] ?? null
            );

            $updateData['time_out_photo_path'] = $timeOutPhotoPath;
            $updateData['time_out_latitude'] = $validated['time_out_geo']['latitude'] ?? null;
            $updateData['time_out_longitude'] = $validated['time_out_geo']['longitude'] ?? null;
            $updateData['time_out_geo_accuracy'] = $validated['time_out_geo']['accuracy'] ?? null;
            $updateData['time_out_location_label'] = $timeOutLabel;
        }

        EmployeeAttendance::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'attendance_date' => $date
            ],
            $updateData
        );

        return response()->json(['status' => 'ok']);
    }

    public function qrCode(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $employee = $this->getEmployeeForUser($user);
        $date = $request->query('date')
            ? Carbon::parse($request->query('date'))->toDateString()
            : Carbon::today()->toDateString();

        $payload = json_encode([
            'type' => 'employee_attendance',
            'employee_id' => $employee->id,
            'date' => $date
        ], JSON_UNESCAPED_SLASHES);

        $lastAttendance = EmployeeAttendance::query()
            ->where('employee_id', $employee->id)
            ->orderByDesc('attendance_date')
            ->first();

        EmployeeAttendance::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'attendance_date' => $date
            ],
            [
                'qr_payload' => $payload,
                'payroll_start' => $lastAttendance?->payroll_start?->toDateString() ?? $date,
                'payroll_end' => $lastAttendance?->payroll_end?->toDateString() ?? $date
            ]
        );

        return response()->json([
            'data' => [
                'date' => $date,
                'payload' => $payload
            ]
        ]);
    }

    private function getEmployeeForUser($user): EmployeeTest
    {
        return EmployeeTest::firstOrCreate(
            ['user_id' => $user->id],
            [
                'position' => 'Employee',
                'rate' => 80,
                'payroll_frequency' => 'Weekly'
            ]
        );
    }

    private function saveBase64Image(?string $data, string $prefix): ?string
    {
        if (!$data) {
            return null;
        }

        if (!preg_match('/^data:image\/(\w+);base64,/', $data, $matches)) {
            return null;
        }

        $extension = strtolower($matches[1]);
        $base64 = substr($data, strpos($data, ',') + 1);
        $binary = base64_decode($base64, true);

        if ($binary === false) {
            return null;
        }

        $filename = sprintf('%s_%s_%s.%s', $prefix, now()->format('Ymd_His'), Str::random(8), $extension);
        $path = 'attendance/' . $filename;

        Storage::disk('public')->put($path, $binary);

        return $path;
    }

    private function reverseGeocodeLabel(?float $latitude, ?float $longitude): ?string
    {
        if (is_null($latitude) || is_null($longitude)) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'EOSHMA ERP Attendance/1.0'
            ])->get('https://nominatim.openstreetmap.org/reverse', [
                'format' => 'jsonv2',
                'lat' => $latitude,
                'lon' => $longitude,
                'addressdetails' => 1
            ]);

            if (!$response->successful()) {
                return null;
            }

            $body = $response->json();
            $address = $body['address'] ?? [];

            $city = $address['city']
                ?? $address['town']
                ?? $address['municipality']
                ?? $address['village']
                ?? null;

            $province = $address['state']
                ?? $address['county']
                ?? null;

            $parts = array_filter([$city, $province]);

            if (!empty($parts)) {
                return implode(', ', $parts);
            }

            return $body['display_name'] ?? null;
        } catch (\Throwable $exception) {
            return null;
        }
    }
}
