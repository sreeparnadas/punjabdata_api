<?php

namespace App\Http\Controllers;

use App\Model\ResultDetails;
use Illuminate\Http\Request;
use App\Model\ResultMaster;
use App\Model\DrawMaster;
use Illuminate\Support\Facades\DB;

class ResultDetailsController extends Controller
{
    function getPreviousDrawResult(){
        $result = DB::select(DB::raw("SELECT * FROM (SELECT draw_masters.end_time, draw_masters.display_time,draw_masters.meridiem,draw_details.id,draw_details.draw_name, result_masters.game_date,
result_details.result_row, result_details.result_col,time(result_masters.created_at) as created_at FROM result_details
INNER JOIN result_masters ON result_details.result_master_id=result_masters.id
INNER JOIN draw_details ON result_masters.draw_details_id=draw_details.id
INNER JOIN draw_masters ON draw_details.draw_master_id=draw_masters.id
WHERE result_masters.game_date=curdate() ORDER BY result_master_id DESC LIMIT 2) as table1 ORDER BY id ASC"));
        return json_encode($result,JSON_NUMERIC_CHECK);
    }
    function getTodayResult(){
        $result = DB::select(DB::raw("SELECT draw_masters.end_time, draw_details.id,draw_details.draw_name, result_masters.game_date,
result_details.result_row, result_details.result_col FROM result_details
INNER JOIN result_masters ON result_details.result_master_id=result_masters.id
INNER JOIN draw_details ON result_masters.draw_details_id=draw_details.id
INNER JOIN draw_masters ON draw_details.draw_master_id=draw_masters.id
WHERE result_masters.game_date=curdate()"));
        return $result;
    }

    function getResultByDate(request $request){
        $requestedData = (object)($request->json()->all());
        $resultDate = $requestedData->result_date;
        $reportData = DB::select((DB::raw("select
        end_time,display_time,meridiem,max(bhagyadata)as bhagyadata
        ,max(rajastani) as rajastani
        FROM(SELECT result_masters.id as result_master_id,draw_masters.id as draw_master_id,draw_masters.end_time,draw_masters.display_time,draw_masters.meridiem,
        draw_details.draw_name,
        case when draw_details.game_id=1 then (result_row*10+result_col) end as bhagyadata
        ,case when draw_details.game_id=2 then (result_row*10+result_col) end as rajastani
        ,result_details.result_row
        ,result_details.result_col
        FROM result_masters
        INNER JOIN draw_details on result_masters.draw_details_id=draw_details.id
        INNER JOIN draw_masters on draw_details.draw_master_id=draw_masters.id
        INNER JOIN result_details on result_masters.id=result_details.result_master_id WHERE result_masters.game_date="."'".$resultDate."'".")
         as table1 GROUP BY draw_master_id order by draw_master_id")));


        return json_encode($reportData,JSON_NUMERIC_CHECK);
    }
}
