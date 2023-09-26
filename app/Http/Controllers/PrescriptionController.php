<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Prescription;

class PrescriptionController extends Controller
{


    /**
     * Get all the daily checked bookings (who already went to the doctor)
     */
    public function index()
    {
        $bookings = Booking::where('date', date('Y-m-d'))->where('status', 1)->where('doctor_id', auth()->user()->id)->get();

        return view('prescription.index', compact('bookings'));
    }


    /**
     * Store prescription on the database
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $data['medicine'] = implode(',', $request->medicine); //convert the array in a string separate by ',' on the DB
        //dd($data);

        Prescription::create($data);

        return redirect()->back()->with('message', 'Prescription created!');
    }

    /**
     * Display the specified resource
     */
    public function show($userId, $date)
    {
        $prescription = Prescription::where('user_id', $userId)->where('date', $date)->first();
        return view('prescription.show', compact('prescription'));
    }


    /**
     * Get all patients from prescription table
     */
    public function patientsFromPrescription()
    {
        $patients = Prescription::get();

        return view('prescription.all', compact('patients'));
    }
}
