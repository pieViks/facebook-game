Ext.define('Admin.view.Players.BrowsePlayersWindow', {
	extend: 'Ext.window.Window',
	alias: 'widget.Players.BrowsePlayersWindow',

    closeAction : 'hide',
    height: 600,
    width: 800,
    maximizable: true,
    layout: 'fit',
    renderTo : 'desktop',

    openPlayers : function() {

    	this.removeAll();

    	var store = Ext.data.StoreManager.lookup('Players');

    	var grid = Ext.create('Ext.grid.Panel', {
    		columnLines: window.cfg.columnLines,
		    columns: [
		              {xtype: 'rownumberer'},
		              {
		            	  header: 'Avatar',
		            	  dataIndex:'facebookID',
		            	  renderer: function(value) {
		            		  	return Ext.String.format('<div style="width:16px; height:32px; background: #333;"><img width="32" height="32" src="https://graph.facebook.com/{0}/picture"></a></div>', value);
		            	  },
		            	  width: 40,
		            	  sortable: false
		              },
		              {header: 'Online',		dataIndex:'lastPlayed',		width:90,
		            	  renderer:	function(value) {
		            		if(value < 90) {
		            			return '<img src="'+window.cfg.iconpath16+'status_online.png" />';
		            		} else {
		            			var days 		= Math.floor(value / (3600 * 24) );
		            			value			= value - ( days * (3600 * 24) );
		            			var hours 		= Math.floor( value / 3600); hours = (hours <= 9) ? "0"+hours : hours;
		            			value			= value - ( hours * 3600 );
		            			var minutes 	= Math.floor( value / 60 ); minutes = (minutes <= 9) ? "0"+minutes : minutes;
		            			value			= value - ( minutes * 60 );
		            			var seconds 	= value; seconds = (seconds <= 9) ? "0"+seconds : seconds;
		            			var string		= ''+days+':'+hours+':'+minutes+':'+seconds+' ago';

		            			return '<img src="'+window.cfg.iconpath16+'status_offline.png" /><br /> '+string;
		            		}

		            	  }
		              },
		              {header: 'pkUserID',		dataIndex:'pkUserID', 		width:70},
		              {header: 'facebookID',	dataIndex:'facebookID', 	width:70, 	hidden:true},
		              {header: 'Firstname',		dataIndex:'firstname', 		width:80},
		              {header: 'Lastname',		dataIndex:'lastname', 		width:100},
		              {header: 'E-Mail',		dataIndex:'email', 			width:140},
		              {header: 'Chips',			dataIndex:'chips', 			width:60, xtype: 'numbercolumn', format:'0,000'},
		              {header: 'Gold',			dataIndex:'gold', 			width:60, xtype: 'numbercolumn', format:'0,000'},
		              {header: 'Ranchvalue',	dataIndex:'ranchvalue', 	width:90, xtype: 'numbercolumn', format:'0,000'},
		             ],
		    store: store,
		    dockedItems: [
		        {
		        	xtype: 'toolbar',
		        	dock: 'top',
		        	items: [
						'->','Search For',
						{
							xtype:'textfield',
							name: 'searchfield',
							id:'searchfield',
							enableKeyEvents : true
						}
					]
		        },
			    {
			        xtype: 'pagingtoolbar',
			        store: 'Players',
			        dock: 'bottom',
			        displayInfo: true

			    }
		    ]
    	});

        this.add(grid);

        store.load(1);
    },

	initComponent: function() {
		this.title = "Browse Players";
		return this.callParent(arguments);
	},

	show: function() {
		this.callParent(arguments);
		this.openPlayers();
		return
	}

});
