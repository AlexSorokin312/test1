<?php
/**
 * @author <Sorokin Aleksei>
 * @package test
 *
 */
class Bootstrap
{
		private static $sum = ['USD'=>0, 'RUB'=>0, 'GBP'=>0, 'EUR'=>0, 'CAD'=>0];
		private static $name;
		private static $date;
	
	
	public static function main($name, $date)
	{	
		self::$name = $name;
		self::$date = self::getDate($date); //Получаем дату в том виде, в каком она содержится в файле
		self::getSumPaids(); //Заполняем массив платежей sum
		return self::$sum;	
	}
	
	
	
	private static function getDate($date)
	{
		$seconds = strtotime($date);
		$date = date("m/d/Y", $seconds);
		return $date;
	}
	
	
	private static function getSumPaids()
	{
			$paids = self::getPayid();
			if (!$paids) throw new Exception("Платежей за этот день нет");
			$count = count($paids);
			for ($i = 0; $i<$count; $i++)
			{
				$pos = strrpos($paids[$i], ',');
				$valuta = substr($paids[$i], $pos+1); //Получаем вид валюты
				$price = self::getPrice($paids[$i], $pos); //Получаем сумму оплаты
				self::$sum[$valuta] += $price; 
			}
	}
	
	
	
	private static function getPayid()
	{
			$data = self::getCSV();
			$count = count($data);
			for ($i=0; $i<$count; $i++)
			{	
				if (strpos($data[$i], self::$date) === false) continue; // Проверяем совпадает ли число платежа с полученным
				if (strpos($data[$i], 'PAYMENT')) $paids[] = $data[$i]; //Находим все платежи с референсом
			}
			return $paids; //Получили платежи в конкретный день
		}
	
	
	
	
	public static function getCSV()
	{
		$file = @fopen(self::$name, 'r');
		if (!$file) throw new Exception('Файл не найден');
		while($strings = fgetcsv($file, 0, "\n"))
			{
				$count = count($strings);
				for ($i = 0; $i<$count; $i++)
				{
					$array_paids[] = $strings[$i];
				}
			}
		fclose($file);
		return $array_paids; //Получили все записи из файла
	}
		
		
		
	private static function getPrice($pay, $pos)
	{
		$price = substr($pay, 0, $pos);
		$pos = strrpos($price, ",");
		if (strlen($price) === $pos) self::getPrice($price, $pos);
		else $price = substr($price, $pos+1);
		$price = (float) $price;
		return $price;
	}		
	
}

	try
	{
	  $sum = Bootstrap::main("Report.csv", "2017-01-09");
	} catch (Exception $e){
		echo $e->getMessage();
		}
	
	
	if ($sum)
	foreach ($sum as $key=>$value)
		{
			echo $key." ".$value."<br />";
		}

	
