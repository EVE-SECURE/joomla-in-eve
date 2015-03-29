# Cron Jobs #

## Setting Up ##

In 4 easy steps.
  * Download com\_cron.zip See http://code.google.com/p/joomla-in-eve/downloads/list
  * Install extension through Joomla! backend
  * Schedule an execution of `cron/index.php`
    * Unix-like systems: edit crontab and add `* * * * * php /path/to/your/joomla/cron/index.php`
    * Windows: Have a look here http://support.microsoft.com/kb/308569 how to schedule a task
    * Hosted space: Hosting will probably offer some kind of admin interface. If not, contact your hosting company. Or you could ask someone to periodically call the script externally (http://yourjoomla.org/cron/index.php).
  * _Deprecated: Add new job. Go to Joomla Administration -> Components -> Cron -> New_
  * Install (or update com\_eve). It will add the event automatically. (version 0.6 and newer)

```
Pattern: * * * * *   	
Type: cron
Plugin:
Event: onCronTick
Enabled: Yes
```

## Details ##

For some reason, Joomla! doesn't support scheduling tasks by any _normal_ way. So I had to add another entry point for Joomla!, similar to administration, installation or xmlrpc.