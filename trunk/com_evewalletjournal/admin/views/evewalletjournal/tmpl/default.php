<?php
/**
 * @version		$Id$
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

?>
<form action="<?php echo JRoute::_('index.php?option=com_evewalletjournal'); ?>" method="post" name="adminForm">
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>

<pre>
<?php
$sql = "INSERT INTO `bak_eve_apicalls` (`type`, `corp`, `authentication`, `authorization`, `paginationRowsetName`, `paginationAttrib`, `paginationParam`, `paginationPerPage`, `delay`, `params`) VALUES \n";
$values = array();
for ($accountKey = 1000; $accountKey <= 1006; $accountKey +=1) {
	$params = array('accountKey' => $accountKey);
	
	$values[] = sprintf("('corp', 'WalletJournal', 'Character', 'Full', 'entries', 'refID', 'beforeRefID', 1000, 0, '%s')", json_encode($params));
}
$sql .= implode(",\n", $values);
$sql .= ";";
echo $sql;
?>

</pre>