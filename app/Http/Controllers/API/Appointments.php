<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Appointment;
use DB;

class Appointments extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getAppointments(Request $request)
    {
        $query = DB::table('appointments')->select(
            'id',
            'recordid',
            'patient', 
            'internalid',
            'appointmentstartdatetime',
            'appointmentlength',
            'appointmentenddatetime',
            'provider',
            'urgent',
            'appointmenttype',
            'status',
            'arrivaltime',
            'consultationtime',
            'bookedby',
            'comment',
            'itemlist',
            'record_status'
        );
        
        if ( $request->input('showdata') ) {
            return $query->get();
        }

        $length = $request->input('length');
        $column = $request->input('column');
        $search_input = $request->input('search');
       

        if ($search_input) {
            $query->where(function($query) use ($search_input) {
                $query
                ->where('patient', 'like', '%' . $search_input . '%')
                ->orWhere('internalid', 'like', '%' . $search_input . '%')
                ->orWhere('dob', 'like', '%' . $search_input . '%')
                ->orWhere('appointmentdate', 'like', '%' . $search_input . '%')
                ->orWhere('appointmenttime', 'like', '%' . $search_input . '%')
                ->orWhere('provider', 'like', '%' . $search_input . '%')
                ->orWhere('urgent', 'like', '%' . $search_input . '%')
                ->orWhere('appointmenttype', 'like', '%' . $search_input . '%')
                ->orWhere('status', 'like', '%' . $search_input . '%')
                ->orWhere('arrivaltime', 'like', '%' . $search_input . '%')
                ->orWhere('consultationtime', 'like', '%' . $search_input . '%')
                ->orWhere('comment', 'like', '%' . $search_input . '%')
                ->orWhere('itemlist', 'like', '%' . $search_input . '%')
                ->orWhere('bookedby', 'like', '%' . $search_input . '%')
                ->orWhere('record_status', 'like', '%' . $search_input . '%');
            });
        }

        $appointments = $query->paginate($length);
        return ['data' => $appointments];
    }

    public function addRawAppointment(Request $request)
    {

        $appointmentStartDate = date_format(date_create($request['appointmentStartDateTime']),"Y-m-d");
        $appointmentStartTime = date_format(date_create($request['appointmentStartDateTime']),"H:i:s");
        $appointmentEndTime = date_format(date_create($request['appointmentEndDateTime']),"H:i:s");

        $parsedStartTime = date_parse($appointmentStartTime);
        $parsedStartTime = $parsedStartTime['hour'] * 3600 + $parsedStartTime['minute'] * 60 + $parsedStartTime['second'];
        $parsedEndTime = date_parse($appointmentEndTime);
        $parsedEndTime = $parsedEndTime['hour'] * 3600 + $parsedEndTime['minute'] * 60 + $parsedEndTime['second'];

        $appointmentLength = $parsedEndTime - $parsedStartTime;
        
        DB::connection('bps_mssql')
        ->update('EXEC BP_AddAppointment ?,?,?,?,?,?,?',
        array($appointmentStartDate,$parsedStartTime,$appointmentLength,$request['practitionerId'],$request['patientId'],
              $request['loginId'],$request['locationId']));
        return ['message' => "Appointment Added Successfully"];
    }

    public function cancelAppointment(Request $request)
    {
        $appointmentId = $request['appointmentId'];
        $loginId = $request['loginId'];

        DB::connection('bps_mssql')
        ->update('EXEC BP_CancelAppointment ?,?',
        array($appointmentId,$loginId));


        return ['message' =>'Appointment is Cancelled Successfully'];

    }

}
