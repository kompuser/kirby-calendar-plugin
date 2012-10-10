<?php

//TODO custom templates
function calendar($events, $options=array(), $template='table') {
		$calendar = new calendar($events, $options);
		echo (empty($events)) ? $calendar->noEntry() : $calendar->cal($template);
}

class calendar {
	
	//default formats. see the doc of strftime() for the formatting syntax.
	var $dDateFormat = '%d-%m-%Y';
	var $dMonthFormat = '%B';
	
	//other defaults
	var $beginEndTimeDelimiter = '->';
	var $dNoEntryMsg = 'No entry.';

	var $events = false;
	var $status = array();
	var $lang = false;
	var $timezone = false;
	var $dateFormat = false;
	var $monthFormat = false;
	var $hasTime = false;
	var $noEntryMsg = false;
	
	function __construct($cEvents, $cOptions=array()) {
		if (!$cEvents) return false;
		
		$this->events = $this->parseEvents($cEvents);
		
		$this->lang = @$cOptions['lang'];
		$this->timezone = @$cOptions['timezone'];
		$this->dateFormat = @$cOptions['date'];
		$this->monthFormat = @$cOptions['month'];
		$this->hasTime = @$cOptions['time'];
		
		//TODO multilanguage noEntryMsg
		$this->noEntryMsg = $this->dNoEntryMsg;
		
		$this->configure();
	}
	
	//TODO errormessages
	function configure() {
		if ($this->lang) {
			setLocale(LC_TIME, $this->lang);
		}
		
		if ($this->timezone) {
			date_default_timezone_set($this->timezone);
		}
		
		if (!$this->dateFormat) $this->dateFormat = $this->dDateFormat;
		
		if (!$this->monthFormat) $this->monthFormat = $this->dMonthFormat;
	}
	
	function eventSort($a, $b) {
		return $a['begin'] - $b['begin'];
	}
	
	function parseEvents($events) {
		$eventsObject = new ArrayObject();
		
		foreach ($events as $time => $event) {
			//XXX 0 = $this->parseTime($time)
        	$eventsObject[0] = $event;
        }
        
        $eventsObject->uksort(array($this, 'eventSort'));
        
        return $eventsObject;
	}
	
	function parseTime($time) {
		$timesArray = explode($this->beginEndTimeDelimiter, $time);
		
		$timesKey = array(
			'begin' => $this->timestamp($timesArray[0]),
			'end'	=> $this->timestamp(@$timesArray[1])
			);
			
		var_dump($timesKey);

		return new ArrayObject(
			
		);
	}
	
	//TODO detect time automaticly
	function timestamp($time) {
		if ($time) {
			return ($this->hasTime) 
				? strtotime($time)
				: strtotime('+23 hours 59 minutes', strtotime($time));
		} else {
			return false;
		}
	}
	
	function noEntry() {
		echo $this->noEntryMsg;
	}
	
	//TODO allow custom templates
	function cal($template) {
		if ($template === 'table') {
			return $this->table();
		} else {
			return 'template not supported';
		}
	}
	
	//TODO headlines. output sorted by headlines.
	function table() {
		$table = false;
		$month = false;
		
		$table .= "<table class=\"calendar\">\n";
		
		foreach ($this->events as $date => $event) {
			$tempMonth = strftime($this->monthFormat, $date);
			if ($month != $tempMonth) {
				$month = $tempMonth;
				$table .= "\t<tr class=\"month\">\n\t\t<td>".$month.
					"</td>\n\t</tr>\n";
			}
			
			$table .= "\t<tr";
			$table .= 
				(($date['end']) ? time() > $date['end'] : time() > $date['begin'])
					? " class=\"past\">\n"
					: ">\n";
			
			$table .= "\t\t<td class=\"date\">".strftime($this->dateFormat, $date['begin']);
			$table .= ($date['end']) ? ' - '.strftime($this->dateFormat, $date['end']) : '';
			$table .= "</td>\n";
			
			foreach ($event as $entry) {
				$table .= "\t\t<td>".$entry."</td>\n";
			}
		}
		
		$table .= "</table>";
		
		return $table;
	}
}

?>
