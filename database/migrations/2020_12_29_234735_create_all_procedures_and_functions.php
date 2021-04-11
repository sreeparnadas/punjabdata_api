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
        DB::unprepared('DROP FUNCTION IF EXISTS get_winning_value;
            CREATE FUNCTION get_winning_value (draw_id INT,series_id INT,draw_date DATE) RETURNS INT
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
END'
        );
        DB::unprepared('DROP FUNCTION IF EXISTS get_jodi_null_cell;
            CREATE FUNCTION get_jodi_null_cell (draw_id INT,series_id INT,draw_date DATE) RETURNS INT
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
            END'
        );
        DB::unprepared('DROP FUNCTION IF EXISTS  get_2d_winning_cell;
            CREATE FUNCTION  get_2d_winning_cell (draw_id INT,series_id INT,draw_date DATE) RETURNS INT
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
            END'
        );
        DB::unprepared('DROP PROCEDURE IF EXISTS  insert_jodi_result;
                        CREATE PROCEDURE  insert_jodi_result(IN draw_id INT)
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
    draw_master_id
    ,game_id
    ,game_date
  ) VALUES (
     draw_id
     ,1
    ,curdate()
  );
  /*end of insert into result master table*/
  /*insert into result details table*/
  select LAST_INSERT_ID() into last_inserted_id;
  set @i=1;
  SELECT payout into payoutValue FROM `play_series` where id=1;
  set @cell_address=get_2d_winning_cell(draw_id, @i ,date_format(curdate(), "%Y-%m-%d"));
  set @r=floor(@cell_address / 10);
  set @c=@cell_address % 10;
   insert into result_details (
    result_master_id
    ,result_row
    ,result_col
    ,payout
  ) VALUES (
     last_inserted_id
    ,@r
    ,@c
    ,payoutValue
  );
    IF _rollback THEN
        ROLLBACK;
    ELSE
        COMMIT;
    END IF;
END');

        DB::unprepared('DROP PROCEDURE IF EXISTS  customer_sale_report_from_admin;
                        CREATE PROCEDURE  customer_sale_report_from_admin( IN start_date DATE, IN end_date DATE)
                        BEGIN
    SELECT
        *
    FROM
        (
        SELECT
            MAX(user_id) AS user_id,
            MAX(agent_name) AS agent_name,
            COALESCE(terminal_id, \'Grand Total\') AS terminal_id,
            MAX(stockist_user_id) AS stockist_user_id,
            COALESCE(ticket_taken_time, \'Total\') AS ticket_taken_time,
            SUM(amount) AS amount,
            SUM(commision) AS commision,
            SUM(prize_value) AS prize_value,
            SUM(net_payable) AS net_payable,
            MAX(record_time) AS record_time
        FROM
            (
            SELECT
                \'digit\' AS game_name,
                user_id,
                agent_name,
                MAX(stockist_user_id) AS stockist_user_id,
                terminal_id,
                CAST(ticket_taken_time AS DATE) AS ticket_taken_time,
                terminal_total_sale_by_date(ticket_taken_time, terminal_id) AS amount,
                terminal_commission_by_sale_date(ticket_taken_time, terminal_id) AS commision,
                get_total_prize_value_by_date(ticket_taken_time, terminal_id) AS prize_value,
                terminal_net_payable_by_sale_date(ticket_taken_time, terminal_id) AS net_payable,
                MAX(record_time) AS record_time
            FROM
                (
                SELECT
                    *
                FROM
                    digit_table
                WHERE
                    ticket_taken_time BETWEEN start_date AND end_date
                ORDER BY
                    record_time
            ) AS table1
        GROUP BY
            terminal_id,
            ticket_taken_time
        ) AS table2
    GROUP BY
        terminal_id,
        ticket_taken_time WITH ROLLUP
    ) AS table3
ORDER BY
    terminal_id,
    ticket_taken_time ;
END');

        DB::unprepared('DROP PROCEDURE IF EXISTS  secondLastTotal;
                        CREATE PROCEDURE  secondLastTotal( IN draw_id INT)
                        BEGIN
 select \'aandar\' as box, max(zero)zero,max(one)as one,max(two) two,max(three) three,max(four)four,max(five)five,
max(six)six,max(seven)seven,max(eight)eight,max(nine)nine from
(select (CASE when (row_num = 0) Then val_one ELSE 0 END) as \'zero\', (CASE when (row_num = 1) Then val_one ELSE 0 END) as \'one\',
(CASE when (row_num = 2) Then val_one ELSE 0 END) as \'two\', (CASE when (row_num = 3) Then val_one ELSE 0 END) as \'three\',
(CASE when (row_num = 4) Then val_one ELSE 0 END) as \'four\', (CASE when (row_num = 5) Then val_one ELSE 0 END) as \'five\',
(CASE when (row_num = 6) Then val_one ELSE 0 END) as \'six\', (CASE when (row_num = 7) Then val_one ELSE 0 END) as \'seven\',
(CASE when (row_num = 8) Then val_one ELSE 0 END) as \'eight\', (CASE when (row_num = 9) Then val_one ELSE 0 END) as \'nine\'
from (select row_num,sum(val_one) as val_one from play_details inner join
 (select * from play_masters where date(created_at)=curdate() and draw_master_id=draw_id)play_masters
 on play_details.play_master_id=play_masters.id group by row_num) t1 group by row_num) t1
 union
 select \'bahar\' as box,max(zero)zero,max(one)as one,max(two) two,max(three) three,max(four)four,max(five)five,
max(six)six,max(seven)seven,max(eight)eight,max(nine)nine from
(select (CASE when (col_num = 0) Then val_two ELSE 0 END) as \'zero\', (CASE when (col_num = 1) Then val_two ELSE 0 END) as \'one\',
(CASE when (col_num = 2) Then val_two ELSE 0 END) as \'two\', (CASE when (col_num = 3) Then val_two ELSE 0 END) as \'three\',
(CASE when (col_num = 4) Then val_two ELSE 0 END) as \'four\', (CASE when (col_num = 5) Then val_two ELSE 0 END) as \'five\',
(CASE when (col_num = 6) Then val_two ELSE 0 END) as \'six\', (CASE when (col_num = 7) Then val_two ELSE 0 END) as \'seven\',
(CASE when (col_num = 8) Then val_two ELSE 0 END) as \'eight\', (CASE when (col_num = 9) Then val_two ELSE 0 END) as \'nine\'
from (select col_num,sum(val_two) as val_two from play_details inner join
 (select * from play_masters where date(created_at)=curdate() and draw_master_id=draw_id)play_masters
 on play_details.play_master_id=play_masters.id group by col_num) t1 group by col_num) t1;
END');






        DB::unprepared('DROP FUNCTION IF EXISTS   get_prize_value_of_barcode;
            CREATE FUNCTION get_prize_value_of_barcode (barcode VARCHAR(30)) RETURNS DOUBLE
            READS SQL DATA
            DETERMINISTIC
            BEGIN

 DECLARE prize_value DOUBLE;
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

 select select_result_row(@draw_date, @sr_id, @draw_id) into @target_row;
  select select_result_column(@draw_date, @sr_id, @draw_id) into @target_col;

  select (play_details.game_value * play_series.winning_price) into prize_value from play_details
  inner join play_series ON play_series.id = play_details.play_series_id
  inner join play_masters on play_masters.id=play_details.play_master_id
  where play_masters.barcode_number= barcode and row_num=@target_row and col_num=@target_col;

 IF prize_value IS NOT NULL THEN
    RETURN prize_value;
  ELSE
    RETURN 0;
  END IF;

END'
        );

        DB::unprepared('DROP FUNCTION IF EXISTS    select_result_column;
            CREATE FUNCTION  select_result_column (draw_date date,series_id int,draw_id int) RETURNS int
            READS SQL DATA
            DETERMINISTIC
            BEGIN
  DECLARE col INT;

select
  result_details.result_col into col
  from result_details
  inner join (select * from result_masters where game_date=draw_date
  and draw_master_id=draw_id) result_masters on result_details.result_master_id = result_masters.id
  inner join play_series on result_details.play_series_id = play_series.id
  where result_details.play_series_id= series_id
  order by result_masters.draw_master_id,result_details.play_series_id;

	RETURN col;
END'
        );

        DB::unprepared('DROP FUNCTION IF EXISTS     select_result_row;
            CREATE FUNCTION   select_result_row (draw_date date,series_id int,draw_id int) RETURNS int
            READS SQL DATA
            DETERMINISTIC
            BEGIN
  DECLARE r INT;

  select
  result_details.result_row into r
  from result_details
  inner join (select * from result_masters where game_date=draw_date and draw_master_id=draw_id) result_masters
  on result_details.result_master_id = result_masters.id
  inner join play_series on result_details.play_series_id = play_series.id
  where result_details.play_series_id=series_id
  order by result_masters.draw_master_id,result_details.play_series_id;


	RETURN r;
END'
        );

        DB::unprepared('DROP FUNCTION IF EXISTS  terminal_commission_by_sale_date;
            CREATE FUNCTION  terminal_commission_by_sale_date (sale_date date,terminal_id varchar(20)) RETURNS double
            READS SQL DATA
            DETERMINISTIC
            BEGIN
  DECLARE r INT;

  select
  result_details.result_row into r
  from result_details
  inner join (select * from result_masters where game_date=draw_date and draw_master_id=draw_id) result_masters
  on result_details.result_master_id = result_masters.id
  inner join play_series on result_details.play_series_id = play_series.id
  where result_details.play_series_id=series_id
  order by result_masters.draw_master_id,result_details.play_series_id;


	RETURN r;
END'
        );

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
