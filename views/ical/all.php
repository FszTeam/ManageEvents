<?php
header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename='.$_SERVER['HTTP_HOST'].'_calendar.ics');
echo "BEGIN:VCALENDAR\n";
echo "METHOD:PUBLISH\n";
echo "VERSION:2.0\n";
//echo "X-WR-CALNAME:SimpleEvent\n";
echo "PRODID:-//Apple Inc.//iCal 4.0.1//EN";

$tz = 'America/New_York';
foreach($events as $event){
    echo    "\nBEGIN:VEVENT\n";
    echo        "UID:".rand(1, 25000000)."\n";
    echo        "DTSTAMP:".date('Ymd')."T".date('Hms')."Z\n";
    if($event->allday==1){
        echo    "DTSTART;VALUE=DATE:".date('Ymd', strtotime($event->event_date))."\n";
        echo    "DTEND;VALUE=DATE:".date('Ymd', strtotime($event->event_date))."\n";
    }else{
        echo        "DTSTART;TZID=".$tz.":".date('Ymd', strtotime($event->event_date))."T".date('Hi', strtotime($event->sttime))."00\n";
        echo        "DTEND;TZID=".$tz.":".date('Ymd', strtotime($event->event_date))."T".date('Hi', strtotime($event->entime))."00\n";
    }
    echo        "SUMMARY:".$event->title."\n";
    //echo      "URL:".BASE_URL.DIR_REL.Loader::helper('navigation')->getLinkToCollection(Page::getByID($row['eventID'])) ."Z\n";
    echo        "DESCRIPTION:<![CDATA[".str_replace(',','\,',htmlspecialchars(strip_tags($event->excerpt)))."]]>\n";
    echo    "END:VEVENT\n";
}
    
echo "CALSCALE:GREGORIAN\n";
echo "END:VCALENDAR\n";
?>