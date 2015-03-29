# Introduction #

com\_eve implements basic basic pages for alliances, corporations and characters. Alliance and corporation contains list of its members, corporation and characters respectively. They also provide links to another components related to displayed entity. E.g. member tracking for corporation or character sheet for character.

# Details #

## Register your component ##

During installation process, insert new record to `#__eve_sections`, where
  * `name` - Identifier of section of component. This is alternate key in table and is used to identify section in your code.
  * `title` - Text of URL leading to section. This URLs are currently listed in com\_eve character, corporation, alliance or user views to access section.
  * `alias` - Segment of URL identifying the section.
  * `entity` - To what is the component related (currently character, corporation or alliance)
  * `component` - Component name without `com_eve` prefix, e.g. `charsheet` for `com_evecharsheet`
  * `view` - View variable
  * `layout` - Layout variable
  * `access` - Access level. 0 = Public, 1 = Registered, 2 = Special


## Routing URL ##

Use `EveRoute::_()` to build URLs. It will generate path containing all params for SEF URLs, or just necessary ID, if site doesn't use SEF URLs. Input parameters are
  * `$name` - see above
  * `$alliance`, `$corporation`, `$character` - $object or array($object, $prefix) containing entity ID and Name. Use _prefix_, if attributes of object are different from standard naming in entity table. E.g. 'ceo' for 'ceoID' and 'ceoName',
  * `$xhtml' - see `JRoute::_()`_

# TODO #

  1. EveRoute - FIX, doesn't allow any other parameters
  1. backend administration for links - publish, unpublish
  1. make links for owner corporations only (optional)