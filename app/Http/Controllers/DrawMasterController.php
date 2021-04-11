<?php

namespace App\Http\Controllers;

use App\Model\DrawMaster;
use App\Model\DrawDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DrawMasterController extends Controller
{
    public function __construct()
    {
//         $this->middleware('authCheck');
    }
    public function getActiveDrawTime(){
        $currentDraw = DrawMaster::where('active', 1)->first();
        return json_encode($currentDraw,JSON_NUMERIC_CHECK);
    }

    public function getActiveAndNextGame(){
        $currentDraw = DB::select("SELECT draw_details.id,draw_masters.id as draw_master_id,draw_masters.serial_number,draw_masters.start_time,draw_masters.end_time,draw_masters.meridiem
,draw_masters.active,draw_details.draw_name FROM draw_details
INNER JOIN draw_masters ON draw_details.draw_master_id=draw_masters.id
WHERE draw_masters.active=1");
        return json_encode($currentDraw,JSON_NUMERIC_CHECK);
    }

    public function getAllDrawTimes(){
        $currentDraw = DrawDetail::select('draw_details.id','draw_details.draw_name','draw_masters.serial_number','draw_masters.end_time'
            ,'draw_masters.display_time','draw_masters.meridiem')
            ->join('draw_masters','draw_details.draw_master_id','draw_masters.id')
            ->orderBy('draw_masters.serial_number')
            ->get();
//        ->toSql();
        return json_encode($currentDraw,JSON_NUMERIC_CHECK);

    }

    public function selectMissedOutDrawTime(){
        $data = DB::select("select * from draw_masters where id not in (select draw_master_id from result_masters where game_date=curdate())
order by serial_number");
        return json_encode($data,JSON_NUMERIC_CHECK);
    }

    public function activateCurrentDrawManually(request $request){
        $requestedData = (object)($request->json()->all());
        $currentDrawId = $requestedData->drawId;

        try
        {
            DB::update('UPDATE draw_masters SET active = IF(serial_number = ?, 1,0)', [$currentDrawId]);
            DB::commit();
        }

        catch (Exception $e)
        {
            DB::rollBack();
            return response()->json(array('success' => 0, 'message' => $e->getMessage().'<br>File:-'.$e->getFile().'<br>Line:-'.$e->getLine()),401);
        }
        return response()->json(array('success' => 1, 'message' => 'Successfully recorded'),200);
    }
    public function getAdvanceDrawTimes(){
        $data = DB::select("select * from draw_masters where id > (select id from draw_masters where active=1)
                    order by serial_number");
        return json_encode($data,JSON_NUMERIC_CHECK);
    }
}
