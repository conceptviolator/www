<?php
require_once 'src/Google_Client.php';
require_once 'src/contrib/Google_CalendarService.php';
require_once "event.php";
session_start();

$client = new Google_Client();
$client->setApplicationName("Google Calendar PHP Starter Application");

// Visit https://code.google.com/apis/console?api=calendar to generate your
// client id, client secret, and to register your redirect uri.
$client->setClientId('312153967443.apps.googleusercontent.com');
$client->setClientSecret('h3fB2Fyh6S9TfsFWpBqoRxYe');
$client->setRedirectUri('https://www.google.com/calendar');
//$client->setDeveloperKey('insert_your_developer_key');
$cal = new Google_CalendarService($client);
if (isset($_GET['logout'])) {
  unset($_SESSION['token']);
}

if (isset($_GET['code'])) {
  $client->authenticate($_GET['code']);
  $_SESSION['token'] = $client->getAccessToken();
  header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
}

if (isset($_SESSION['token'])) {
  $client->setAccessToken($_SESSION['token']);
}

if ($client->getAccessToken()) {
  $calList = $cal->calendarList->listCalendarList();
  print "<h1>Calendar List</h1><pre>" . print_r($calList, true) . "</pre>";


$_SESSION['token'] = $client->getAccessToken();
} else {
  $authUrl = $client->createAuthUrl();
  
  print "<a class='login' href='$authUrl'>Connect Me!</a>";
  /*createEvent($client, 'New Years Party',
    'Ring in the new year with Kim and I',
    'Our house',
    '2013-07-28', '21:00', '2013-07-28', '22:00', '+07' );*/
}