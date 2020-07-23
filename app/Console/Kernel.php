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
            ->join('NextOfKin', 'Patients.nextofkinid', '=', 'NEXTOFKIN.nextofkinid')
            ->join('Email', 'Patients.InternalID', '=', 'Email.InternalID','left outer')
            ->where('Patients.FirstName','!=','\'\'')
            ->select('Patients.InternalID as internalid','Patients.ExternalID as externalid','Titles.Title as title',
            'Patients.Firstname as firstname', 'Patients.Middlename as middlename', 'Patients.Surname as surname',
            'Patients.Address1 as address1','Patients.City as city', 'Patients.Postcode as postcode',
            'Patients.DOB as dob','Sex.Sex as sex','Ethnicity.EthnicType AS ethnicity','Patients.HomePhone as homephone',
            'Patients.WorkPhone as workphone','Patients.MobilePhone as mobilephone','Email.Email as email',
            'Patients.MedicareNo as medicareno','Patients.PensionNo as pensionno','Patients.Religion as religion','Patients.Recordstatus as record_status',
            'Patients.IHI as ihi','Patients.MedicareExpiry as medicareexpiry','Patients.countryofbirth as birthcountry','Patients.healthfundname as healthfund',
            'Patients.healthfundno as healthfundmembershipno','Patients.dvano','Patients.pensionno','Patients.pensionexpiry',
            DB::raw('CONCAT(trim(NEXTOFKIN.FIRSTNAME),\' \',trim(NEXTOFKIN.SURNAME)) as nextkin'))
            ->get();

            var_dump("Patient records have been fetched");

            foreach($patients as $patient)
            {

                DB::connection('mysql')->table("patients")->updateOrInsert([
                    'internal_id' => trim($patient->internalid)],[
                    'patient_name' => trim($patient->firstname).' '.trim($patient->middlename).' '.trim($patient->surname),
                    'address' => trim($patient->address1).' '.trim($patient->city).' '.trim($patient->postcode), 
                    'dob' => trim($patient->dob), 
                    'sex' => trim($patient->sex),
                    'ethnicity' => trim($patient->ethnicity), 
                    'home_phone' => trim($patient->homephone), 
                    'work_phone' => trim($patient->workphone), 
                    'mobile_phone' => trim($patient->mobilephone),
                    'email' => trim($patient->email),
                    'record_status' => trim($patient->record_status),
                ]);

               $patientId = DB::getPDO()->lastInsertId();

               if($patientId!=0){
                    DB::connection('mysql')->table("patient_info")->updateOrInsert([
                        'patient_id' => $patientId,
                        'ihi' => trim($patient->ihi),
                        'medicare' => trim($patient->medicareno),
                        'medicare_expiry' => trim($patient->medicareexpiry),
                        //'marital_status' => trim($patient->maritalstatus),
                        'religion' => trim($patient->religion),
                        'birth_country' => trim($patient->birthcountry),
                        // 'employment' => trim($patient->employment),
                        // 'occupation' => trim($patient->occupation),
                        'name_prefix' => trim($patient->title),
                        'health_fund' => trim($patient->healthfund),
                        'health_fund_membership_no' => trim($patient->healthfundmembershipno),
                        'dva_card_no' => trim($patient->dvano),
                        // 'dva_card_expiry' => trim($patient->dvacardexpiry),
                        // 'dva_card_type' => trim($patient->dvacardtype),
                        'pension_no' => trim($patient->pensionno),
                        // 'pension_type' => trim($patient->pensiontype),
                        'pension_expiry' => trim(str_replace(" 00:00:00.000","",$patient->pensionexpiry)),
                        'next_kin' => trim($patient->nextkin),
                        'account_responsible' => '1'
                    ]);
               }
               
            }

            var_dump("Successfully inserted the patient records");



            // $users = DB::connection('bps_mssql')
            // ->table("Users")
            // ->where('userstatus','=','1')
            // ->select('userid','userstatus','firstname','surname')
            // ->get();
            
            // var_dump("Patient records have been fetched");

            // foreach($users as $user)
            // {

            //     DB::connection('mysql')->table("users")->updateOrInsert([
            //         'userid' => trim($patient->internalid)],[
            //         'firstname' => trim($patient->firstname),
            //         'surname' => trim($patient->lastname)
            //     ]);

            //     $patientId = DB::getPDO()->lastInsertId();

            //     if($patientId!=0){
            //             DB::connection('mysql')->table("p")->updateOrInsert([
            //                 'patient_id' => $patientId,
            //                 'ihi' => trim($patient->ihi),
            //                 'medicare' => trim($patient->medicareno),
            //                 'medicare_expiry' => trim($patient->medicareexpiry),
            //                 //'marital_status' => trim($patient->maritalstatus),
            //                 'religion' => trim($patient->religion),
            //                 'birth_country' => trim($patient->birthcountry),
            //                 // 'employment' => trim($patient->employment),
            //                 // 'occupation' => trim($patient->occupation),
            //                 'name_prefix' => trim($patient->title),
            //                 'health_fund' => trim($patient->healthfund),
            //                 'health_fund_membership_no' => trim($patient->healthfundmembershipno),
            //                 'dva_card_no' => trim($patient->dvano),
            //                 // 'dva_card_expiry' => trim($patient->dvacardexpiry),
            //                 // 'dva_card_type' => trim($patient->dvacardtype),
            //                 'pension_no' => trim($patient->pensionno),
            //                 // 'pension_type' => trim($patient->pensiontype),
            //                 'pension_expiry' => trim(str_replace(" 00:00:00.000","",$patient->pensionexpiry)),
            //                 'next_kin' => trim($patient->nextkin),
            //                 'account_responsible' => '1'
            //             ]);
            //     }
               
            // }

            // var_dump("Successfully inserted the patient records");

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
