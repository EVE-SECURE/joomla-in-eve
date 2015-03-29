The packages will follow simple naming scheme: `com_<component name>.<major version>.<minor version>.<revision>.zip` These are the files you should use, when you need fresh install of component. Use Joomla admin interface to install the package

To make a update, download `com_<component name>.<major version>.<minor version>.<revision>-patch.zip` files. Then unpack the content of archive directly to Joomla root overwriting the old files.

Version numbers
  * `major version` will change to 1 when the core component will have all important functionality.
  * `minor version` will change in case there were some kind of database change. I'll try to provide .sql file for updates. Or maybe I'll finally learn how to make update packages for Joomla.
  * `revision` is obvious (SVN revision package was generated from)