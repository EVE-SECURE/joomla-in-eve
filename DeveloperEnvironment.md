# Introduction #

This is just recommendation. It works well both on Windows and Linux (not tested on Mac).

PHP:
  * 5.2 or higher
  * phing

IDE:
  * Eclipse PDT http://www.eclipse.org/pdt/
  * Subclipse plugin http://subclipse.tigris.org/ for SVN

Install eclipse with Subclipse. Then select your working directory, preferably outside of your htdocs root. Go to SVN repository perspective, add JIE as new repository location. Checkout project trunk as new PHP project.

To set up build routine, just follow official recommendation Joomla extension development: http://docs.joomla.org/Setting_up_your_workstation_for_extension_development. You can skip the part about creating _build.xml_ file, it's already included in project.

After that copy `_`docs/joomla-in-eve.properties to project root. Fill in _testdir_ variable, this should be your testing joomla installaion. DON'T ADD THE FILE TO SVN REPOSITORY! (it's in svn:ignore) This would screw other developers.

Now you can either:
  1. Use packages to install components to Joomla.
  1. Build the whole project and use `_`docs/**.sql files to add required tables manually.**

That's it. GL&HF