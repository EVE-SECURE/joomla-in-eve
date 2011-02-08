window.addEvent('domready', function() {
	$('selectAllAccounts').addEvent('change', function() {
		for (var i = 1; i <= 7; i += 1) {
			$('roleAccountCanTake'+i).checked = this.checked;
			$('roleAccountCanQuery'+i).checked = this.checked;
		}
	});
	$('selectAllHangars').addEvent('change', function() {
		for (var i = 1; i <= 7; i += 1) {
			$('roleHangarCanTake'+i).checked = this.checked;
			$('roleHangarCanQuery'+i).checked = this.checked;
		}
	});
	$('selectAllContainers').addEvent('change', function() {
		for (var i = 1; i <= 7; i += 1) {
			$('roleContainerCanTake'+i).checked = this.checked;
		}
	});
});
