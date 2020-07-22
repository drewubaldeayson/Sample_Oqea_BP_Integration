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
            ->select(
                DB::raw('LTrim(Rtrim(Patients.Firstname) + \' \' + Rtrim(Patients.Surname)) AS patient'),'Appointments.InternalID as internalid','Appointments.AppointmentDate as appointmentdate',
            'Appointments.AppointmentTime as appointmenttime','Appointments.AppointmentLength as appointmentlength',DB::raw('LTrim(Rtrim(Titles.Title) + \' \' + Rtrim(Users.Firstname) + \' \' + Rtrim(Users.Surname)) AS provider'),
            'YesNo.YesNoword AS urgent','AppointmentTypes.Description AS appointmenttype','AppointmentCodes.Description AS status','Appointments.ArrivalTime as arrivaltime',
            'Appointments.ConsultationTime as consultationtime',DB::raw('LTrim(Rtrim(Booking.Firstname) + \' \' + Rtrim(Booking.Surname)) AS bookedby'),'Appointments.COMMENT as comment','Appointments.ITEMLIST as itemlist',
            'Appointments.RecordId as recordid','Appointments.RecordStatus as recordstatus')
            ->get();
            
            var_dump("Appointment records have been fetched");

            foreach($appointments as $appointment)
            {
                $appointmentDate = str_replace(" 00:00:00.000","",trim($appointment->appointmentdate));
                $appointmentStartTime = trim(date("h:i:s",$appointment->appointmenttime));
                $appointmentLength = intval(trim($appointment->appointmentlength))/60;
                $appointmentEndTime = trim(date("h:i:s",strtotime('+'.$appointmentLength.' minutes',strtotime(trim(date("h:i:s",$appointment->appointmenttime))))));
     
                DB::connection('mysql')->table("appointments")->updateOrInsert([
                    'recordid' => trim($appointment->recordid)],[
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
                    'itemlist' => trim($appointment->itemlist),
                    'record_status' => trim($appointment->recordstatus)
                ]);
               
            }

            var_dump("Successfully inserted the appointment records");

            $patients = DB::connection('bps_mssql')
            ->table("Patients")
            ->join('Titles', 'Patients.TitleCode', '=', 'Titles.TitleCode')
            ->join('Sex', 'Patients.SexCode', '=', 'Sex.SexCode')
            ->join('PensionType', 'Patients.PensionCode', '=', 'PensionType.PensionCode')
            ->join('Ethnicity', 'Patients.EthnicCode', '=', 'Ethnicity.EthnicCode')
            ->join('PatientStatus', 'Patients.PatientStatus', '=', 'PatientStatus.StatusCode')
            ->join('Users', 'Patients.UserID', '=', 'Users.UserID')
            ->join('Titles as UserTitles', 'Users.TitleCode', '=', 'UserTitles.TitleCode')
            ->join('Email', 'Patients.InternalID', '=', 'Email.InternalID','left outer')
            ->where('Patients.FirstName','!=','\'\'')
            ->select('Patients.InternalID as internalid','Patients.ExternalID as externalid','Titles.Title as title',
            'Patients.Firstname as firstname', 'Patients.Middlename as middlename', 'Patients.Surname as surname',
            'Patients.Address1 as address1','Patients.City as city', 'Patients.Postcode as postcode',
            'Patients.DOB as dob','Sex.Sex as sex','Ethnicity.EthnicType AS ethnicity','Patients.HomePhone as homephone',
            'Patients.WorkPhone as workphone','Patients.MobilePhone as mobilephone','Email.Email as email',
            'Patients.MedicareNo as medicareno','Patients.PensionNo as pensionno','Patients.Religion as religion','Patients.Recordstatus as record_status',
            DB::raw('LTrim(Rtrim(UserTitles.Title) + \' \' + Rtrim(Users.Firstname) + \' \' + Rtrim(Users.Surname)) AS usualdoctor'))
            ->get();

            var_dump("Patient records have been fetched");

            foreach($patients as $patient)
            {
                DB::connection('mysql')->table("patients")->updateOrInsert([
                    'internal_id' => trim($patient->internalid)],[
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
                    'usual_doctor' => trim($patient->usualdoctor),
                    'record_status' => trim($patient->record_status),
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
