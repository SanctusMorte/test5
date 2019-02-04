<?php
/**
 * @author Zlivko Maksim
 * @package test
 *
 */
class Bootstrap
{

	// функция для вывода значений
	public function display_values ( $usd_values, $eur_values, $cad_values, $gpb_values, $another_values )
	{
		$usd_total = 0;
		$eur_total = 0;
		$cad_total = 0;
		$gpb_total = 0;

		// считаем общее значение для USD
		If ( $usd_values ) {
			foreach ( $usd_values as $value )
			{
				$usd_total += (float)$value;
			}
		} else {

		}
		$usd_total = "USD " . $usd_total;

		// считаем общее значение для EUR
		If ( $eur_values ) {
			foreach ( $eur_values as $value )
			{
				$eur_total += (float)$value;
			}
		} else {

		}
		$eur_total = "EUR " . $eur_total;

		// считаем общее значение для CAD
		If ( $cad_values ) {
			foreach ( $cad_values as $value )
			{
				$cad_total += (float)$value;
			}
		} else {

		}
		$cad_total = "CAD " . $cad_total;

		// считаем общее значение для GPB
		If ( $gpb_values ) {
			foreach ( $gpb_values as $value )
			{
				$gpb_total += (float)$value;
			}
		} else {

		}
		$gpb_total = "GPB " . $gpb_total;

		// выводим
		echo "Totals\n";
		echo $usd_total . "\n";
		echo $cad_total . "\n";
		echo $eur_total . "\n";
		echo $gpb_total . "\n";

		// если есть другие валюты, выводим их тоже
		If ( $another_values ) {
			echo "Another currency totals\n";
			foreach ($another_values as $values) 
			{
				foreach ( $values as $key => $value) 
				{
					echo $value . "\n";
				}
			}
		} else {

		}	
	}

	// функция для сбора массивов
	public function pick_arrays( $handle )
	{
		$arr_dates = array();
		$arr_narrative_1 = array();
		$arr_debits = array();
		$arr_currency = array();
		$arr_totals = array();
		$arr_finish = []; // готовый массив для вывода

		$usd_values = [];
		$eur_values = [];
		$cad_values = [];
		$gpb_values = [];
		$another_values = [];
		// До тех пор, пока не выйдет ошибка (т.е. конец документа), парсим каждое значение
		while (( $data = fgetcsv( $handle, 1000, "," )) != FALSE ) 
		{
			array_push( $arr_dates, $data[0] ); // заносим в массив 0 столбец - даты
			array_push( $arr_narrative_1, substr( $data[1], 0, 3 )); // заносим в массив 1 столбец - только первые 3 буквы
			array_push( $arr_debits, $data[8] );  // заносим в массив 8 столбец - дебиты
			array_push( $arr_currency, $data[9] ); // заносим в массив 9 столбец - валюту
			// объединям все 4 массива в один большой массив
			$arr_totals = array_merge( $arr_totals, array("dates" => $arr_dates, "pays" => $arr_narrative_1, "debits" => $arr_debits, "currency" => $arr_currency));	
		}
		
		// вычисляем ключи только тех элементов, которые равны 'PAY' (то, что нам нужно) у массива $arr_narrative_1
		$keys_narrative_1 = array_keys($arr_narrative_1, 'PAY' );
		
		// для каждого ключа в массиве $keys_narrative_1
		foreach ( $keys_narrative_1 as $key ) 
		{
			// мы сверяем с датой из общего массива
			If  ( $arr_totals["dates"][$key] == "06/03/2011" ) 
			{
				// заполняем массивы валют значениями
				If ( $arr_totals["currency"][$key] == "USD" ) {
					array_push($usd_values, $arr_totals["debits"][$key]);
				} elseif (  $arr_totals["currency"][$key] == "EUR" ) {
					array_push($eur_values, $arr_totals["debits"][$key]);
				} elseif (  $arr_totals["currency"][$key] == "CAD" ) {
					array_push($cad_values, $arr_totals["debits"][$key]);
				} elseif (  $arr_totals["currency"][$key] == "GBP" ) {
					array_push($gpb_values, $arr_totals["debits"][$key]);
				} else {
					$another_values[] = [ $arr_totals["currency"][$key] => $arr_totals["debits"][$key] . $arr_totals["currency"][$key] ];
				}		
			}
		}	

		// подключаем функцию для вывода значений	
		$this->display_values($usd_values, $eur_values, $cad_values, $gpb_values, $another_values); 	
	}


	public static function main($argv)
	{
		$file_name = "statement.csv";
		$handle = fopen( $file_name, "r" ); // открываем файл

		If ( $handle !== FALSE ) {
			echo "\n \nFile ".$file_name. " successfully opened, operation in progress... \n \n";
			$this->pick_arrays($handle); 
		} else {
			echo "\n \nOops, some kind of error occurred, the file ".$file_name. " was not found.. \n";
		}

	}
}

Bootstrap::main($argv);

