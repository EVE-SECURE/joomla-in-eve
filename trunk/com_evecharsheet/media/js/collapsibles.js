Collapsibles = {
	skills: function() {
		var list = $$('.evecharsheet-skills .skill-group');
		var headings = $$('.evecharsheet-skills h4');
		var collapsibles = new Array();
		
		headings.each( function(heading, i) {
			var collapsible = new Fx.Slide(list[i], { 
				duration: 500, 
				transition: Fx.Transitions.linear,
			});
			
			collapsibles[i] = collapsible;
			
			heading.addEvent('click', function(){
				collapsible.toggle();
				heading.toggleClass('heading-collapsed');
				heading.toggleClass('heading-expanded');
				return false;
			});
			heading.addClass('heading-collapsed');
			collapsible.hide();
			
		});
		
		$$('.collapse-all-skills').each(function(element) {
			element.onclick = function(){
				headings.each( function(heading, i) {
					collapsibles[i].hide();
					heading.addClass('heading-collapsed');
					heading.removeClass('heading-expanded');
				});
				return false;
			}
		});

		
		$$('.expand-all-skills').each(function(element) {
			element.onclick = function(){
				headings.each( function(heading, i) {
					collapsibles[i].show();
					heading.removeClass('heading-collapsed');
					heading.addClass('heading-expanded');
				});
				return false;
			}
		});
	},
	
	certificates: function() {
		var list = $$('.evecharsheet-certificates .certificate-category');
		var headings = $$('.evecharsheet-certificates h4');
		var collapsibles = new Array();
		
		headings.each( function(heading, i) {
			var collapsible = new Fx.Slide(list[i], { 
				duration: 500, 
				transition: Fx.Transitions.linear,
			});
			
			collapsibles[i] = collapsible;
			
			heading.addEvent('click', function(){
				collapsible.toggle();
				heading.toggleClass('heading-collapsed');
				heading.toggleClass('heading-expanded');
				return false;
			});
			heading.addClass('heading-collapsed');
			collapsible.hide();
			
		});
		
		$$('.collapse-all-certificates').each(function(element) {
			element.onclick = function(){
				headings.each( function(heading, i) {
					collapsibles[i].hide();
					heading.addClass('heading-collapsed');
					heading.removeClass('heading-expanded');
				});
				return false;
			}
		});

		
		$$('.expand-all-certificates').each(function(element) {
			element.onclick = function(){
				headings.each( function(heading, i) {
					collapsibles[i].show();
					heading.removeClass('heading-collapsed');
					heading.addClass('heading-expanded');
				});
				return false;
			}
		});
	},
	
	start: function() {
		Collapsibles.skills();
		Collapsibles.certificates();
	}
}

window.addEvent('domready', Collapsibles.start);