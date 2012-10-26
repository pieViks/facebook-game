Ext.define('Admin.view.MainMenu.MainMenu', {
	extend: 'Ext.toolbar.Toolbar',
	alias: 'widget.MainMenu',

	requires: [
	           'Admin.view.MainMenu.Players',
	           'Admin.view.MainMenu.Logs',
	           'Admin.view.MainMenu.Statistics'
	          ],

	items: [
	        {xtype: "MainMenu.Logs"},
	        {xtype: "MainMenu.Statistics"},
	        {xtype: "MainMenu.Players"}
	       ]


});
