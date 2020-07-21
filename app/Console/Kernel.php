<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $appointments = DB::connection('bps_mssql')
            ->table("Appointments")
            ->join('AppointmentCodes', 'Appointments.AppointmentCode', '=', 'AppointmentCodes.AppointmentCode')
            ->join('AppointmentTypes', 'Appointments.AppointmentType', '=', 'AppointmentTypes.AppointmentCode')
            ->join('YesNo', 'Appointments.Urgent', '=', 'YesNo.YesNoCode')
            ->join('Patients', 'Appointments.InternalID', '=', 'Patients.InternalID')
            ->join('Users', 'Appointments.UserID', '=', 'Users.UserID')
            ->join('Users AS Booking', DB::raw('ABS(Appointments.CreatedBy)'), '=', 'Booking.UserID')
            ->join('Titles', 'Users.TitleCode', '=', 'Titles.TitleCode')
            ->where('Appointments.RecordStatus', '=', '1')
            ->select('Patients.Firstname as Patient')
            ->select(
                DB::raw('LTrim(Rtrim(Patients.Firstname) + \' \' + Rtrim(Patients.Surname)) AS patient'),'Appointments.InternalID as internalid','Appointments.AppointmentDate as appointmentdate',
            'Appointments.AppointmentTime as appointmenttime','Appointments.AppointmentLength as appointmentlength',DB::raw('LTrim(Rtrim(Titles.Title) + \' \' + Rtrim(Users.Firstname) + \' \' + Rtrim(Users.Surname)) AS provider'),
            'YesNo.YesNoword AS urgent','AppointmentTypes.Description AS appointmenttype','AppointmentCodes.Description AS status','Appointments.ArrivalTime as arrivaltime',
            'Appointments.ConsultationTime as consultationtime',DB::raw('LTrim(Rtrim(Booking.Firstname) + \' \' + Rtrim(Booking.Surname)) AS bookedby'),'Appointments.COMMENT as comment','Appointments.ITEMLIST as itemlist')
            ->get();
            
            var_dump("Appointment records have been fetched");

            foreach($appointments as $appointment)
            {
                $appointmentDate = str_replace(" 00:00:00.000","",trim($appointment->appointmentdate));
                $appointmentStartTime = trim(date("h:i:s",$appointment->appointmenttime));
                $appointmentLength = intval(trim($appointment->appointmentlength))/60;
                $appointmentEndTime = trim(date("h:i:s",strtotime('+'.$appointmentLength.' minutes',strtotime(trim(date("h:i:s",$appointment->appointmenttime))))));
   
                DB::connection('mysql')->table("appointments")->insertOrIgnore([
                    'patient' => trim($appointment->patient),
                    'internalid' => trim($appointment->internalid), 
                    'appointmentstartdatetime' => $appointmentDate.' '.$appointmentStartTime, 
                    'appointmentlength' => $appointmentLength. ' minutes',
                    'appointmentenddatetime' => $appointmentDate.' '.$appointmentEndTime,
                    'provider' => trim($appointment->provider), 
                    'urgent' => trim($appointment->urgent), 
                    'appointmenttype' => trim($appointment->appointmenttype), 
                    'status' => trim($appointment->status), 
                    'arrivaltime' => date("h:i:s",strtotime(trim(date("h:i:s",$appointment->arrivaltime)))),
                    'consultationtime' => date("h:i:s",strtotime(trim(date("h:i:s",$appointment->consultationtime)))),
                    'bookedby' => trim($appointment->bookedby),
                    'comment' => trim($appointment->comment),
                    'itemlist' => trim($appointment->itemlist)
                ]);
            }

            var_dump("Successfully inserted the appointment records");

            $patients = DB::connection('bps_mssql')
            ->table("BPS_Patients")
            ->select('internalid','title','firstname','middlename','surname','address1','city','postcode','dob','sex','ethnicity',
                     'homephone','workphone','mobilephone','email','medicareno','pensionno','religion','usualdoctor')
            ->get();

            var_dump("Patient records have been fetched");

            foreach($patients as $patient)
            {
                DB::connection('mysql')->table("patients")->insertOrIgnore([
                    'patient_name' => trim($patient->title).' '.trim($patient->firstname).' '.trim($patient->middlename).' '.trim($patient->surname),
                    'address' => trim($patient->address1).' '.trim($patient->city).' '.trim($patient->postcode), 
                    'dob' => trim($patient->dob), 
                    'sex' => trim($patient->sex),
                    'ethnicity' => trim($patient->ethnicity), 
                    'home_phone' => trim($patient->homephone), 
                    'work_phone' => trim($patient->workphone), 
                    'mobile_phone' => trim($patient->mobilephone),
                    'email' => trim($patient->email),
                    'medicare_no' => trim($patient->medicareno),
                    'pension_no' => trim($patient->pensionno),
                    'religion' => trim($patient->religion),
                    'usual_doctor' => trim($patient->usualdoctor)
                ]);
            }

            var_dump("Successfully inserted the patient records");

        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
