/*
	Class:    	ContextMenu
	Author:   	David Walsh
	Website:    http://davidwalsh.name
	Version:  	1.0
	Date:     	1/20/2009
	
	SAMPLE USAGE AT BOTTOM OF THIS FILE
	
*/


var ContextMenu = new Class({

	//implements
	Implements: [Options,Events],

	//options
	options: {
		actions: {},
		menu: 'contextmenu',
		stopEvent: true,
		targets: 'body',
		trigger: 'contextmenu',
		offsets: { x:0, y:0 },
		onShow: Function.empty,
		onHide: Function.empty,
		onClick: Function.empty,
		fadeSpeed: 200
	},
	
	//initialization
	initialize: function(options) {
		//set options
		this.setOptions(options)
		
		//option diffs menu
		this.menu = $(this.options.menu);
		this.targets = $$(this.options.targets);
		
		//fx
		//this.fx = new Fx.Tween(this.menu, { property: 'opacity', duration:this.options.fadeSpeed });
		//this.fx = new Fx.Style(this.menu, {property: 'opacity', duration:this.options.fadeSpeed, transition: Fx.Transitions.Elastic.easeOut, link:'chain'});
		this.fx = new Fx.Style(this.menu, 'opacity', {duration:this.options.fadeSpeed});
		
		//hide and begin the listener
		this.hide().startListener();
		
		//hide the menu
		this.menu.setStyles({ 'position':'absolute','top':'-900000px', 'display':'block' });
	},
	
	//get things started
	startListener: function() {
		/* all elemnts */
		this.targets.each(function(el) {
			/* show the menu */
			el.addEvent(this.options.trigger,function(e) {
				//enabled?
				if(!this.options.disabled) {
					//prevent default, if told to
					e = new Event(e);
					if(this.options.stopEvent) { 
						e.stop(); 
					}
					//record this as the trigger
					this.options.element = $(el);
					//position the menu
					this.menu.setStyles({
						top: (e.page.y + this.options.offsets.y),
						left: (e.page.x + this.options.offsets.x),
						position: 'absolute',
						'z-index': '2000'
					});
					//show the menu
					this.show();
				}
			}.bind(this));
		},this);
		
		/* menu items */
		this.menu.getElements('a').each(function(item) {
			item.addEvent('click',function(e) {
				if(!item.hasClass('disabled')) {
					this.execute(item.getProperty('href').split('#')[1],$(this.options.element));
					this.fireEvent('click',[item,e]);
				}
			}.bind(this));
		},this);
		
		//hide on body click
		$(document.body).addEvent('click', function() {
			this.hide();
		}.bind(this));
	},
	
	//show menu
	show: function() {
		this.fx.start(0,1);
		this.fireEvent('show');
		this.shown = true;
		return this;
	},
	
	//hide the menu
	hide: function() {
		if(this.shown)
		{
			this.fx.start(1,0);
			this.fireEvent('hide');
			this.shown = false;
		}
		return this;
	},
	
	//disable an item
	disableItem: function(item) {
		this.menu.getElements('a[href$=' + item + ']').addClass('disabled');
		return this;
	},
	
	//enable an item
	enableItem: function(item) {
		this.menu.getElements('a[href$=' + item + ']').removeClass('disabled');
		return this;
	},
	
	//diable the entire menu
	disable: function() {
		this.options.disabled = true;
		return this;
	},
	
	//enable the entire menu
	enable: function() {
		this.options.disabled = false;
		return this;
	},
	
	//execute an action
	execute: function(action,element) {
		if(this.options.actions[action]) {
			this.options.actions[action](element,this);
		}
		return this;
	}
	
});

ContextMenu.implement(new Options);
ContextMenu.implement(new Events);

/* usage 
//once the DOM is ready
window.addEvent('domready', function() {
	var context = new ContextMenu({
		targets: 'a',
		menu: 'contextmenu',
		actions: {
			copy: function(element,ref) {
				element.setStyle('color','#090');
				alert('You selected the element that says: "' + element.get('text') + '."  I just changed the color green.');
				alert('Disabling the menu to show each individual action can control the menu instance.');
				ref.disable();
			}
		}
	});
	
	$('enable').addEvent('click',function(e) { e.stop(); context.enable(); alert('Menu Enabled.'); });
	$('disable').addEvent('click',function(e) { e.stop(); context.disable(); alert('Menu Disabled.'); });
	
	$('enable-copy').addEvent('click',function(e) { e.stop(); context.enableItem('copy'); alert('Copy Item Enabled.'); });
	$('disable-copy').addEvent('click',function(e) { e.stop(); context.disableItem('copy'); alert('Copy Item Disabled.'); });
	
});
*/
