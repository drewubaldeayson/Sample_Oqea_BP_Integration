<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class Providers extends Controller
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

    public function getProviders(Request $request)
    {
        $query = DB::table('users')
        ->select(
            'id',
            DB::raw('CONCAT(first_name,\' \',last_name) as name')
        )->get();
        return ['data' => $query];
    }

    public function checkEmail(Request $request)
    {
        $email = $request->email;
        $hasRecord = false;

        $users = DB::connection('bps_mssql')
        ->table("Users")
        ->where('userstatus','=','1')
        ->where('email','=',$email)
        ->select('userstatus',DB::raw('trim(firstname) as firstname'),'surname as lastname','mobilephone')
        ->get();

        return $users;
    }

}
