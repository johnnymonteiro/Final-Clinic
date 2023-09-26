<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Time;
use App\Models\User;
use App\Models\Booking;
use App\Mail\AppointmentMail;
use App\Models\Prescription;

class FrontendController extends Controller
{

    /**
     * List all the todays appointments
     */
    public function index()
    {
        //date_default_timezone_set('Africa/Porto-Novo'); -> apenas se origem diferir mais que um dia em GMT
        //dd(date('Y-m-d'));
        if (request('date')) {
            $doctors = $this->findDoctorsBasedOnDate(request('date'));
            return view('welcome', compact('doctors'));
        }
        $doctors = Appointment::where('date', date('Y-m-d'))->get();
        return view('welcome', compact('doctors'));
    }


    /**
     * Show the specified resource
     */
    public function show($doctorId, $date)
    {
        $appointment = Appointment::where('user_id', $doctorId)->where('date', $date)->first();
        $times = Time::where('appointment_id', $appointment->id)->where('status', 0)->get();
        $user = User::where('id', $doctorId)->first();
        $doctor_id = $doctorId;

        return view('appointment', compact('times', 'date', 'user', 'doctor_id'));
    }



    /**
     *  Doctors listing by date
     */
    public function findDoctorsBasedOnDate($date)
    {
        $doctors = Appointment::where('date', $date)->get();
        return $doctors;
    }


    /**
     * Store a patient booking
     */
    public function store(Request $request)
    {
        $request->validate(['time' => 'required']);
        $check = $this->checkBookingTimeInterval();

        if ($check) {
            return redirect()->back()->with('errmessage', 'You already made an appointment. Please wait to make next appointment');
        }

        Booking::create([
            'user_id' => auth()->user()->id,
            'doctor_id' => $request->doctorId,
            'time' => $request->time,
            'date' => $request->date,
            'status' => 0      //pode ficar sem colocar pois ja esta definido no model como default 0
        ]);

        Time::where('appointment_id', $request->appointmentId)
            ->where('time', $request->time)
            ->update(['status' => 1]);         //mudar o status para 1, sempre que for feito uma marcação de consulta


        /**
         * Send email notification after a booking
         */
        $doctorName = User::where('id', $request->doctorId)->first();
        $mailData = [
            'name' => auth()->user()->name,
            'time' => $request->time,
            'date' => $request->date,
            'doctorName' => $doctorName->name

        ];
        try {
            \Mail::to(auth()->user()->email)->send(new \App\Mail\AppointmentMail($mailData)); //colocar sempre \App\Mail\AppointmentMail completo para enviar emails
        } catch (\Exception $e) {
        }

        return redirect()->back()->with('message', 'Your appointment was booked.');
    }


    /**
     * Patient can only make 1 booking per day
     */
    public function checkBookingTimeInterval()
    {
        return Booking::orderby('id', 'desc')
            ->where('user_id', auth()->user()->id)
            ->whereDate('created_at', date('Y-m-d')) //->where('date', date('Y-m-d'))
            ->exists();
    }



    /**
     * View all the bookings made by the logged in patient
     */
    public function myBookings()
    {
        $appointments = Booking::latest()->where('user_id', auth()->user()->id)->get();

        return view('booking.index', compact('appointments'));
    }

    /**
     * List patient's prescriptions in patient's dashboard
     */
    public function myPrescription()
    {
        $prescriptions = Prescription::where('user_id', auth()->user()->id)->get();

        return view('my-prescription', compact('prescriptions'));
    }
}
