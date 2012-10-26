Ext.define('Admin.controller.FileBrowser', {
    extend: 'Ext.app.Controller',

    views: [
            'MainMenu.Files',
            'FileBrowser.FileBrowserWindow'
            ],
    stores: [],

    models: [],

    openWindows:[],

    init: function() {
        this.control({
            '[xtype="MainMenu.Files"] button[action="filebrowser"]': {
                click: this.openFileBrowser
            },
            '[xtype="FileBrowser.FileBrowserWindow"] grid': {
            	 itemclick: this.openFileDetail
            },
        });

        this.callParent(arguments);


    },

    openFileBrowser: function() {
		if(this.openWindows['win_filebrowser'] != undefined) {
			this.openWindows['win_filebrowser'].show();
			return;
		}

		var fileBrowserWindow	= this.getFileBrowserFileBrowserWindowView();

		this.openWindows['win_filebrowser'] = new fileBrowserWindow();
		this.openWindows['win_filebrowser'].show();
    },

    openFileDetail: function(grid, record){
    	console.log(record);
    	Ext.create('Ext.window.Window', {
    		title:record.data.filename,
    		width: 500,
    		height: 500,
    		html:'<iframe frameborder="no" style="width:100%;height:100%;" src="/'+record.data.path+record.data.filename+'"></iframe>'
    	}).show();

    }
});
