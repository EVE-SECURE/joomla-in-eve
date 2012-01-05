<?php
/**
 * @version		$Id: index.php 0.1 Alpha 2008-04-27 $
 * @author		Pavol Kovalik
 * @package		Joomla! in EVE
 * @subpackage	Template
 * @copyright	Copyright (C) 2008 Pavol Kovalik. All rights reserved.
 * @license		GNU/GPL, see http://www.gnu.org/licenses/gpl.html
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

defined('_JEXEC') or die('Restricted access');

$url = clone(JURI::getInstance());
//$showRightColumn = $this->countModules('user1 or user2 or right or top');
//$showRightColumn &= JRequest::getCmd('layout') != 'form';
//$showRightColumn &= JRequest::getCmd('task') != 'edit'
$showLeftColumn = $this->countModules('left');
$showRightColumn = $this->countModules('right');

$centerSize = 800;
if($showLeftColumn) $centerSize -= 200;
if($showRightColumn) $centerSize -= 200;
?>
<?php echo '<?xml version="1.0" encoding="utf-8"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
	xml:lang="<?php echo $this->language; ?>"
	lang="<?php echo $this->language; ?>"
	dir="<?php echo $this->direction; ?>">
<head>
<jdoc:include type="head" />
<link rel="stylesheet"
	href="<?php echo $this->baseurl ?>/templates/eve_igb/css/general.css"
	type="text/css" />
</head>
<body>
<div id="header"><jdoc:include type="modules" name="top" /></div>

<div id="breadcrumb"><jdoc:include type="modules" name="breadcrumbs" />
</div>

<table>
	<colgroup>
	<?php if ($showLeftColumn): ?>
		<col width="200" />
		<?php endif; ?>
		<col width="<?php echo $centerSize;?>" />
		<?php if ($showRightColumn): ?>
		<col width="200" />
		<?php endif; ?>
	</colgroup>
	<tr>
	<?php if ($showLeftColumn): ?>
		<td><jdoc:include type="modules" name="left" /></td>
		<?php endif; ?>
		<td><jdoc:include type="message" /> <jdoc:include type="component" />
		</td>
		<?php if ($showRightColumn): ?>
		<td><jdoc:include type="modules" name="right" /></td>
		<?php endif; ?>
	</tr>
</table>

<div id="footer"><jdoc:include type="modules" name="footer" /></div>

<div id="legal">Copyright (C) 2008 <a href="evemail:Lumy">Lumy</a>. All
rights reserved.</div>
<jdoc:include type="modules" name="debug" />
</body>
</html>
