Ext.define('Admin.view.MainMenu.Files', {
	extend: 'Ext.container.ButtonGroup',
	alias: 'widget.MainMenu.Files',
	title: 'Files',

	items: [{
        text: 'filebrowser',
        scale: 'large',
        rowspan: 3,
        icon: window.cfg.iconpath32 + 'folders_explorer.png',
        iconAlign: 'top',
        action: 'filebrowser'
    }]

});
