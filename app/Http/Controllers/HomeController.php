<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $users = DB::select('select * from users where id = ?', [1]);

        $rows = DB::table('adv_company_info AS i')
        ->join('adv_company_tel AS t', 'i.adv_company_user_id', '=', 't.adv_company_user_id')
        ->select('i.adv_company_user_id','i.company_name', 'i.profile_photo', 'i.main_project', 'i.company_tab', 'i.adress', 
        'i.introduction',DB::raw('group_concat(t.name separator ";") AS contact_name'), DB::raw('group_concat(t.tel separator ";") AS contact_tel'), DB::raw('group_concat(t.job separator ";") AS contact_job'))
        ->groupby('i.adv_company_user_id','i.company_name', 'i.profile_photo', 'i.main_project', 'i.company_tab', 'i.adress', 
        'i.introduction')
        ->whereIn('i.adv_company_user_id', array(58, 61))
        ->get();

        /**input
         * contact_name[...]
         * contact_tel[...]
         * contact_job[...]
         *       
         * output
         * $temp[0 => {#236 ▼
                +"name": "423423"
                +"tel": "2424223"
                +"job": "423424"
                }]
            */
        $info = []; 
        foreach ($rows as $row) {
            $row->contact_name = explode(';', $row->contact_name);
            $row->contact_tel = explode(';', $row->contact_tel);
            $row->contact_job = explode(';', $row->contact_job);

            for($i=0; $i < count($row->contact_name); $i++){
                $temp[] = (object)array("name"=>$row->contact_name[$i], "tel"=>$row->contact_tel[$i], "job"=>$row->contact_job[$i]);
            };
            $row->contact_tel = $temp;
            
            unset($temp, $row->contact_name, $row->contact_job);
            array_push($info, $row);                      
        }

        return response()->json([
            'info' => $info
        ]);
    }
}
