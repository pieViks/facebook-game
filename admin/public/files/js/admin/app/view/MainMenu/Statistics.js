Ext.define('Admin.view.MainMenu.Statistics', {
	extend: 'Ext.container.ButtonGroup',
	alias: 'widget.MainMenu.Statistics',
	title: 'Statistics',
	columns: 2,

	items: [

	    	{
		        text: 'Online Users - live',
		        scale: 'small',
		        icon: window.cfg.iconpath16 + 'chart_line.png',
		        action: 'onlineuserslive'
	    	}

    ]

});
