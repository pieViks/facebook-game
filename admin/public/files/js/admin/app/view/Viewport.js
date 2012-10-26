Ext.define('Admin.view.Viewport', {
    extend: 	'Ext.container.Viewport',
    renderTo: 	Ext.getBody(),
    requires: 	[ 'Admin.view.MainMenu.MainMenu' ],
    layout: 	'border',

    items: [
	{
        region: 'north',
        xtype: 'MainMenu'
    },
    {
        region: 'center',
        xtype: 'container',
        id: 'desktop',
        layout: 'fit',
        cls: "viewport-background"
    }
    ]

});
