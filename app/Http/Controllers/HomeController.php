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
        // $this->middleware('auth');
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

    public function show() {
        $sql = "SELECT * FROM (
            (SELECT 
            id AS id, 
            type AS type,
            left_title AS title, 
            left_cover AS cover, 
            right_title AS right_title, 
            right_cover AS right_cover,
            sort AS sort,
            UNIX_TIMESTAMP(create_time) AS time
            FROM adv_company_home_double WHERE adv_company_user_id = 58) 
            UNION ALL 
            (SELECT 
            id, 
            type, 
            title,
            cover,
            NULL,
            NULL,
            sort,
            UNIX_TIMESTAMP(create_time)
            FROM adv_company_home_single WHERE adv_company_user_id = 58) 
            )AS res
            ORDER BY sort ASC
        //     ";
        $sql1 = DB::table('adv_company_home_double AS d')->select('left_cover', 'd.id AS double_id', 'd.type AS double_type', 'd.sort AS double_sort')->where('d.adv_company_user_id', '=', 58);
        // dd($sql1);
        // dd($sql1);
        $results = DB::table('adv_company_home_single AS s')->select('cover', 's.id', 's.type', 's.sort')->where('s.adv_company_user_id', '=', 58)->unionAll($sql1)->orderby('sort', 'asc')->get();
        
        // $rows = DB::select("select * from ".$sql2);
        foreach($results as $res) {
           $res->left_cover = urldecode($res->cover); 
           dd($res);
        }

    foreach($res as $key){
        $res[$key]['cover'] = urldecode($res[$key]['cover']);
        if($res[$key]['right_cover'])
            $res[$key]['right_cover'] = urldecode($res[$key]['right_cover']);
            
        $res[$key]['time'] = date("Y年m月d日", $res[$key]['time']);
        $sort[] = $res[$key]['sort'];
        $time[] = $res[$key]['time'];
        }
    // foreach($res as $key => $value){
    //   $res[$key]['cover'] = urldecode($res[$key]['cover']);
    //   if($res[$key]['right_cover'])
    //     $res[$key]['right_cover'] = urldecode($res[$key]['right_cover']);
        
    //   $res[$key]['time'] = date("Y年m月d日", $res[$key]['time']);
    //   $sort[] = $res[$key]['sort'];
    //   $time[] = $res[$key]['time'];
    // }
    array_multisort($sort, SORT_DESC, $time, SORT_ASC, $res);
    $res = json_encode($res);
    print_r($res);
    }
}
