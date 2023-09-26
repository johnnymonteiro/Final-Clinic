<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Time;
use Laravel\Ui\Presets\React;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $myappointments = Appointment::latest()->where('user_id', auth()->user()->id)->get();
        return view('admin.appointment.index', compact('myappointments'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.appointment.create');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'date' => 'required|unique:appointments,date,NULL,id,user_id,' . \Auth::id(),
            'time' => 'required'
        ]);
        //dd($request->all());
        $appointment = Appointment::create([
            'user_id' => auth()->user()->id,
            'date' => $request->date
        ]);

        //dd($appointment->id);
        foreach ($request->time as $time) {
            Time::create([
                'appointment_id' => $appointment->id,
                'time' => $time,
                //'status' => 0, -> desnecessário pois ja esta definido no model como default 0
            ]);
        }

        return redirect()->back()->with('message', 'Appointment created for ' . $request->date);
    }




    public function check(Request $request)
    {
        $date = $request->date;
        $appointment = Appointment::where('date', $date)->where('user_id', auth()->user()->id)->first();

        if (!$appointment) {
            return redirect()->to('/appointment')->with('errmessage', 'Appointment time not available for this date');
        }

        $appointmentId = $appointment->id;

        $times = Time::where('appointment_id', $appointmentId)->get();


        return view('admin.appointment.index', compact('times', 'appointmentId', 'date'));
    }




    public function updateTime(Request $request)
    {
        $appointmentId = $request->appointmentId;
        $appointment = Time::where('appointment_id', $appointmentId)->delete();

        foreach ($request->time as $time) {
            Time::create([
                'appointment_id' => $appointmentId,
                'time' => $time,
                'status' => 0
            ]);
        }

        return redirect()->route('appointment.index')->with('message', 'Appointment time updated!!');
    }
}
