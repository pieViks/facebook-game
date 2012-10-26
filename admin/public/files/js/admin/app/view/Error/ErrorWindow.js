Ext.define('Admin.view.Error.ErrorWindow', {
	extend: 'Ext.window.Window',
	alias: 'widget.Error.ErrorWindow',

	title: 'Unique Errors',

    closeAction : 'hide',
    height: 600,
    width: 700,
    maximizable: true,
    layout: 'fit',
    renderTo : 'desktop',

    store: null,
    grid: null,

    items: [],

    show: function() {
    	this.callParent(arguments);

    	this.store = Ext.create('Ext.data.Store', {
    	    fields:['pkUniqueErrorsId','errno','errnoString','message','stage','environment','filename','lineno','amount','lastOccurrence','firstOccurrence','follow','email'],
    	    autoLoad: false,
    	    autoDestroy: true,
    	    pageSize: 50,
    	    remoteSort: true,
    	    remoteFilter: true,

    	    proxy: {
    			type: 'ajax',
    			url: '/logbrowser/errors.php',
    			reader: {
    				type: 'json',
    				root: 'errors',
    	            totalProperty: 'total'
    			}
    		}
    	});

		this.grid =	Ext.create('Ext.grid.Panel', {
	    	region:	'center',
	    	id:		'errors_grid',
	    	store: this.store,
	    	columns: [
	    	          {header:'Severity', dataIndex:'errnoString', flex: true},
	    	          {header:'Stage', dataIndex:'stage', flex: true},
	    	          {header:'Message', dataIndex:'message', width:330},
	    	          {header:'Filename', dataIndex:'filename', flex: true},
	    	          {header:'Line', dataIndex:'lineno',flex: true},
	    	          {header:'Times Occurred', dataIndex:'amount', flex: true},
	    	          {header:'First Occurrence', dataIndex:'firstOccurrence', flex: true},
	    	          {header:'Last Occurrence', dataIndex:'lastOccurrence', flex: true},
	    	          {header:'Follow', dataIndex:'follow', flex: true, hidden:true},
	    	          {header:'Email', dataIndex:'email', flex: true, hidden:true}
	    	],

	    	dockedItems: [ {xtype: 'pagingtoolbar', store: this.store, dock: 'bottom', displayInfo: true } ],

	    	listeners: {
				itemclick: function(view, record, item, index, event)
				{
					var details = "";
					var i;
					for(i in record.data) {
						details += "<b>["+i+"]</b>" + ":<br />\n"+record.data[i]+"<br />\n";
					}
					details += " \n";

					var form = Ext.create('Ext.form.Panel', {
					    url: '/logbrowser/followerror/'+record.data.pkUniqueErrorsId+'.php',
					    frame: true,
					    renderTo: Ext.getBody(),
					    layout: 'fit',
					    width: 500,
					    defaults: {
					        bodyPadding: 4
					    },
					    items: [
						{
						    xtype: 'htmleditor',
						    fieldLabel: 'Details',
						    name: 'details',
						    value: details,
						    height: 400,
						    enableAlignments: false,
						    enableColors: false,
						    enableFont: false,
						    enableFontSize: false,
						    enableFormat: false,
						    enableLinks: false,
						    enableLists: false,
						    enableSourceEdit: false
						},
					    {
					    	xtype:'checkboxfield',
					        fieldLabel: 'Follow',
					        name: 'follow',
					        value: record.data.follow,
					        checked: (record.data.follow == '1') ? true : false
					    },
					    {
					    	xtype:'checkboxfield',
					        fieldLabel: 'Delete',
					        name: 'delete',
					        checked: false
					    },
					    {
					    	xtype: 'textfield',
					        fieldLabel: 'Email',
					        name: 'email',
					        value: record.data.email,
					        width: 200
					    }
					    ],

					    buttons: [
						    {
						        text: 'Submit',
						        formBind: true,

						        handler: function() {
						            var form = this.up('form').getForm();
						            if (form.isValid()) {
						                form.submit();
						            }
						        }
						    }
					    ],
					});

					var window = Ext.create('Ext.window.Window', {
					    maximizable: true,
					    resizable: false,
					    layout: 'fit',
					    renderTo : 'desktop',
						items: [form],
						title: 'Unique Error Details id:'+record.data.pkUniqueErrorsId
					});

					window.show();
				}
    	    },
		});

		this.add(this.grid);
		this.store.load(0);


		return
	}



});