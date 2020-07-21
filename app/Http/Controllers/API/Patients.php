<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class Patients extends Controller
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

    public function getPatients(Request $request)
    {
        $query = DB::table('patients')->select(
            'id',
            'patient_name', 
            'address',
            'dob',
            'sex',
            'ethnicity',
            'home_phone',
            'work_phone',
            'mobile_phone',
            'email',
            'medicare_no',
            'pension_no',
            'religion',
            'usual_doctor'
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
                ->where('patient_name', 'like', '%' . $search_input . '%')
                ->orWhere('address', 'like', '%' . $search_input . '%')
                ->orWhere('dob', 'like', '%' . $search_input . '%')
                ->orWhere('sex', 'like', '%' . $search_input . '%')
                ->orWhere('ethnicity', 'like', '%' . $search_input . '%')
                ->orWhere('home_phone', 'like', '%' . $search_input . '%')
                ->orWhere('work_phone', 'like', '%' . $search_input . '%')
                ->orWhere('mobile_phone', 'like', '%' . $search_input . '%')
                ->orWhere('email', 'like', '%' . $search_input . '%')
                ->orWhere('medicare_no', 'like', '%' . $search_input . '%')
                ->orWhere('pension_no', 'like', '%' . $search_input . '%')
                ->orWhere('religion', 'like', '%' . $search_input . '%')
                ->orWhere('usual_doctor', 'like', '%' . $search_input . '%');
            });
        }

        $appointments = $query->paginate($length);
        return ['data' => $appointments];
    }

}
