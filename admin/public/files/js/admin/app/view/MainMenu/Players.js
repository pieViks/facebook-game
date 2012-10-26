Ext.define('Admin.view.MainMenu.Players', {
	extend: 'Ext.container.ButtonGroup',
	alias: 'widget.MainMenu.Players',
	title: 'Players',

	items: [{
        text: 'browse players',
        scale: 'large',
        rowspan: 3,
        icon: window.cfg.iconpath32 + 'group.png',
        iconAlign: 'top',
        action: 'browse'
    }]

});