<?php

use Illuminate\Database\Seeder;
use App\Model\PersonCategory;
use App\Model\Person;
use App\Model\Game;
use App\Model\PlaySeries;
use App\Model\Stockist;
use App\Model\StockistToTerminal;
use App\Model\MatrixCombination;
use App\Model\DrawMaster;
use App\Model\NextGameDraw;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
    //    personCategory
        PersonCategory::create(['person_category_name'=>'Admin']);
        PersonCategory::create(['person_category_name'=>'Developer']);
        PersonCategory::create(['person_category_name'=>'Terminal']);
        PersonCategory::create(['person_category_name'=>'Stockist']);


        //people
        Person::create(['people_unique_id'=>'C-001-ad','people_name'=>'Sachin Tendulkar','person_category_id'=>1,'user_id'=>'coder','user_password'=>'12345','default_password'=>'1234']);
        Person::create(['people_unique_id'=>'T-0001-1920','people_name'=>'test terminal','person_category_id'=>3,'user_id'=>'terminal','user_password'=>'12345','default_password'=>'1234']);

        // game
        Game::create(['game_name'=>'2D']);

        // play series
        PlaySeries::create(['series_name'=>'Jodi','game_initial' => '' ,'mrp'=> 1.00, 'winning_price'=>90, 'commision'=>5.00, 'payout'=>150,'default_payout'=>150]);
        PlaySeries::create(['series_name'=>'Single','game_initial' => '' ,'mrp'=> 1.00, 'winning_price'=>9, 'commision'=>5.00, 'payout'=>150,'default_payout'=>150]);


        // stockist
        Stockist::create(['stockist_unique_id'=>'ST-0001','stockist_name' => 'test stockist' ,'user_id'=> 1001, 'user_password'=>12345, 'serial_number'=>1, 'current_balance'=>1000,'person_category_id'=>4]);


        // stockist_to_terminal
        StockistToTerminal::create(['stockist_id'=>1,'terminal_id' => 2 ,'current_balance'=> 100, 'inforce'=>1]);

        //NextGameDraw
        NextGameDraw::create(['next_draw_id'=>2,'last_draw_id'=>1]);

        // matrix combination
        MatrixCombination::create(['row_num'=>0,'col_num' => 0]); MatrixCombination::create(['row_num'=>0,'col_num' => 1]); MatrixCombination::create(['row_num'=>0,'col_num' => 2]);
        MatrixCombination::create(['row_num'=>0,'col_num' => 3]); MatrixCombination::create(['row_num'=>0,'col_num' => 4]); MatrixCombination::create(['row_num'=>0,'col_num' => 5]);
        MatrixCombination::create(['row_num'=>0,'col_num' => 6]); MatrixCombination::create(['row_num'=>0,'col_num' => 7]); MatrixCombination::create(['row_num'=>0,'col_num' => 8]);
        MatrixCombination::create(['row_num'=>0,'col_num' => 9]);

        MatrixCombination::create(['row_num'=>1,'col_num' => 0]); MatrixCombination::create(['row_num'=>1,'col_num' => 1]); MatrixCombination::create(['row_num'=>1,'col_num' => 2]);
        MatrixCombination::create(['row_num'=>1,'col_num' => 3]); MatrixCombination::create(['row_num'=>1,'col_num' => 4]); MatrixCombination::create(['row_num'=>1,'col_num' => 5]);
        MatrixCombination::create(['row_num'=>1,'col_num' => 6]); MatrixCombination::create(['row_num'=>1,'col_num' => 7]); MatrixCombination::create(['row_num'=>1,'col_num' => 8]);
        MatrixCombination::create(['row_num'=>1,'col_num' => 9]);

        MatrixCombination::create(['row_num'=>2,'col_num' => 0]); MatrixCombination::create(['row_num'=>2,'col_num' => 1]); MatrixCombination::create(['row_num'=>2,'col_num' => 2]);
        MatrixCombination::create(['row_num'=>2,'col_num' => 3]); MatrixCombination::create(['row_num'=>2,'col_num' => 4]); MatrixCombination::create(['row_num'=>2,'col_num' => 5]);
        MatrixCombination::create(['row_num'=>2,'col_num' => 6]); MatrixCombination::create(['row_num'=>2,'col_num' => 7]); MatrixCombination::create(['row_num'=>2,'col_num' => 8]);
        MatrixCombination::create(['row_num'=>2,'col_num' => 9]);

        MatrixCombination::create(['row_num'=>3,'col_num' => 0]); MatrixCombination::create(['row_num'=>3,'col_num' => 1]); MatrixCombination::create(['row_num'=>3,'col_num' => 2]);
        MatrixCombination::create(['row_num'=>3,'col_num' => 3]); MatrixCombination::create(['row_num'=>3,'col_num' => 4]); MatrixCombination::create(['row_num'=>3,'col_num' => 5]);
        MatrixCombination::create(['row_num'=>3,'col_num' => 6]); MatrixCombination::create(['row_num'=>3,'col_num' => 7]); MatrixCombination::create(['row_num'=>3,'col_num' => 8]);
        MatrixCombination::create(['row_num'=>3,'col_num' => 9]);

        MatrixCombination::create(['row_num'=>4,'col_num' => 0]); MatrixCombination::create(['row_num'=>4,'col_num' => 1]); MatrixCombination::create(['row_num'=>4,'col_num' => 2]);
        MatrixCombination::create(['row_num'=>4,'col_num' => 3]); MatrixCombination::create(['row_num'=>4,'col_num' => 4]); MatrixCombination::create(['row_num'=>4,'col_num' => 5]);
        MatrixCombination::create(['row_num'=>4,'col_num' => 6]); MatrixCombination::create(['row_num'=>4,'col_num' => 7]); MatrixCombination::create(['row_num'=>4,'col_num' => 8]);
        MatrixCombination::create(['row_num'=>4,'col_num' => 9]);

        MatrixCombination::create(['row_num'=>5,'col_num' => 0]); MatrixCombination::create(['row_num'=>5,'col_num' => 1]); MatrixCombination::create(['row_num'=>5,'col_num' => 2]);
        MatrixCombination::create(['row_num'=>5,'col_num' => 3]); MatrixCombination::create(['row_num'=>5,'col_num' => 4]); MatrixCombination::create(['row_num'=>5,'col_num' => 5]);
        MatrixCombination::create(['row_num'=>5,'col_num' => 6]); MatrixCombination::create(['row_num'=>5,'col_num' => 7]); MatrixCombination::create(['row_num'=>5,'col_num' => 8]);
        MatrixCombination::create(['row_num'=>5,'col_num' => 9]);

        MatrixCombination::create(['row_num'=>6,'col_num' => 0]); MatrixCombination::create(['row_num'=>6,'col_num' => 1]); MatrixCombination::create(['row_num'=>6,'col_num' => 2]);
        MatrixCombination::create(['row_num'=>6,'col_num' => 3]); MatrixCombination::create(['row_num'=>6,'col_num' => 4]); MatrixCombination::create(['row_num'=>6,'col_num' => 5]);
        MatrixCombination::create(['row_num'=>6,'col_num' => 6]); MatrixCombination::create(['row_num'=>6,'col_num' => 7]); MatrixCombination::create(['row_num'=>6,'col_num' => 8]);
        MatrixCombination::create(['row_num'=>6,'col_num' => 9]);

        MatrixCombination::create(['row_num'=>7,'col_num' => 0]); MatrixCombination::create(['row_num'=>7,'col_num' => 1]); MatrixCombination::create(['row_num'=>7,'col_num' => 2]);
        MatrixCombination::create(['row_num'=>7,'col_num' => 3]); MatrixCombination::create(['row_num'=>7,'col_num' => 4]); MatrixCombination::create(['row_num'=>7,'col_num' => 5]);
        MatrixCombination::create(['row_num'=>7,'col_num' => 6]); MatrixCombination::create(['row_num'=>7,'col_num' => 7]); MatrixCombination::create(['row_num'=>7,'col_num' => 8]);
        MatrixCombination::create(['row_num'=>7,'col_num' => 9]);

        MatrixCombination::create(['row_num'=>8,'col_num' => 0]); MatrixCombination::create(['row_num'=>8,'col_num' => 1]); MatrixCombination::create(['row_num'=>8,'col_num' => 2]);
        MatrixCombination::create(['row_num'=>8,'col_num' => 3]); MatrixCombination::create(['row_num'=>8,'col_num' => 4]); MatrixCombination::create(['row_num'=>8,'col_num' => 5]);
        MatrixCombination::create(['row_num'=>8,'col_num' => 6]); MatrixCombination::create(['row_num'=>8,'col_num' => 7]); MatrixCombination::create(['row_num'=>8,'col_num' => 8]);
        MatrixCombination::create(['row_num'=>8,'col_num' => 9]);

        MatrixCombination::create(['row_num'=>9,'col_num' => 0]); MatrixCombination::create(['row_num'=>9,'col_num' => 1]); MatrixCombination::create(['row_num'=>9,'col_num' => 2]);
        MatrixCombination::create(['row_num'=>9,'col_num' => 3]); MatrixCombination::create(['row_num'=>9,'col_num' => 4]); MatrixCombination::create(['row_num'=>9,'col_num' => 5]);
        MatrixCombination::create(['row_num'=>9,'col_num' => 6]); MatrixCombination::create(['row_num'=>9,'col_num' => 7]); MatrixCombination::create(['row_num'=>9,'col_num' => 8]);
        MatrixCombination::create(['row_num'=>9,'col_num' => 9]);


//        draw master
        DrawMaster::create(['serial_number'=>1, 'draw_name'=>'BHAGYA DARPEN', 'start_time'=>'12:00:00', 'end_time'=>'09:00:00', 'meridiem'=>'AM', 'active'=>1, 'diff'=>0]);
        DrawMaster::create(['serial_number'=>2, 'draw_name'=>'RAJASTANI SAVERA', 'start_time'=>'09:00:00', 'end_time'=>'09:15:00', 'meridiem'=>'AM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>3, 'draw_name'=>'BHAGYA SUPER', 'start_time'=>'09:15:00', 'end_time'=>'09:30:00', 'meridiem'=>'AM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>4,'draw_name'=>'RAJASTANI SUBAHA', 'start_time'=>'09:30:00', 'end_time'=>'09:45:00', 'meridiem'=>'AM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>5, 'draw_name'=>'BHAGYA MORNING', 'start_time'=>'09:45:00', 'end_time'=>'10:00:00', 'meridiem'=>'AM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>6, 'draw_name'=>'BHAGYA RANI', 'start_time'=>'10:00:00', 'end_time'=>'10:15:00', 'meridiem'=>'AM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>7,'draw_name'=>'RAJASTANI SUPER', 'start_time'=>'10:15:00', 'end_time'=>'10:30:00', 'meridiem'=>'AM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>8, 'draw_name'=>'BHAGYA SANGAM', 'start_time'=>'10:30:00', 'end_time'=>'10:45:00', 'meridiem'=>'AM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>9, 'draw_name'=>'RAJASTANI VIKRAM', 'start_time'=>'10:45:00', 'end_time'=>'11:00:00', 'meridiem'=>'AM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>10, 'draw_name'=>'BHAGYA KUSUM', 'start_time'=>'11:00:00', 'end_time'=>'11:15:00', 'meridiem'=>'AM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>11, 'draw_name'=>'RAJASTANI CHETAK', 'start_time'=>'11:15:00', 'end_time'=>'11:30:00', 'meridiem'=>'AM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>12, 'draw_name'=>'BHAGYA SAMRAT', 'start_time'=>'11:30:00', 'end_time'=>'11:45:00', 'meridiem'=>'AM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>13, 'draw_name'=>'RAJASTANI SEEMA', 'start_time'=>'11:45:00', 'end_time'=>'12:00:00', 'meridiem'=>'PM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>14, 'draw_name'=>'BHAGYA REKHA', 'start_time'=>'12:00:00', 'end_time'=>'12:15:00', 'meridiem'=>'PM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>15, 'draw_name'=>'RAJASTANI SEEMAMOTKKA', 'start_time'=>'12:15:00', 'end_time'=>'12:30:00', 'meridiem'=>'PM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>16, 'draw_name'=>'BHAGYA AAKANSA', 'start_time'=>'12:30:00', 'end_time'=>'12:45:00', 'meridiem'=>'PM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>17, 'draw_name'=>'RAJASTANI BEGAM', 'start_time'=>'12:45:00', 'end_time'=>'13:00:00', 'meridiem'=>'PM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>18, 'draw_name'=>'BHAGYA RANI', 'start_time'=>'13:00:00', 'end_time'=>'13:15:00', 'meridiem'=>'PM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>19, 'draw_name'=>'RAJ KOHINOOR', 'start_time'=>'13:15:00', 'end_time'=>'13:30:00', 'meridiem'=>'PM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>20, 'draw_name'=>'BHAGYA LUCKY', 'start_time'=>'13:30:00', 'end_time'=>'13:45:00', 'meridiem'=>'PM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>21, 'draw_name'=>'RAJASTANI GANAPATI', 'start_time'=>'13:45:00', 'end_time'=>'14:00:00', 'meridiem'=>'PM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>22, 'draw_name'=>'BHAGYA SEETA', 'start_time'=>'14:00:00', 'end_time'=>'14:15:00', 'meridiem'=>'PM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>23, 'draw_name'=>'RAJASTANI GANAPATI', 'start_time'=>'14:15:00', 'end_time'=>'14:30:00', 'meridiem'=>'PM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>24, 'draw_name'=>'BHAGYA RAJA', 'start_time'=>'14:30:00', 'end_time'=>'14:45:00', 'meridiem'=>'PM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>25, 'draw_name'=>'RAJASTANI KING', 'start_time'=>'14:45:00', 'end_time'=>'15:00:00', 'meridiem'=>'PM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>26, 'draw_name'=>'BHAGYA KIRAN', 'start_time'=>'15:00:00', 'end_time'=>'15:15:00', 'meridiem'=>'PM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>27, 'draw_name'=>'RAJASTANI YANTRA', 'start_time'=>'15:15:00', 'end_time'=>'15:30:00', 'meridiem'=>'PM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>28, 'draw_name'=>'BHAGYA EXPRESS', 'start_time'=>'15:30:00', 'end_time'=>'15:45:00', 'meridiem'=>'PM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>29, 'draw_name'=>'RAJASTANI NOOR', 'start_time'=>'15:45:00', 'end_time'=>'16:00:00', 'meridiem'=>'PM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>30, 'draw_name'=>'BHAGYA JYOTI', 'start_time'=>'16:00:00', 'end_time'=>'16:15:00', 'meridiem'=>'PM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>31, 'draw_name'=>'RAJASTANI GOLD', 'start_time'=>'16:15:00', 'end_time'=>'16:30:00', 'meridiem'=>'PM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>32, 'draw_name'=>'BHAGYA CHANDI', 'start_time'=>'16:30:00', 'end_time'=>'16:45:00', 'meridiem'=>'PM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>33, 'draw_name'=>'RAJASTANI SILVER', 'start_time'=>'16:45:00', 'end_time'=>'17:00:00', 'meridiem'=>'PM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>34, 'draw_name'=>'BHAGYA DOPAHAR', 'start_time'=>'17:00:00', 'end_time'=>'17:15:00', 'meridiem'=>'PM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>35, 'draw_name'=>'RAJASTANI JIGAR', 'start_time'=>'17:15:00', 'end_time'=>'17:30:00', 'meridiem'=>'PM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>36, 'draw_name'=>'BHAGYA SUNDARAM', 'start_time'=>'17:30:00', 'end_time'=>'17:45:00', 'meridiem'=>'PM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>37, 'draw_name'=>'RAJASTANI RAJA', 'start_time'=>'17:45:00', 'end_time'=>'18:00:00', 'meridiem'=>'PM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>38, 'draw_name'=>'BHAGYA CHANRAMA', 'start_time'=>'18:00:00', 'end_time'=>'18:15:00', 'meridiem'=>'PM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>39, 'draw_name'=>'BHAGYA CHANRAMA', 'start_time'=>'18:15:00', 'end_time'=>'18:30:00', 'meridiem'=>'PM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>40, 'draw_name'=>'RAJASTANI DAILY', 'start_time'=>'18:30:00', 'end_time'=>'18:45:00', 'meridiem'=>'PM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>41, 'draw_name'=>'BHAGYA DELUX', 'start_time'=>'18:45:00', 'end_time'=>'19:00:00', 'meridiem'=>'PM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>42, 'draw_name'=>'RAJASTANI V.I.P', 'start_time'=>'19:00:00', 'end_time'=>'19:15:00', 'meridiem'=>'PM', 'active'=>0,'diff'=>0]);
        DrawMaster::create(['serial_number'=>43, 'draw_name'=>'RAJASTANI RHAINO', 'start_time'=>'19:15:00', 'end_time'=>'19:30:00', 'meridiem'=>'PM', 'active'=>0,'diff'=>0]);

    }
}
