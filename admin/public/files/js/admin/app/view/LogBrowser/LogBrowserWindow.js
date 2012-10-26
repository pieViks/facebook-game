Ext.define('Admin.view.LogBrowser.LogBrowserWindow', {
	extend: 'Ext.window.Window',
	alias: 'widget.LogBrowser.LogBrowserWindow',

	title: 'Log Browser',

    closeAction : 'hide',
    height: 600,
    width: 800,
    maximizable: true,
    renderTo : 'desktop',
    layout : 'border',
    defaults: {
        frame:false,
        split:true
    },
    items: [
            {
            	title: 'folders',
            	xtype: 'treepanel',
            	region: 'west',
            	width: 240,
            	store: Ext.create('Ext.data.TreeStore', {
    		        proxy: {
    		            type: 'ajax',
    		            url: '/logbrowser/getDirs.php'
    		        }
            	}),
    	        rootVisible: false,
    	        region: 'west',
    	        useArrows: true,
    	        listeners: {
    	        	itemclick: function(view, record, item, index, event)
    	        	{
    	        		var store = Ext.data.StoreManager.lookup('LogBrowserFiles');
    	        		store.load({
    	        			url: '/logbrowser/getFiles.php?path=' + record.data.id
    	        		});
    	        		store.sort('filedate', 'DESC');

    	        		Ext.getCmp('LogBrowser_grid').setTitle('/'+record.data.id);
    	        	}
    	        }

            },
            {
            	title: 'logfiles',
            	region:'center',
            	xtype:'grid',
            	id:'LogBrowser_grid',
            	columns: [
            	          {header:'filename', dataIndex:'filename', width:300},
            	          {header:'size', dataIndex:'filesize', flex: true},
            	          {header:'last modified', dataIndex:'filedate', flex: true}
            	          ],
            	store: Ext.create('Ext.data.Store', {
            		storeId: 'LogBrowserFiles',
        			proxy: {
        				type: 'ajax',
        				url: '/LogBrowser/getFiles.php'
        	        },
        	        reader: {
        	        	type: 'json'
        	        },
        	        model: Ext.define('File', {
        	        	extend: 'Ext.data.Model',
        	        	fields: ['path', 'filename', 'filesize', 'filedate']
        	         })
        	    })
            }

           ],

	initComponent: function() {
		this.callParent(arguments);
	},

	show: function() {

		this.callParent(arguments);
		return
	}

});
