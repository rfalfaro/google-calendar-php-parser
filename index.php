<?php

/**
 * This is the Alexandria Democratic Committee (ADC) calendar parser which takes
 * the information from a public Google Calendar and manipulates the
 * feeds to display a monthly calendar
 *
 * @author   Ricardo Alfaro <ralfaro@doteagle.com>
 * @license  http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 */

// Definitions for calendar address and Google API key

// You can get your Google Calendar address here:
// https://developers.google.com/google-apps/calendar/v3/reference/calendarList/list

$calendar = '';

// You must register this application using the Google Developers Console in order to authenticate:
// https://console.developers.google.com/

$key = '';

// Set start date

$today = date('c');

// Set cutoff date

$next_month= date('c', strtotime("+1 month", strtotime($today)));

// Let's fetch the JSON file

$file_adc_events_json = 'https://www.googleapis.com/calendar/v3/calendars/'.$calendar.'/events/?key='.$key;

$the_json_file = file_get_contents($file_adc_events_json);

$json = json_decode($the_json_file,true);

$google_calendar_array = array();

// Get the timezone from the Google Calendar feed

$timezone = $json['timeZone'];

// Create a new array from the JSON file with the required fields

foreach($json['items'] as $event_entry)
{
	$start = $event_entry['start']['dateTime'];
	$end = $event_entry['end']['dateTime'];
	$title = $event_entry['summary'];
	$description = nl2br($event_entry['description']);
	$location = $event_entry['location'];
	$event_url = $event_entry['htmlLink'];

	$new_event = array('date_start'=>$start, 'date_end'=>$end, 'summary'=>$title, 'location'=>$location, 'description'=>$description,'event_url'=>$event_url);
	array_push($google_calendar_array,$new_event);
}

// Sort function - ascending by date

function date_sort ($a,$b)
{
	return strtotime($a['date_start']) - strtotime($b['date_start']);
}

// Call sort function to our array

usort($google_calendar_array, "date_sort");

// echo '<pre>'. print_r($google_calendar_array,true) .'</pre>'; 		// debug

function detectEmail($str)
{
    $mail_pattern = "/([A-z0-9\._-]+\@[A-z0-9_-]+\.)([A-z0-9\_\-\.]{1,}[A-z])/";
    $str = preg_replace($mail_pattern, '<a href="mailto:$1$2">$1$2</a>', $str);

    return $str;
}

function detectURL($str)
{
	$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
	$str = preg_replace($reg_exUrl, '<a href="$0">$0</a>', $str);
	
    return $str;
}

// If the date is within range of the original start and cutoff date then it will be displayed

foreach($google_calendar_array as $single_event)

{
	// Pre-Process the dates
	
	$startDt = new DateTime($single_event['date_start']);
	$startDt->setTimeZone ( new DateTimezone ( $timezone ) );
	$startDate = $startDt->format ( 'l, F j, Y' );
	
	$endDt = new DateTime($single_event['date_end']);
	$endDt->setTimeZone ( new DateTimezone ( $timezone ) );
	$endDate = $endDt->format ( 'l, F j, Y' );
		
	$startHour = $startDt->format ( 'g:i a' );
	$endHour = $endDt->format ( 'g:i a' );
		
	if(($single_event['date_start'] >= $today) && ($single_event['date_start'] <= $next_month))
	{
		echo('<p>');
		
		if($endDate == $startDate)
		{
			echo('<strong>'.$startDate.'</strong><br/>'.$startHour.'-'.$endHour);
		}
		else
		{
			echo('<strong>'.$startDate.' - '.$endDate.'</strong>');
		}
		
		echo('<br/>'.$single_event['summary']);
		
		if($single_event['location'] != "")
		{
			echo('<br/>'.stripslashes($single_event['location']).'<br/>');
		}
		echo('</p>');
		
		$description = $single_event['description'];
		$description = stripslashes($description);
		$description = detectEmail($description);
		$description = detectURL($description);
		echo('<p>'.$description.'</p>');
		
		echo('<a href="'.$single_event['event_url'].'">Event Details</a>');

	}

}
?>