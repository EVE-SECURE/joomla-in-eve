<?php

class CronHelperParser
{
	private $bits = array();
	private $bitnames	= array('minute', 'hour', 'day', 'month', 'dayweek');
	private $bitranges 	= array('0-59', '0-23', '1-31', '1-12', '0-7');
	private $monthdays = array(31,29,31,30,31,30,31,31,30,31,30,31);

	private function expand($bit, $biti)
	{
		$result = array();
		$ranges = explode(',', $bit);
		foreach ($ranges as $range) {
			$range = str_replace('*', $this->bitranges[$biti], $range);
			if (!preg_match('#(\\d+)(-(\\d+))?(/(\\d+))?#', $range, $matches)) {
				throw new Exception('Invalid bit for '.$this->bitnames[$biti]);
			}
				
			$from = $matches[1];
			$to = isset($matches[3]) && $matches[3] != 0 ? $matches[3] : $matches[1];
			$step = isset($matches[5]) ? $matches[5] : 1;
				
			for ($i = $from; $i <= $to; $i += $step) {
				$result[$i] = intval($i);
			}
				
		}
		asort($result);
		return $result;
	}

	public function parse($string)
	{
		$this->bits = array();
		$bits = preg_split('/[ \t]+/', $string);
		if (count($bits) != 5) {
			throw new Exception('5 Bits required');
		}
		foreach ($bits as $biti => $bit) {
			$expanded = $this->expand($bit, $biti);
			$this->bits[$biti] = $expanded;
		}
		return $bits;
	}

	public function __toString()
	{
		$result = '';
		foreach ($this->bits as $bit) {
			if ($result) {
				$result .= ', ';
			}
			$result .= json_encode(array_values($bit));
		}
		echo $result;
	}

	/**
	 * @param DateTime $time
	 */
	public function getNext($time)
	{
		$time->format('i H d m');

		$time = clone $time;

		$nextday = false;
		$minute = next($this->bits[0]);
		$hour = current($this->bits[0]);
		if ($minute === false) {
			$minute = reset($this->bits[0]);
			$hour = next($this->bits[1]);
			if ($hour === false) {
				$hour = reset($this->bits[1]);
				$nextday = true;
			}
		}
		$time->setTime($hour, $minute);
		if ($nextday) {
			$this->nextDay($time);
		}

	}

	/**
	 * Set bits of last call
	 *
	 * @param unknown_type $time
	 */
	private function setBits($time)
	{
		//TODO:
		$bits = $time->format('i H d m');
		foreach ($bits as $i => $bit) {
			reset($this->bits[$i]);
			while (true) {

			}
		}
	}

	public function getMinutes()
	{
		return $this->bits[0];
	}

	public function getHours()
	{
		return $this->bits[1];
	}

	public function getDays()
	{
		return $this->bits[2];
	}

	public function getMonths()
	{
		return $this->bits[3];
	}

	public function getWeekdays()
	{
		return $this->bits[4];
	}

	/**
	 * @param DateTime $time
	 */
	private function nextDay($time)
	{
		$year = $time->format('Y');
		while (true) {
			$day = next($this->bits[2]);
			$month = current($this->bits[3]);
			if (($day === false) || ($day > $this->monthdays[$month+1])) {
				$day = reset($this->bits[2]);
				$month = next($this->bits[3]);
				if ($month === false) {
					$month = reset($this->bits[3]);
					$year += 1;
				}
			} else {
				$month = current($this->bits[3]);
			}
			$time->setDate($year, $month, $day);
			$weekday = intval($time->format('w'));
			if (in_array($weekday, $this->bits[4])) {
				return;
			}
		}
	}

}