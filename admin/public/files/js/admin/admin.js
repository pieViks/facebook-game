Ext.Loader.setConfig({enabled:true});

Ext.application({

    name: 					'Admin',
    appFolder: 				'/files/js/admin/app',

    autoCreateViewport: 	true,

    controllers: 			[
                 			 'Players',
                 			 'FileBrowser',
                 			 'LogBrowser',
                 			 'Statistics'
                 			 ],

	launch: function() {


	}
});
