Ext.define('Admin.view.Players.PlayerDetailWindow', {
	extend: 'Ext.window.Window',
	alias: 'widget.Players.PlayerDetailWindow',

    closeAction : 'hide',
    height: 700,
    width: 500,
    maximizable: true,
    layout: 'fit',
    renderTo : 'desktop',

    userId: 0,

    userConfig : {},

    constructor: function(config) {

    	this.userConfig = config;
        return this.callParent(arguments);
    },

    openDetails : function() {

    	this.removeAll();

    	var store = Ext.create('Ext.data.Store', {
    	    fields:['table', 'param', 'value', 'readablevalue'],
    	    groupField: 'table',
    	    autoLoad: true,
    	    autoDestroy: true,
    		proxy: {
    			type: 'ajax',
    			url: '/playerstores/playerdetails/'+this.userConfig.pkUserID+'.php',
    			reader: {
    				type: 'json',
    				root: 'player',
    	            totalProperty: 'total'
    			},
    			writer: {
    				root: 'player'
    			}
    		}
    	});

    	var grid = Ext.create('Ext.grid.Panel', {
    	    store: store,
    	    columns: [
    	        {header: 'Param', dataIndex: 'param', width: 160},
    	        {header: 'Value', dataIndex: 'value', flex:1,
    	        	editor: { xtype: 'textfield' }
    	        },
    	        {header: 'Readable Value', dataIndex: 'readablevalue', flex:1},
    	    ],
    	    features: [{ftype:'grouping',groupHeaderTpl: 'Table: {name} ({rows.length})'}],
    	    selType: 'cellmodel',
    	    plugins: [
    	        Ext.create('Ext.grid.plugin.CellEditing', {
    	            clicksToEdit: 1
    	        })
    	    ],
    	    fbar:[
				{
					  xtype : 'button',
					  text 	: 'Reset',
					  icon: window.cfg.iconpath16 + 'cancel.png',
					  handler: function() {
						  store.load();
					  }
				},
    	        {
    	        	  xtype : 'button',
    	        	  text 	: 'Save Changes',
    	        	  icon: window.cfg.iconpath16 + 'disk.png',
    	        	  handler: function() {
    	        		  if( confirm('Are you sure you would like to save your changes?') )
    	        		  {
    	        			  store.sync();
    	        		  }
    	        	  }
    	        }
				]
    	});

        this.add(grid);
    },

    openPlayerLogs : function() {

    	this.removeAll();

    	var grid = Ext.create('Ext.grid.Panel', {
    		title: 'logs',
        	region:'center',
        	id:'LogBrowser_grid',
        	columns: [
        	          {header:'filename', dataIndex:'filename', width:300},
        	          {header:'size', dataIndex:'filesize', flex: true},
        	          {header:'last modified', dataIndex:'filedate', flex: true}
        	          ],
        	store: Ext.create('Ext.data.Store', {
        		storeId: 'PlayerLogfiles',
        		autoLoad: true,
        		autoDestroy: true,
    			proxy: {
    				type: 'ajax',
    				url: '/logbrowser/getFilesForUser/'+this.userConfig.pkUserID+'.php'
    	        },
    	        reader: {
    	        	type: 'json'
    	        },
    	        model: Ext.define('File', {
    	        	extend: 'Ext.data.Model',
    	        	fields: ['path', 'filename', 'filesize', 'filedate']
    	         })
    	    }),
    	    listeners: {
                  itemclick: {
                      fn: function(grid, record) {
                     	 var file 	= record.data.filename;
                     	 var userId = this.userConfig.pkUserID;

                     	 var logWindow = Ext.create('widget.LogBrowser.LogOpenLogWindow', file, '"[user '+userId+']"');
                     	 logWindow.show();
                      },
                      scope: this
                  }
            }
    	});
    	this.add(grid);
    },

    openItems : function() {

    	this.removeAll();

    	var store = Ext.create('Ext.data.Store', {
    	    fields:['pkDynamicObjectId', 'fkUserId', 'fkItemId', 'type', 'subtype', 'x', 'y', 'rotation', 'state', 'stateStartTime', 'stateStartTimeReadable', 'ranchValue'],
    	    autoLoad: true,
    	    autoDestroy: true,
    		proxy: {
    			type: 'ajax',
    			url: '/playerstores/playeritems/'+this.userConfig.pkUserID+'.php',
    			reader: {
    				type: 'json',
    				root: 'dynamicobjects',
    	            totalProperty: 'total'
    			},
    			writer: {
    				root: 'dynamicobjects'
    			}
    		}
    	});

    	var grid = Ext.create('Ext.grid.Panel', {
    	    store: store,
    	    columns: [
    	        {xtype: 'rownumberer'},
    	        {header: 'pkDynamicObjectId', dataIndex: 'pkDynamicObjectId', width:20 },
    	        {header: 'fkUserId', dataIndex: 'fkUserId', width:60, editor: { xtype: 'textfield' } },
    	        {header: 'fkItemId', dataIndex: 'fkItemId', width:60, editor: { xtype: 'textfield' } },
    	        {header: 'type', dataIndex: 'type', width:60, editor: { xtype: 'textfield' } },
    	        {header: 'subtype', dataIndex: 'subtype', width:60, editor: { xtype: 'textfield' } },
    	        {header: 'x', dataIndex: 'x', width:40, editor: { xtype: 'textfield' } },
    	        {header: 'y', dataIndex: 'y', width:40, editor: { xtype: 'textfield' } },
    	        {header: 'rotation', dataIndex: 'rotation', width:70, editor: { xtype: 'textfield' } },
    	        {header: 'state', dataIndex: 'state', width:60, editor: { xtype: 'textfield' } },
    	        {header: 'stateStartTime', dataIndex: 'stateStartTime', width:80, editor: { xtype: 'textfield' } },
    	        {header: 'stateStartTime readable', dataIndex: 'stateStartTimeReadable', width:190},
    	        {header: 'ranchValue', dataIndex: 'ranchValue', width:70, editor: { xtype: 'textfield' } }
    	    ],
    	    selType: 'cellmodel',
    	    plugins: [
    	        Ext.create('Ext.grid.plugin.CellEditing', {
    	            clicksToEdit: 1
    	        })
    	    ],
    	    fbar:[
				{
					  xtype : 'button',
					  text 	: 'Reset',
					  icon: window.cfg.iconpath16 + 'cancel.png',
					  handler: function() {
						  store.load();
					  }
				},
    	        {
    	        	  xtype : 'button',
    	        	  text 	: 'Save Changes',
    	        	  icon: window.cfg.iconpath16 + 'disk.png',
    	        	  handler: function() {
    	        		  if( confirm('Are you sure you would like to save your changes?') )
    	        		  {
    	        			  store.sync();
    	        		  }
    	        	  }
    	        }
				]
    	});

        this.add(grid);
        if(this.width < 900) {
        	this.setWidth( 900 );
        }
    },

    openPlayGame: function() {

    	this.removeAll();
    	this.add({
    		xtype:'panel',
    		html:'<iframe style="width: 100%; height: 100%;" scrolling="no" frameborder="0" src="/playgame/user/'+this.userConfig.pkUserID+'.php"></iframe>'
    	})
    	if(this.width < 800) {
        	this.setWidth( 800 );
        }
        this.setHeight( 910 );

    },

	initComponent: function() {
		this.title = "Player Details ~ " + this.userConfig.pkUserID + " " + this.userConfig.firstname + " " + this.userConfig.lastname

		this.tbar = [
		 	       {
			    	   xtype:'buttongroup',
			    	   columns: 2,
			    	   items: [
			    	           {
				    	        	xtype 	: 'button',
				    	        	text 	: 'Player Details',
				    	        	icon: window.cfg.iconpath32 + 'user.png',
				    	        	iconAlign: 'top',
				    	            scale: 'large',
				    	            rowspan: 3,
				    	            action: 'details',
				    	            userId: this.userConfig.pkUserID
				    	        },
				    	        {
				    	        	xtype 	: 'button',
				    	        	text 	: 'Player Items',
				    	        	icon: window.cfg.iconpath16 + 'bricks.png',
				    	            iconAlign: 'left',
				    	            action: 'items',
				    	            userId: this.userConfig.pkUserID
				    	        },
				    	        {
				    	        	xtype 	: 'button',
				    	        	text 	: 'Player Statistics',
				    	        	icon: window.cfg.iconpath16 + 'chart_pie.png',
				    	            iconAlign: 'left',
				    	            action: 'statistics',
				    	            userId: this.userConfig.pkUserID
				    	        },
				    	        {
				    	        	xtype 	: 'button',
				    	        	text 	: 'Player Logfiles',
				    	        	icon: window.cfg.iconpath16 + 'server_information.png',
				    	            iconAlign: 'left',
				    	            action: 'playerlogs',
				    	            userId: this.userConfig.pkUserID
				    	        }
				    	        ]
			       },
			       {
			    	   xtype:'buttongroup',
			    	   columns: 2,
			    	   items: [
			    	           {
				    	        	xtype 	: 'button',
				    	        	text 	: 'Play Game',
				    	        	icon: window.cfg.iconpath32 + 'joystick.png',
				    	        	iconAlign: 'top',
				    	            scale: 'large',
				    	            rowspan: 3,
				    	            action: 'playgame',
				    	            userId: this.userConfig.pkUserID
				    	        }
				    	        ]
			       }
			       ];

		return this.callParent(arguments);
	},

	show: function() {
		this.callParent(arguments);
		this.openDetails();
		return
	}


});