Ext.define('Admin.view.FileBrowser.FileBrowserWindow', {
	extend: 'Ext.window.Window',
	alias: 'widget.FileBrowser.FileBrowserWindow',

	title: 'File Browser',

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
    		            url: '/filebrowser/getDirs.php'
    		        }
            	}),
    	        rootVisible: false,
    	        region: 'west',
    	        useArrows: true,
    	        listeners: {
    	        	itemclick: function(view, record, item, index, event)
    	        	{
    	        		var store = Ext.data.StoreManager.lookup('FileBrowserFiles');
    	        		store.load({
    	        			url: '/filebrowser/getFiles.php?path=' + record.data.id

    	        		});

    	        		Ext.getCmp('filebrowser_grid').setTitle('/'+record.data.id);
    	        	}
    	        }

            },
            {
            	title: 'files',
            	region:'center',
            	xtype:'grid',
            	id:'filebrowser_grid',
            	columns: [
            	          {header:'path', dataIndex:'path', hidden:true},
            	          {header:'filename', dataIndex:'filename', flex: true},
            	          {header:'size', dataIndex:'filesize', flex: true},
            	          {header:'last modified', dataIndex:'filedate', flex: true}
            	          ],
            	store: Ext.create('Ext.data.Store', {
            		storeId: 'FileBrowserFiles',
        			proxy: {
        				type: 'ajax',
        				url: '/filebrowser/getFiles.php'
        	        },
        	        reader: {
        	        	type: 'json'
        	        },
        	        model: Ext.define('File', {
        	        	extend: 'Ext.data.Model',
        	        	fields: ['path', 'filename', 'filesize', 'filedate']
        	         })
        	    }),
        	    tbar: [{text:'upload'}]
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
