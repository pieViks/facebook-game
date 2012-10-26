Ext.define('Admin.controller.LogBrowser', {
    extend: 'Ext.app.Controller',

    views: [
            'MainMenu.Logs',
            'LogBrowser.LogBrowserWindow',
            'LogBrowser.LogOpenLogWindow',
            'LogBrowser.AdvancedSearchWindow',
            'LogBrowser.AdvancedSearchResultWindow',
            'Error.ErrorWindow'
            ],
    stores: [],

    models: [],

    openWindows:[],

    init: function() {
        this.control({
            '[xtype="MainMenu.Logs"] button[action="logbrowser"]': {
                click: this.openLogBrowser
            },
            '[xtype="MainMenu.Logs"] button[action="errors"]': {
                click: this.openErrorWindow
            },
            '[xtype="MainMenu.Logs"] button[action="asearch"]': {
                click: this.openAdvancedSearchWindow
            },
            '[xtype="LogBrowser.LogBrowserWindow"] grid': {
            	itemclick: this.openLog
            },
            '[xtype="LogBrowser.AdvancedSearchWindow"] button[action="submit"]': {
            	click: this.openAdvancedSearchResultWindow
            }
        });

        this.callParent(arguments);
    },

    openLogBrowser: function() {
		if(this.openWindows['win_logbrowser'] != undefined) {
			this.openWindows['win_logbrowser'].show();
			return;
		}

		var logBrowserWindow	= this.getLogBrowserLogBrowserWindowView();

		this.openWindows['win_logbrowser'] = new logBrowserWindow();
		this.openWindows['win_logbrowser'].show();
    },

    openLog: function(grid, record) {

    	var fileName 	= record.data.filename;
    	/*var windowName 	= 'win_logbrowser_'+ fileName.split('-').join('').split('.').join('');

    	if(this.openWindows[windowName] != undefined) {
			this.openWindows[windowName].show();
			return;
		}*/

		var logWindow	= this.getLogBrowserLogOpenLogWindowView();
		var logWindowClass = new logWindow(fileName);
		logWindowClass.show();
    },

    openErrorWindow: function() {

    	if(this.openWindows['win_error'] != undefined) {
			this.openWindows['win_error'].show();
			return;
		}

		var logErrorWindow	= this.getErrorErrorWindowView();

		this.openWindows['win_error'] = new logErrorWindow();
		this.openWindows['win_error'].show();
    },

    openAdvancedSearchWindow: function() {

    	if(this.openWindows['win_asearch'] != undefined) {
			this.openWindows['win_asearch'].show();
			return;
		}

		var advancedSearchWindow	= this.getLogBrowserAdvancedSearchWindowView();

		this.openWindows['win_asearch'] = new advancedSearchWindow();
		this.openWindows['win_asearch'].show();
    },
    
    openAdvancedSearchResultWindow: function(a) {
    	
    	var formData = a.up('form').getForm().getFieldValues();
    	
    	var resultWindow		= this.getLogBrowserAdvancedSearchResultWindowView();
		var resultWindowClass 	= new resultWindow();
		resultWindowClass.search( formData );
		resultWindowClass.show();
    	
    }

});
