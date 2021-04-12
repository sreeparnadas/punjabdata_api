<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAllProceduresAndFunctions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS gamepane_punjab_data.digit_barcode_report_from_terminal;
                            CREATE PROCEDURE gamepane_punjab_data.`digit_barcode_report_from_terminal`(IN `term_id` VARCHAR(100), IN `start_date` DATE, IN `end_date` DATE)
                            BEGIN
                            SELECT
                                draw_time,
                                MAX(ticket_taken_time) AS ticket_taken_time,
                                barcode_number,
                                MAX(draw_master_id) AS draw_master_id,
                                SUM(game_value) AS quantity,
                                SUM(game_value) * MAX(mrp) AS amount,
                                get_prize_value_of_barcode(barcode_number) AS prize_value,
                                GROUP_CONCAT(
                                    row_num,
                                    col_num
                                ORDER BY
                                    row_num,
                                    col_num
                                ) AS particulars,
                                MAX(is_claimed) AS is_claimed
                            FROM
                                (
                                SELECT
                                    play_masters.barcode_number,
                                    play_masters.terminal_id,
                                    play_details.play_series_id,
                                    play_series.mrp,
                                    play_masters.draw_master_id,
                                    play_masters.is_claimed,
                                    play_details.row_num,
                                    play_details.col_num,
                                    play_details.game_value,
                                    draw_masters.start_time,
                                    draw_masters.end_time AS draw_time,
                                    TIME_FORMAT(
                                        play_masters.created_at,
                                        \'%h:%i%p\'
                                    ) AS ticket_taken_time
                                FROM
                                    play_details
                                INNER JOIN(
                                    SELECT
                                        *
                                    FROM
                                        play_masters
                                    WHERE
                                        terminal_id = term_id AND DATE(play_masters.created_at) BETWEEN start_date AND end_date
                                ) play_masters
                            ON
                                play_masters.id = play_details.play_master_id
                            INNER JOIN draw_masters ON draw_masters.id = play_masters.draw_master_id
                            INNER JOIN play_series ON play_series.id = play_details.play_series_id
                            ORDER BY
                                draw_master_id
                            DESC
                            ) AS table1
                            GROUP BY
                                barcode_number,
                                draw_time
                            ORDER BY
                                draw_master_id DESC,ticket_taken_time DESC;

                            END;
        ');
        DB::unprepared('DROP PROCEDURE IF EXISTS gamepane_punjab_data.fetch_terminal_digit_total_sale;
                            CREATE PROCEDURE gamepane_punjab_data.`fetch_terminal_digit_total_sale`(IN `term_id` VARCHAR(100), IN `start_date` DATE, IN `end_date` DATE)
                            BEGIN
                            select
                            DATE_FORMAT(ticket_taken_time, "%d/%m/%Y") as ticket_taken_time
                              ,sum(game_value*mrp) as amount
                              ,get_total_prize_value_by_date(ticket_taken_time,term_id) as prize_value
                              ,terminal_net_payable_by_sale_date(ticket_taken_time,term_id) as net_payable
                              from (select play_masters.terminal_id as terminal_id,
                              play_series.commision as commision, play_series.winning_price as winning_price, play_series.mrp as mrp,play_details.game_value,
                              date(play_masters.created_at) as ticket_taken_time
                              from play_details
                              inner join play_masters ON play_masters.id = play_details.play_master_id
                              inner join play_series ON play_series.id = play_details.play_series_id
                              where date(play_masters.created_at) between start_date and end_date and terminal_id=term_id) as table1
                              group by ticket_taken_time;

                            END;
        ');
        DB::unprepared('DROP PROCEDURE IF EXISTS gamepane_punjab_data.insert_jodi_result;
                    CREATE PROCEDURE gamepane_punjab_data.`insert_jodi_result`(IN `draw_master_id` INT, IN `draw_details_id` INT)
                    BEGIN
                      Declare i INT;

                      DECLARE cell_address INT;
                      DECLARE r FLOAT;
                      DECLARE c INT;
                      DECLARE payoutValue FLOAT;
                      DECLARE last_inserted_id FLOAT;
                      DECLARE _rollback BOOL DEFAULT 0;
                        DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET _rollback = 1;
                        START TRANSACTION;
                      /*insert into result master table*/
                        insert into result_masters (
                        draw_details_id
                        ,game_id
                        ,game_date
                      ) VALUES (
                         draw_details_id
                         ,1
                        ,curdate()
                      );
                      /*end of insert into result master table*/
                      /*insert into result details table*/
                      select LAST_INSERT_ID() into last_inserted_id;
                      set @i=1;
                      SELECT payout into payoutValue FROM `play_series` where id=@i;
                      set @cell_address=get_2d_winning_cell(draw_master_id,draw_details_id, @i ,date_format(curdate(), "%Y-%m-%d"));
                      set @r=floor(@cell_address / 10);
                      set @c=@cell_address % 10;
                       insert into result_details (
                        result_master_id
                        ,play_series_id
                        ,result_row
                        ,result_col
                        ,payout
                      ) VALUES (
                         last_inserted_id
                         ,@i
                        ,@r
                        ,@c
                        ,payoutValue
                      );
                        IF _rollback THEN
                            ROLLBACK;
                        ELSE
                            COMMIT;
                        END IF;
                    END;
        ');
        DB::unprepared('DROP FUNCTION IF EXISTS gamepane_punjab_data.get_2d_winning_cell;
                CREATE FUNCTION gamepane_punjab_data.`get_2d_winning_cell`(`draw_id` INT, `in_draw_details_id` INT, `series_id` INT, `draw_date` DATE) RETURNS int
                    READS SQL DATA
                    DETERMINISTIC
                BEGIN
                              DECLARE cell_address INT;
                              DECLARE target_value INT;
                              DECLARE winning_row INT;
                              DECLARE winning_col INT;
                              DECLARE val INT;
                              select get_winning_value(draw_id, series_id, draw_date) into target_value;
                              select result into cell_address from manual_result_digits where play_series_id=series_id and draw_details_id=in_draw_details_id
                              and game_date=curdate();

                              return cell_address;
                END;
        ');

        DB::unprepared('DROP FUNCTION IF EXISTS gamepane_punjab_data.get_2d_winning_cell_bk;
                        CREATE FUNCTION gamepane_punjab_data.`get_2d_winning_cell_bk`(`draw_id` INT, `series_id` INT, `draw_date` DATE) RETURNS int
                            READS SQL DATA
                            DETERMINISTIC
                        BEGIN
                                      DECLARE cell_address INT;
                                      DECLARE target_value INT;
                                      DECLARE winning_row INT;
                                      DECLARE winning_col INT;
                                      DECLARE val INT;
                                      select get_winning_value(draw_id, series_id, draw_date) into target_value;
                                      select result into cell_address from manual_result_digits
                                      where play_series_id=series_id and draw_master_id=draw_id and game_date=draw_date;
                                      IF cell_address IS NULL THEN
                                         /*Get the matching value*/
                                        select
                                        sum(play_details.game_value) as game_value into val
                                        from play_details
                                        inner join play_masters ON play_masters.id = play_details.play_master_id
                                        inner join play_series ON play_series.id = play_details.play_series_id
                                        where play_masters.draw_master_id=draw_id and play_series.id=series_id and date(play_masters.created_at)=draw_date
                                        group by play_details.row_num, play_details.col_num
                                        having game_value<=target_value order by game_value desc limit 1;
                                      /*End of get the matching value*/
                                      /*Fetch the final row and column both and set the cell*/
                                        select row_num,col_num into winning_row,winning_col from (select play_details.row_num,play_details.col_num,
                                        sum(play_details.game_value) as game_value
                                        from play_details
                                        inner join play_masters ON play_masters.id = play_details.play_master_id
                                        inner join play_series ON play_series.id = play_details.play_series_id
                                        where play_masters.draw_master_id=draw_id and play_series.id=series_id and date(play_masters.created_at)=draw_date
                                        group by play_details.row_num, play_details.col_num
                                        having game_value = val order by rand() limit 1) as table1;
                                        SET cell_address = 10 * winning_row + winning_col;
                                      END IF;
                                      /* If do not get the closest lower value of the target value then select blank cell*/
                                      IF cell_address IS NULL THEN
                                        SET cell_address= get_jodi_null_cell(draw_id, series_id, draw_date);
                                      END IF;
                                      /* End of  selecting the blank cell*/
                                      /* If no equal or closest lower value OR no blank cell then select the closest greater value*/
                                      IF cell_address IS NULL THEN
                                         /*Get the matching value*/
                                        select sum(play_details.game_value) as game_value into val
                                        from play_details
                                        inner join play_masters ON play_masters.id = play_details.play_master_id
                                        inner join play_series ON play_series.id = play_details.play_series_id
                                        where play_masters.draw_master_id=draw_id and play_details.play_series_id=series_id
                                        and date(play_masters.created_at)=draw_date
                                        group by play_details.row_num, play_details.col_num
                                        having game_value > target_value order by game_value asc limit 1;
                                      /*End of get the matching value*/
                                      /*Fetch the final row and column both and set the cell*/
                                        select row_num,col_num into winning_row,winning_col from (select play_details.row_num,play_details.col_num,
                                        sum(play_details.game_value) as game_value
                                        from play_details
                                        inner join play_masters ON play_masters.id = play_details.play_master_id
                                        inner join play_series ON play_series.id = play_details.play_series_id
                                        where play_masters.draw_master_id=draw_id and play_series.id=series_id and date(play_masters.created_at)=draw_date
                                        group by play_details.row_num, play_details.col_num
                                        having game_value = val order by rand() limit 1)as table1;
                                        SET cell_address = 10 * winning_row + winning_col;
                                      END IF;
                                        RETURN cell_address;
                                    END;
        ');

        DB::unprepared('DROP FUNCTION IF EXISTS gamepane_punjab_data.get_jodi_null_cell;
                    CREATE FUNCTION gamepane_punjab_data.`get_jodi_null_cell`(`draw_id` INT, `series_id` INT, `draw_date` DATE) RETURNS int
                        READS SQL DATA
                        DETERMINISTIC
                    BEGIN
                                DECLARE winning_row INT;
                              DECLARE winning_col INT;
                              DECLARE cell_address INT;
                              select row_num,col_num into winning_row,winning_col from(select row_num,col_num from matrix_combinations where not EXISTS
                              (select row_num,col_num from(select
                              play_details.row_num
                              , play_details.col_num
                              , sum(play_details.game_value) as game_value
                              from play_details
                              inner join play_masters ON play_masters.id = play_details.play_master_id
                              inner join play_series ON play_series.id = play_details.play_series_id
                              where play_masters.draw_master_id = draw_id AND play_series.id = series_id
                              AND date(play_masters.created_at) = draw_date
                               group by play_details.row_num, play_details.col_num order by row_num)as table1
                               where table1.col_num = matrix_combinations.col_num and table1.row_num = matrix_combinations.row_num)) as table2
                    order by rand() limit 1;
                    SET cell_address = 10 * winning_row + winning_col;
                    RETURN cell_address;
                    END;
        ');
        DB::unprepared('DROP FUNCTION IF EXISTS gamepane_punjab_data.get_prize_value_of_barcode;
                    CREATE FUNCTION gamepane_punjab_data.`get_prize_value_of_barcode`(`barcode` VARCHAR(30)) RETURNS double
                        READS SQL DATA
                        DETERMINISTIC
                    BEGIN

                     DECLARE prize_value DOUBLE;
                     DECLARE secLastTotal INT;
                     DECLARE lastTotal INT;
                      SET @sr_id=\'\';
                      SET @draw_id=\'\';
                      SET @draw_date=\'\';
                      SET @target_row=\'\';
                      SET @target_col=\'\';

                      select max(play_details.play_series_id),max(play_masters.draw_master_id),date(max(play_masters.created_at))
                      into @sr_id, @draw_id, @draw_date from play_details
                      inner join (select * from play_masters where barcode_number=barcode) play_masters
                      ON play_masters.id = play_details.play_master_id
                      inner join play_series ON play_series.id = play_details.play_series_id;

                     select select_result_row(@draw_date,@draw_id) into @target_row;
                     select select_result_column(@draw_date,@draw_id) into @target_col;

                      IF @sr_id=1 THEN

                        select (play_details.game_value * play_series.winning_price) into prize_value from play_details
                        inner join play_series ON play_series.id = play_details.play_series_id
                        inner join play_masters on play_masters.id=play_details.play_master_id
                        where play_masters.barcode_number= barcode and row_num=@target_row and col_num=@target_col;
                      ELSE
                        SELECT sum(val_one)*max(play_series.winning_price) into secLastTotal FROM play_details
                        inner join play_masters on play_masters.id=play_details.play_master_id
                        inner join play_series ON play_series.id = play_details.play_series_id
                        where play_masters.barcode_number=barcode and play_details.row_num=@target_row;

                        SELECT sum(val_two)*max(play_series.winning_price) into lastTotal FROM play_details
                        inner join play_masters on play_masters.id=play_details.play_master_id
                        inner join play_series ON play_series.id = play_details.play_series_id
                        where play_masters.barcode_number=barcode and play_details.col_num=@target_col;
                        IF secLastTotal IS NULL THEN
                          SET secLastTotal = 0;
                        END IF;
                        IF lastTotal IS NULL THEN
                          SET lastTotal = 0;
                        END IF;
                        SET prize_value=(secLastTotal + lastTotal);
                      END IF;

                       IF prize_value IS NOT NULL THEN
                        RETURN prize_value;
                      ELSE
                        RETURN 0;
                      END IF;

                    END;
        ');
        DB::unprepared('DROP FUNCTION IF EXISTS gamepane_punjab_data.get_winning_value;
                        CREATE FUNCTION gamepane_punjab_data.`get_winning_value`(`draw_id` INT, `series_id` INT, `draw_date` DATE) RETURNS int
                            READS SQL DATA
                            DETERMINISTIC
                        BEGIN
                          DECLARE total_sale INT;
                           select sum(game_value)*mrp into total_sale from play_details
                         inner join play_masters ON play_details.play_master_id=play_masters.id
                         inner join play_series ON play_details.play_series_id=play_series.id
                        where play_masters.draw_master_id= draw_id and play_details.play_series_id= series_id
                         and date(play_masters.created_at)= draw_date
                         group by play_details.play_series_id,play_series.mrp;
                         IF total_sale IS NOT NULL THEN
                         select total_sale*commision/100,payout/100,winning_price into @commision,@p,@winning_price
                          from play_series where id = series_id;
                          set @winning_price_on = (total_sale - @commision)*@p;
                          select truncate(@winning_price_on/@winning_price,0) into @winning_value;
                          IF @winning_value IS NOT NULL THEN
                          RETURN @winning_value;
                          ELSE
                          RETURN 0;
                          END IF;
                          ELSE
                         return 0;
                          END IF;
                        END;
        ');
        DB::unprepared('DROP FUNCTION IF EXISTS gamepane_punjab_data.select_result_column;
                    CREATE FUNCTION gamepane_punjab_data.`select_result_column`(`draw_date` DATE, `draw_id` INT) RETURNS int
                        READS SQL DATA
                        DETERMINISTIC
                    BEGIN
                      DECLARE col INT;

                    select
                      result_details.result_col into col
                      from result_details
                      inner join (select * from result_masters where game_date=draw_date
                      and draw_master_id=draw_id) result_masters on result_details.result_master_id = result_masters.id
                    order by result_masters.draw_master_id;

                        RETURN col;
                    END;
        ');
        DB::unprepared('DROP FUNCTION IF EXISTS gamepane_punjab_data.select_result_row;
                    CREATE FUNCTION gamepane_punjab_data.`select_result_row`(`draw_date` DATE, `draw_id` INT) RETURNS int
                        READS SQL DATA
                        DETERMINISTIC
                    BEGIN
                      DECLARE r INT;

                      select
                      result_details.result_row into r
                      from result_details
                      inner join (select * from result_masters where game_date=draw_date and draw_master_id=draw_id) result_masters
                      on result_details.result_master_id = result_masters.id
                    order by result_masters.draw_master_id;
                    RETURN r;
                    END;
        ');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('all_procedures_and_functions');
    }
}
