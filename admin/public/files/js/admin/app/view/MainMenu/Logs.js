Ext.define('Admin.view.MainMenu.Logs', {
	extend: 'Ext.container.ButtonGroup',
	alias: 'widget.MainMenu.Logs',
	title: 'Logs',
	columns: 2,

	items: [
	        {
		        text: 'Log Browser',
		        scale: 'large',
		        rowspan: 3,
		        icon: window.cfg.iconpath32 + 'server_information.png',
		        iconAlign: 'top',
		        action: 'logbrowser'
	    	},
	    	{
		        text: 'Advanced Search',
		        scale: 'small',
		        icon: window.cfg.iconpath16 + 'magnifier.png',
		        action: 'asearch'
	    	},
	    	{
		        text: 'Unique Errors',
		        scale: 'small',
		        icon: window.cfg.iconpath16 + 'alarm_bell.png',
		        action: 'errors'
	    	}

    ]

});
