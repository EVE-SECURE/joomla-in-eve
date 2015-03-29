# Introduction #

Simple _how to_
  1. Registering API CALL type
  1. Scheduling API CALL
  1. Processing result


# Details #

## Registering API CALL type ##

If extension requires new api call, you'll have to register it first. It's best to do so at component installation. Either in install.sql file, or using intall.php script. Just insert new record to `#__eve_apicalls`

## Scheduling API CALL ##

  * Global calls (/eve/AllianceList) - during component installation process
  * Character related calls (/char/CharacterSheet) - create eveapi plugin (plugins/eveapi/_name_.php) and use `onRegisterCharacter` method
  * Owner corporation related calls (/corp/MemberTracking) - create eveapi plugin (plugins/eveapi/_name_.php) and use `onSetOwnerCorporation` method

## Processing result ##

All results should be processed by in eveapi plugin. Method has to be named as call with suffixes and slashes removed: /char/CharacterSheet.xml.aspx -> `function charCharacterSheet($xml, $fromCache, $options = array())`

## Example ##

Registering API CALL
You can do it either in `evecomponent.xml`
```
<install>
  <queries>
    <query>INSERT INTO `#__eve_apicalls` (`type`, `call`, `authentication`, `authorization`, `pagination`, `delay`, `params`) VALUES ('corp', 'MemberTracking', 'Character', 'Full', NULL, 0, '');</query>
  </queries>
</install>
```
or in /admin/sql/install.sql
```
INSERT INTO `#__eve_apicalls` (`type`, `call`, `authentication`, `authorization`, `pagination`, `delay`, `params`) VALUES ('corp', 'MemberTracking', 'Character', 'Full', NULL, 0, '');

```
or in install /admin/install.php
```
function com_install() {
  $dbo = JFactory::getDBO();
  $dbo->setQuery("INSERT INTO `#__eve_apicalls` (`type`, `call`, `authentication`, `authorization`, `pagination`, `delay`, `params`) VALUES ('corp', 'MemberTracking', 'Character', 'Full', NULL, 0, '');");
  $dbo->Query();
}
```

Plugin example for `/plugins/eveapi/evecomponent.php`
```
<?php
defined('_JEXEC') or die();
jimport('joomla.plugin.plugin');

class plgEveapiEvecomponent extends JPlugin {
  function __construct($subject, $config = array()) {
    parent::__construct($subject, $config);
  }
  
  public function onRegisterAccount($userID, $apiStatus) {
    //handle evemt, when player registers new account
  }
  
  public function onRegisterCharacter($userID, $characterID) {
    //handle event, when player registers new character
  }
  
  public function onSetOwnerCorporation($userID, $characterID, $owner) {
    //handle event, when admin set/unset corporation as owner
  }
  
  public function accountCharacters($xml, $fromCache, $options = array()) {
    //example of api call handling function: /account/Characters.xml.aspx
    //see API docs for all available calls
    //also see existing plugins to get an idea, what/how to do
  }
  
}

```


# TODO #


  1. multipage calls (like assets, kills, etc.)
  1. delays