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

        $info = []; 
        foreach ($rows as $row) {
            $contact_name = explode(';', $row->contact_name);
            $contact_tel = explode(';', $row->contact_tel);
            $contact_job = explode(';', $row->contact_job);

            $arr = (array)$row;
            
    
            $arr['contact_name'] = $contact_name;
            $arr['contact_tel'] = $contact_tel;
            $arr['contact_job'] = $contact_job;

            
            for($i=0; $i < count($arr['contact_name']); $i++){
                $temp[] = (object)array("name"=>$arr['contact_name'][$i], "tel"=>$arr['contact_tel'][$i], "job"=>$arr['contact_job'][$i]);
            };
            //

            $arr['tel'] = $temp;
            unset($temp, $arr['contact_job'], $arr['contact_name'], $arr['contact_tel']);

            array_push($info, $arr);        
        }

        return response()->json([
            'info' => $info
        ]);
    }
}
