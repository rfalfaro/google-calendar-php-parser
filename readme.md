## Google Calendar API parser for PHP

This was a weekend project originally developed for the Alexandria Democratic Committee.  The idea is to fetch a public Google Calendar feed and manipulate its response to display the upcoming events for the next month.  It also includes two functions that automatically detects and transforms e-mail and URL addresses into the correct HTML code within an event description.

## Required fields 
You will need a Google Calendar API key and the correct Google Calendar address for the following two fields:

[$calendar] = Google Calendar Address
[$key] = Google Calendar API key

You can get the Google Calendar Address at https://developers.google.com/google-apps/calendar/v3/reference/calendarList/list and the Google Calendar API key at https://console.developers.google.com/.