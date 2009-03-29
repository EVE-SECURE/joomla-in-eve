<?php
/**
 * @version		$Id$
 */
 
// No direct access
defined('_JEXEC') or die('Restricted access'); ?>
<form action="index.php" method="post" name="adminForm">
<h2>Current CorpID: <?php echo $this->v_corpID; ?></h2>
<h2>Current AllianceID: <?php echo $this->v_allianceID; ?></h2>
<h3><?php echo JText::_( 'Please Check the Parameters to allow registration to Corp or Alliance members.' ); ?></h3>
 
<input type="hidden" name="option" value="com_everegister" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="everegister" />
 
</form>
