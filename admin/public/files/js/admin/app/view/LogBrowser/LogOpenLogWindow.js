Ext.define('Admin.view.LogBrowser.LogOpenLogWindow', {
	extend: 'Ext.window.Window',
	alias: 'widget.LogBrowser.LogOpenLogWindow',

//    closeAction : 'hide',
    height: 700,
    width: 500,
    maximizable: true,
    layout: 'fit',
    renderTo : 'desktop',

    autoScroll: true,
    scroll:'both',

    fileName: '',
    search: '',

    store: null,
    grid: null,

    items: [],

    constructor: function(fileName, search) {

    	this.fileName = fileName;
    	console.log(search);
    	this.title = fileName;
    	this.search = (search !== undefined) ? search : '';
    	return this.callParent(arguments);

    },

    openLog : function(fileName) {

    	this.title = fileName;
    	this.removeAll();

    	this.store = Ext.create('Ext.data.Store', {
    	    fields:['linenumber','line'],
    	    autoLoad: false,
    	    autoDestroy: true,
    	    pageSize: 100,
    	    remoteSort: false,
    	    remoteFilter: true,
    		proxy: {
    			type: 'ajax',
    			url: '/logbrowser/openLog.php?file='+fileName,
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
    	    columns: [
    	              {header: 'line', dataIndex:'linenumber', sortable:true, width:40},
    	              {header:'content', dataIndex:'line', sortable:false, flex:1}
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
    	    },
    	    dockedItems: [
    	  		        {
    	  		        	xtype: 'toolbar',
    	  		        	dock: 'top',
    	  		        	items: [
    	  						{
    	  							xtype:'button',
    	  							text:'follow log',
    	  							listeners: {
    	  								click: {
    	  									fn: function(event, val) {

    	  										this.store.pageSize = 'tail';
    	  										this.store.load();

    	  										var logAutoRefresh = {
    	  											run: function(){
    	  												this.store.load();
    	  											},
    	  											scope: this,
    	  											interval: 20000
    	  										}
    	  										Ext.TaskManager.start(logAutoRefresh);
    	  									},
    	  									scope: this
    	  								}
    	  							}

    	  						},
    	  						'->','Search For',
    	  						{
    	  							xtype:'textfield',
    	  							name: 'searchfield',
    	  							id:'searchfield',
    	  							enableKeyEvents : true,
    	  							value: this.search,
    	  							listeners: {
    	  			                     change: {
    	  			                         fn: function(event, val) {

    	  			                        	 	this.store.filters.add(
    	  			                        	 		'search',
    	  			                        	 		new Ext.util.Filter({
	    	  			                        	 		property: '*',
	    	  			                        	 		value: val
			    	  			      					})
    	  			                        	 	);
    	  			                        	 	this.store.load();
    	  			                         },
    	  			                         scope: this,
    	  			                         buffer: 100
    	  			                     }
    	  			                 }
    	  						}
    	  					]
    	  		        },
    	  			    {
    	  			        xtype: 'pagingtoolbar',
    	  			        store: this.store,
    	  			        dock: 'bottom',
    	  			        displayInfo: true
    	  			    }
    	  		    ]

    	});




        if(this.search != undefined && this.search != '') {
        	this.store.filters.add(
          	 		'search',
          	 		new Ext.util.Filter({
              	 		property: '*',
              	 		value: this.search
    					})
          	 	);
        }

        this.store.load(0);

        this.add(this.grid);

        this.grid.showHorizontalScroller();
    },


	show: function() {
		this.callParent(arguments);
		this.openLog(this.fileName);
		return
	}


});