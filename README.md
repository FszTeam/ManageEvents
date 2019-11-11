# ManageEvents
ManageEvents for October cms offers robust event management tools to make managing events stupid simple. With our powerful multidate system, you can recur and group entire sets of dates daily, weekly, every other week, monthly, or annually.

manageevents-plugin
===========

A powerful events management app for October CMS.

## Displaying Calendars In your Pages

The plugin includes a component eventCalendar that can display events in calendar format. You can add the component to your page and render it with the component tag:

```php
{% component 'eventCalendar' %}
```

There are several Component Settings you will want check when adding the eventCalendar Component:

* **eventPage** - the page used for viewing an individual events.
* **style** - the Calendar style.
* **calendar** - the specific calendar to add.
* **eurocal** - day of the week and date formatting for eauropean markets.

You can set up the eventCalendar component to dynamically filter by specific calendars.  To allow your Event Calendars
to filter calendars dynamically, you can add ':calendar?/' to the pages url wherein passing any calendar slug will
filter the eventCalendar output by that calendar:

```
url = "/canlendar-page/:calendar?/"
```

## Displaying Event Lists In your Pages

The plugin also includes a component eventList that can display events in list format. Add the component to your page and render it with the component tag:

```php
{% component 'eventList' %}
```

There are several Component Settings you will want check when adding the eventList Component:

* **eventPage** - the page used for viewing an individual events.
* **style** - the list style.
* **past events** - allow none (events drop of after they pass), only (only list out past events), one (include -1 day in addition to future events), two, & three.
* **calendar** - the specific calendar to filter by.
* **number of events** - number of events to list and/or number of events per page.
* **pagination** - enable paging of events.

You can set up the eventList component to dynamically filter by specific calendars.  To allow your Event Lists to
filter calendars dynamically, you can add ':calendar?/' to the pages url wherein passing any calendar slug will
filter the eventList output by that calendar:

```
url = "/event-list-page/:calendar?/"
```

## Displaying Individual Events in your Pages

The plugin additionally includes a component Event that can display any given event. Add the component to your page and render it with the component alias:

```php
{% component 'event' %}
```

There are two ways to define what event you'd like to display on a page:

* **predifined** - a predefined event ID#.
* **dynamic** - a dynamically passed event ID# from an eventList or eventCalendar component.


To determine which method you will use, define the paramId when adding the Event component:

* **paramId** - either a specific event ID# or :event_id
* **invites** - allow email invites.

If adding dynamically (you want to show individual events based on what is clicked from an eventCalendar or eventList component), you will want to ensure that the value you enter for paramId is appended to your pages url path along with a bogus slug parameter:

> **Note:** The Event Slug is for cleaner URL viewing only and has no baring on event data pulled. This param can be named anything you want but must be present between your page url and your event ID.

```php
url = /page/:event_slug/:event_id/
```

Invites are only usable by registered & logged in users to prevent your site from being abused and blacklisted. 

You can also only invite one email address at a time.  This tool is not designed for mass convenience.  To utilize this feature you will want to set the "Sender Name" & "Sender Email" in your System Settings area.



## ManageEvents Settings

To access settings click on the "System" icon, then click on "ManageEvents" under "Misc"

* **Social Settings** - here you can enter your sharethis API key, and then enable which social buttons to show.

* **Date/Time Settings** - here you can specify how dates and times display within the default ManageEvents views and lists.

* **Event Settings** - here you can define event 'fall-off'.  The time to pass by before events no longer display in your front end event list components.




## ManageEvents Integrations

ManageEvents conveniently integrates with RainLab's amazing Static Pages plugin.  You can set your Static Menu's to list
and filter your ManageEvents Calendars and their events.  Simply follow the Static Pages docs to utilize. Having both
plugins installed will provide ManageEvents Calendars as Static Menu Items.


## Quick Start

* Create a page called "events".
* Add either an eventsList or eventsCalendar component to it.
* Set that component to point to /event
* Create a page called "event". 
* Set the url of the page to be /event/:event_slug?/:event_id?/
* Add an event component to the page

That's it!  Now go create so events and off you go!!!
