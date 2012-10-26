Ext.define('Admin.view.LogBrowser.AdvancedSearchResultWindow', {
	extend: 'Ext.window.Window',
	alias: 'widget.LogBrowser.AdvancedSearchResultWindow',

	title: 'Advanced Log Search Result',

    maximizable : true,
    resizable : true,
    renderTo : 'desktop',
    
    layout: 'fit',
    items: [],
    
    width: 600,
    height: 400,

    store: null,
    grid: null,

    constructor: function() {
    	return this.callParent(arguments);
    },

    show: function(formData) {
    	this.callParent(arguments);
		this.toggleMaximize( );
		return
	},
    
    search: function(formData) {

    	this.removeAll();
    	
    	console.log(Ext.encode(formData));
    	var search = Ext.encode(formData)

    	this.store = Ext.create('Ext.data.Store', {
    	    fields: ['timestamp','logfile','line'],
    	    autoLoad: false,
    	    autoDestroy: true,
    	    remoteSort: false,
    	    remoteFilter: false,
    		proxy: {
    			type: 'ajax',
    			url: '/logbrowser/searchLog.php?search='+search,
    			reader: {
    				type: 'json',
    				root: 'lines',
    	            totalProperty: 'total'
    			}
    		}
    	});

    	this.grid = Ext.create('Ext.grid.Panel', {
    	    store: this.store,
    	    autoScroll: true,
    	    autoRender: true,
    	    scroll: true,
    	    flex: 1,
    	    columns: [
    	              {header:'Logfile', dataIndex:'logfile', sortable:true, width: 160},
    	              {header:'content', dataIndex:'line', sortable:false, flex: 1}
    	    ],
    	    dockedItems: [
      	  		        
      	  		    ],
    	    listeners: {
				itemclick: function(view, record, item, index, event)
				{
					var details = "";
					var i;
					for(i in record.data) {
						details += i + ":<br />"+record.data[i]+" <br/><br/>\n";
					}
					Ext.Msg.alert('Entry Details', details);
				}
    	    }
    	  

    	});

        this.add(this.grid);
        
        this.store.load(0);
    }


});
