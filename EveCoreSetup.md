# Requirements #

Joomla! in EVE requires
  * PHP 5 or higher. (Tested on 5.2)
  * MySQL
  * Joomla! 1.5
  * CCP Static Data Dump


# Installation #

Install CronJobs. Then install com\_eve.zip as any other component.

# Setting up API account activation #
  1. Enable all EVE related plugins.
    * Cron - EVE Catches onCronTick event and fetches all scheduled api calls.
    * Eveapi - EVE Processes and stores basic api calls like _accountCharacters_, _charCharacterSheet_, _corpCorporationSheet_ and _eveAllianceList_.
    * Search - EVE Search for characters, corporations or alliances.
    * System - EVE Load Joomla! in EVE framework.
    * User - EVE - automatically blocks all new accounts upon creation.
  1. Disable _New User Account Activation_ in _Global Configuration_. EVE - User plugin will automatically disable any new Joomla! account.
  1. Register _Owner Corporation_ or _Owner Alliance_. Go to Components -> EVE -> Corporations (or Alliances) -> New (or Edit existing one). Enter corporation ID, make sure OWNER? field is set to "yes" and store.
  1. Create link to API input form in any menu. Go to Menus -> {Menu} -> New -> EVE -> API Input Form

New users will now register in two steps:
  1. Register new Joomla! account.
  1. Use the API Input Form to register their API key. Form requires username, password, EVE userID and API key to unblock Joomla! account.

Note: The one userID/API key cannot be used to unblock Joomla! account as long as it's assigned to another account. This is security measure to prevent other people to get access to your site in case they get someone's API key. Make sure your corp mates register at your site as soon as possible they god admitted to corporation.

# Keeping default account registration #
Go to Extensions -> Plugin Manager -> Disable User - EVE plugin

# Static Data Dump #

You can download here: http://wiki.eve-id.net/CCP_Database_Dump_Resources. Download it and import it. You can use different database for dump, just make sure Joomla has access to it. If you choose different database, go to Components -> EVE -> Overview -> Parameters and set _STATIC DATA DUMP DATABASE_ to name of said database. Reload the overview and check _CCP Static Data Dump Info_ to make sure all tables are imported.