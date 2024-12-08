<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexAppointmentRequest;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Role;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(IndexAppointmentRequest $request)
    {
        $rows = $request->input('rows', 10);

        $searchableFields = ['firstname', 'lastname'];

        $filterableFields = ['patient', 'status'];

        $dateFields = ['due_date', 'created_at', 'updated_at'];

        $user = auth()->user();

        $roles = Role::whereIn('name', ['super-admin', 'admin', 'reception'])->pluck('id');

        $isAdmin = collect($roles)->contains($user->role->id);

        $appointments = Appointment::with([
            'treatments:id,title',
            'patient:id,firstname,lastname',
            'status:id,value,severity',
        ]);

        if ($isAdmin) $appointments = $appointments->with('patient.user:id,name')->when($request->input('user'), function ($query, $user) {
            $query->whereHas('patient.user', function (Builder $query) use ($user) {
                $query->where('name', 'like', "%{$user}%");
            });
        });
        else $appointments = $appointments->whereHas('patient', function (Builder $query) use ($user) {
            $query->where('user', $user->id);
        });


        foreach ($searchableFields as $field) {
            $appointments->when($request->input($field), function ($query, $value) use ($field) {
                $query->whereHas('patient', function (Builder $query) use ($field, $value) {
                    $query->where($field, 'like', "%{$value}%");
                });
            });
        }

        foreach ($filterableFields as $field) {
            $appointments->when($request->input($field), function ($query, $value) use ($field) {
                $query->where($field, $value);
            });
        }

        foreach ($dateFields as $field) {
            $appointments->when($request->input($field), function ($query, $value) use ($field) {
                $value = collect($value)->map(fn($d, $i) => Carbon::parse($d)
                    ->setTimezone('Asia/Tehran')
                    ->{$i ? 'endOfDay' : 'startOfDay'}()
                    ->format('Y-m-d H:i:s'));

                $query->whereBetween($field, $value);
            });
        }

        $appointments->when($request->input('treatment'), function ($query, $treatment) {
            $query->whereHas('treatment', function (Builder $query) use ($treatment) {
                $query->where('id', $treatment);
            });
        });

        $appointments = $appointments->latest()->paginate($rows);

        $response = $this->paginate($appointments);

        $response['statuses'] = Appointment::model()->statuses;

        return response()->json($response);
    }

    public function store(StoreAppointmentRequest $request)
    {
        $form = $request->only(['due_date', 'desc']);

        $patient = Patient::find($request->get('patient'));

        $appointment = $patient->appointments()->create($form);

        $treatments = $request->get('treatments');

        $appointment->treatments()->attach($treatments);

        $appointment = $patient->appointments()->with([
            'treatments:id,title',
            'patient:id,firstname,lastname,user',
            'status:id,value,severity',
            'patient.user:id,name',
        ])->latest()->first();

        $status = Status::firstWhere('name', 'appointment-set')->id;

        $patient->update(compact('status'));

        return response()->json($appointment);
    }

    public function update(UpdateAppointmentRequest $request, Appointment $appointment)
    {
        $form = $request->only(['status', 'deposit']);

        $appointment->update($form);

        $appointment->refresh();

        $status = $form['status'];

        if (in_array($status, [4, 5, 6]))
            $appointment->patient()->update(compact('status'));


        $appointment->load('status');

        return response()->json($appointment);
    }
}
