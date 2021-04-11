<?php

namespace App\Http\Controllers;

use App\Model\ManualResultDigit;
use App\Model\PlaySeries;
use App\Model\RechargeToTerminal;
use App\Model\Stockist;
use App\Model\StockistToTerminal;
use App\Model\DrawMaster;
use App\Model\DrawDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Exception;

class ManualResultDigitController extends Controller
{
    public function getDrawTimeForManualResult(){
        $drawTime = DB::select(DB::raw("select draw_masters.id as draw_master_id,draw_details.id as draw_details_id,draw_masters.display_time,draw_masters.meridiem,draw_details.draw_name from draw_details
inner join draw_masters on draw_details.draw_master_id=draw_masters.id
where draw_details.id not in
        (select draw_details_id from result_masters where date(created_at)=(curdate())) AND draw_details.id not in
        (select draw_details_id from manual_result_digits where date(created_at)=curdate()) order by draw_masters.serial_number"));
        return json_encode($drawTime,JSON_NUMERIC_CHECK);
    }

    public function saveManualResult(request $request){
        $requestedData = (object)($request->json()->all());
        $drawDetailsId = $requestedData->master['draw_details_id'];
        $gameDate = $currentDate = Carbon::now()->format('Y-m-d');

        try
        {

            if($requestedData->master['result'] != -1){
                $manualResultDigit = new ManualResultDigit();
                $manualResultDigit->play_series_id = 1;
                $manualResultDigit->draw_details_id = $drawDetailsId;
                $manualResultDigit->result = $requestedData->master['result'];
                $manualResultDigit->game_date = $gameDate;
                $manualResultDigit->save();
            }
            DB::commit();
        }

        catch (Exception $e)
        {
            DB::rollBack();
            return response()->json(array('success' => 0, 'message' => $e->getMessage().'<br>File:-'.$e->getFile().'<br>Line:-'.$e->getLine()),401);
        }
        return response()->json(array('success' => 1, 'message' => 'Successfully recorded'),200);
    }

    public function getLastInsertedManualResult(){
        $manualResults = ManualResultDigit::select('draw_masters.id as draw_master_id','draw_details.id as draw_details_id','draw_masters.display_time',
                        'draw_masters.meridiem','draw_details.draw_name','manual_result_digits.result')
                        ->join('draw_details', 'manual_result_digits.draw_details_id', '=', 'draw_details.id')
                        ->join('draw_masters', 'draw_details.draw_master_id', '=', 'draw_masters.id')
                        ->where('draw_masters.active',1)
                        ->whereRaw("manual_result_digits.game_date = curdate()")
                        ->get();
        return json_encode($manualResults,JSON_NUMERIC_CHECK);
    }

    public function updateCurrentManual(request $request){
        $requestedData = (object)($request->json()->all());
        $drawMasterId = $requestedData->master['draw_master_id'];
        $gameDate = $currentDate = Carbon::now()->format('Y-m-d');
        $result = ($requestedData->master['aandar']*10)+$requestedData->master['bahar'];
        try
        {
            if($result){
                ManualResultDigit::where('draw_master_id',$drawMasterId)
                                ->where('play_series_id',2)
                                ->whereRaw("manual_result_digits.game_date = curdate()")
                                ->update(["result" => $result]);
            }
            DB::commit();
        }

        catch (Exception $e)
        {
            DB::rollBack();
            return response()->json(array('success' => 0, 'message' => $e->getMessage().'<br>File:-'.$e->getFile().'<br>Line:-'.$e->getLine()),401);
        }
        return response()->json(array('success' => 1, 'message' => 'Successfully updated'),200);
    }
}
