<?
require_once "Zend/Loader.php";
Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_AuthSub');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_Calendar');


function createEvent ($client, $title, $desc='fb2lib.net.ru', $where,
    $startDate, $startTime, $endDate, $endTime, $tzOffset = '+3')
{
  $gdataCal = new Zend_Gdata_Calendar($client);
  $newEvent = $gdataCal->newEventEntry();
  
  $newEvent->title = $gdataCal->newTitle($title);
  $newEvent->where = array($gdataCal->newWhere($where));
  $newEvent->content = $gdataCal->newContent("$desc");
  
  $when = $gdataCal->newWhen();
  $when->startTime = "{$startDate}T{$startTime}:00.000{$tzOffset}:00";
  $when->endTime = "{$endDate}T{$endTime}:00.000{$tzOffset}:00";
  $newEvent->when = array($when);


  // Upload the event to the calendar server
  // A copy of the event as it is recorded on the server is returned
  $createdEvent = $gdataCal->insertEvent($newEvent);
  
  $createdEntryId = $createdEvent->id->text;
  //$eventId = substr($createdEntryId, -26);
  //Вычленяем ID
  $eventId = str_replace("https://www.google.com/calendar/feeds/default/private/full/", "", $createdEntryId);
  return $eventId;
}


function getEvent($client, $eventId)
{
  $gdataCal = new Zend_Gdata_Calendar($client);
  $query = $gdataCal->newEventQuery();
  $query->setUser('default');
  $query->setVisibility('private');
  $query->setProjection('full');
  $query->setEvent($eventId);


  try {
    $eventEntry = $gdataCal->getCalendarEventEntry($query);
    return $eventEntry;
  } catch (Zend_Gdata_App_Exception $e) {
    var_dump($e);
    return null;
  }
}




function setReminder($client, $eventId, $minutes=1)
{
  $gc = new Zend_Gdata_Calendar($client);
  $method = "sms";
  if ($event = getEvent($client, $eventId)) {
    $times = $event->when;
    foreach ($times as $when) {
        $reminder = $gc->newReminder();
        $reminder->setMinutes($minutes);
        $reminder->setMethod($method);
        $when->reminders = array($reminder);
    }
    $eventNew = $event->save();
    return $eventNew;
  } else {
    return null;
  }
}


function sendFunction($user, $pass, $title, $text) {
$date = date("Y-m-d");
$hour = date("H");
$minute=date("i")+2; // Устанавливается на 2 минуты позже, чтобы успело сработать напоминание
echo "$user --- $date $hour:$minute
";


$service = Zend_Gdata_Calendar::AUTH_SERVICE_NAME; // predefined service name for calendar


$client = Zend_Gdata_ClientLogin::getHttpClient($user,$pass,$service);


$eventId = createEvent($client, $title, 'fb2lib.net.ru', $text, 
   $date, "$hour:$minute", $date, "$hour:$minute", '+03' );
 
$event = setReminder($client, $eventId,1);
}




function send_sms ($title, $text) {
sendFunction('conceptviolator@gmail.com', 'para-tantra', $title, $text);
//sendFunction('user2@gmail.com', 'pass2', $title, $text);
}

?>