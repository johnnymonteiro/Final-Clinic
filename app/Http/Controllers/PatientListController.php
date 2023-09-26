<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;

class PatientListController extends Controller
{


    public function index(Request $request)
    {

        if ($request->date) {
            $bookings = Booking::latest()->where('date', $request->date)->get();
            return view('admin.patientList.index', compact('bookings'));
        }

        $bookings = Booking::latest()->where('date', date('Y-m-d'))->get();

        return view('admin.patientList.index', compact('bookings'));
    }



    /**
     * Change status to 1 or 0 if patient visited the doctor
     */
    public function toggleStatus($id)
    {
        $booking = Booking::find($id);
        $booking->status = !$booking->status;
        $booking->save();

        return redirect()->back();
    }


    /**
     * Get a list, 20 each page, of all bookings made on the clinic
     */
    public function allTimeAppointment()
    {
        $bookings = Booking::latest()->paginate(20);

        return view('admin.patientList.index', compact('bookings'));
    }
}
