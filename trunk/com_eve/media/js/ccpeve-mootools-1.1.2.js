var CcpEveContextMenu = new Class({
	Implements: [Options],
	
	options: {
		targets: '.ccpeve',
		doctype: 'html4/xhtml',
		menuclass: 'contextmenu-ccpeve',
		titles: {
			showInfo: 'Show Info',
			showPreview:'Show Preview',
			showMarketDetails: 'Show Market Details',
			showRouteTo: 'Show Route',
			showMap: 'Show on Map',
			setDestination: 'Set Destination',
			addWaypoint: 'Add Waypoint',
			showContract: 'Show Contract',
			close: 'Close',
		},
	},
	
	args: {
		showInfo: ['typeId', 'itemId'],
		showPreview: ['typeId'],
		showMarketDetails: ['typeId'],
		showRouteTo: ['itemId'],
		showMap: ['itemId'],
		setDestination: ['itemId'],
		addWaypoint: ['itemId'],
		showContract: ['itemId', 'contractId'],
	},
	
	entities: {
		character: {
			click: 'showInfo',
			typeId: 1377,
			contextmenu: ['showInfo'],
		},
		corporation: {
			click: 'showInfo',
			typeId: 2,
			contextmenu: ['showInfo'],
		},
		alliance: {
			click: 'showInfo',
			typeId: 16159,
			contextmenu: ['showInfo'],
		},
		solarSystem: {
			click: 'showInfo',
			typeId: 5,
			contextmenu: ['showInfo', 'showRouteTo', 'showMap', 'setDestination', 'addWaypoint'],
		},
		constellation: {
			click: 'showInfo',
			typeId: 4,
			contextmenu: ['showInfo'],
		},
		region: {
			click: 'showInfo',
			typeId: 3,
			contextmenu: ['showInfo'],
		},
		station: {
			click: 'showInfo',
			typeId: 3867,
			contextmenu: ['showInfo', 'showPreview'],
		},
		item: {
			click: 'showInfo',
			contextmenu: ['showInfo'],
		},
		destination: {
			click: 'setDestination',
			typeId: 5,
			contextmenu: ['showInfo', 'showRouteTo', 'showMap', 'setDestination', 'addWaypoint'],
		},
		contract: {
			click: 'showContract',
			typeId: 5,
			contextmenu: ['showContract', 'showRouteTo', 'showMap', 'setDestination', 'addWaypoint'],
		},
	},
	
	
	_getEvent: function(option, element) {
		var _this = this;
		return function(e) {
			var _args = _this.args[option.click].map(function(arg) {return element.dataset[arg]}).filter(function(item) {return $chk(item);});
			//alert('CCPEVE.'+option.click+'('+_args+');');
			eval('CCPEVE.'+option.click+'('+_args+');');
		}
	},
	
	_getContextMenuAction: function(option) {
		var _this = this;
		return function(element, ref) {
			var _args = _this.args[option].map(function(arg) {return element.dataset[arg]}).filter(function(item) {return $chk(item);});
			//alert('CCPEVE.'+option+'('+_args+');');
			eval('CCPEVE.'+option+'('+_args+');');
		}
	},
		


	initialize: function(options) {
		this.setOptions(options);
		this.targets = $$(this.options.targets);
		this.buildMenus();
	},
	
	buildMenus: function() {
		this.targets.each(function(element) {
			this.prepareDataset(element);
			var name = element.dataset.ccpeve;
			if (!$chk(this.entities[name])) {
				return;
			}
			if ($chk(this.entities[name].click)) {
				if (element.getTag() != 'a' || element.getProperty('href') == '#') {
					element.addEvent('click', this._getEvent(this.entities[name], element));
				}
			}
			if ($chk(this.entities[name].contextmenu)) {
				var _actions = {};
				var _menu = new Element('ul', {
					'class': this.options.menuclass,
					}).inject(element, 'after');
				_menu.setHTML('<li class="separator"><li><a href="#close">'+this.options.titles['close']+'</a></li>');
				if ($chk(element.dataset.showMarketDetails)) {
					this.addMenuItem(_menu, _actions, "showMarketDetails");
				}
				if ($chk(element.dataset.showPreview)) {
					this.addMenuItem(_menu, _actions, "showPreview");
				}
				$A(this.entities[name].contextmenu).reverse().each(function(value) {
					this.addMenuItem(_menu, _actions, value);
				}, this);
				new ContextMenu({
					targets: element,
					menu: _menu,
					actions: _actions,
				});
			}
		}, this);
	},
	
	addMenuItem: function(menu, actions, value) {
		actions[value] = this._getContextMenuAction(value);
		var _li = new Element('li', {}).inject(menu, 'top');
		_li.setHTML('<a href="#'+value+'">'+this.options.titles[value]+'</a>');
	},
	
	prepareDataset: function(element) {
		if (!$chk(element.dataset)) {
			element.dataset = {};
		}
		if (this.options.doctype == 'html5') {
			$each(element.attributes, function(attribute, key){
				if (attribute.name.match(/^data-/)) {
					var tmp = attribute.name.split('-');
					tmp.shift();
					for (var i=1; i < tmp.length; i++) {
						tmp[i] = tmp[i].substr(0, 1).toUpperCase() + tmp[i].substr(1)
					}
					element.dataset[tmp.join('')] = attribute.value;
				}
			});
			var name = element.dataset.ccpeve;
			if ($chk(this.entities[name]) && $chk(this.entities[name].typeId)) {
				element.dataset.typeId = this.entities[name].typeId;
			}
		} else {
			if (element.hasClass('showPreview')) {
				element.dataset.showPreview = 'true';
			}
			if (element.hasClass('showMarketDetails')) {
				element.dataset.showMarketDetails = 'true';
			}
			var classes = element.className.split(/ +/);
			classes.each(function(class) {
				var args = class.split('-');
				var name = args.shift();
				if ($chk(this.entities[name])) {
					element.dataset.ccpeve = name;
					var _args = $A(this.args[this.entities[name].click]);
					if ($chk(this.entities[name].typeId)) {
						element.dataset.typeId = this.entities[name].typeId;
						_args.remove('typeId');
					}
					_args.each(function(item, index) {
						element.dataset[item] = args[index];
					});
					return;
				}
			}, this);
		}
	},
});

CcpEveContextMenu.implement(new Options);

window.addEvent('domready', function() {
	if ($chk(CCPEVE)) {
		new CcpEveContextMenu({});
	}
});
