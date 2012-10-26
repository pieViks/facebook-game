Ext.define('Admin.view.LogBrowser.AdvancedSearchWindow', {
	extend: 'Ext.window.Window',
	alias: 'widget.LogBrowser.AdvancedSearchWindow',

	title: 'Advanced Log Search',

    closeAction : 'hide',
    maximizable: false,
    resizable: false,
    renderTo : 'desktop',

    layout: 'fit',
    items: [],

    initComponent: function() {
		this.callParent(arguments);
		this.showForm();
	},

	show: function() {
		this.callParent(arguments);
		return
	},

    showForm : function() {

    	this.removeAll();

    	var stages = Ext.create('Ext.data.Store', {
    	    fields: ['abbr', 'name'],
    	    data : [
    	        {"abbr":"dev", 		"name":"Development"},
    	        {"abbr":"accept", 	"name":"Accept"},
    	        {"abbr":"live", 	"name":"Live"}
    	    ]
    	});

    	var logtypes = Ext.create('Ext.data.Store', {
    	    fields: ['abbr', 'name'],
    	    data : [
    	        {"abbr":"access", 		"name":"Access log"},
    	        {"abbr":"error", 		"name":"Error log"},
    	        {"abbr":"transaction", 	"name":"User Transactions log"},
    	        {"abbr":"slow", 		"name":"Slow log"}
    	    ]
    	});

        var formPanel = Ext.create('Ext.form.Panel', {
        	bodyPadding: 10,
            frame: true,
            border: 0,

            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 180,
                anchor: '100%'
            },

            buttons: [

		                 {
		                     text   : 'Search',
		                     formBind: true,
		                     action: 'submit'
		                 }
		    ],
            items: [
                    {
		                xtype: 'textfield',
		                name: 'search',
		                fieldLabel: 'Search for string',
		                allowBlank: false
		            },
		            {
		            	xtype:'combobox',
		            	fieldLabel: 'On Stage',
		            	name: 'stage',
		                store: stages,
		                forceSelection: true,
		                queryMode: 'local',
		                displayField: 'name',
		                valueField: 'abbr',
		                allowBlank: false
		            },
		            {
		            	xtype:'combobox',
		            	fieldLabel: 'Search in logtypes',
		            	name: 'logtypes',
		                store: logtypes,
		                forceSelection: true,
		                queryMode: 'local',
		                displayField: 'name',
		                valueField: 'abbr',
		                allowBlank: false
		            },
		            {
		                xtype: 'fieldcontainer',
		                fieldLabel: 'Start Date & Time',
		                combineErrors: true,
		                msgTarget : 'under',
		                defaults: {
		                    flex: 1
		                },
		                items: [
		                    {
		                        xtype     : 'datefield',
		                        name      : 'startDate',
		                        fieldLabel: 'Date',
		                        allowBlank: false,
		                        format: 'd/m/Y'
		                    },
		                    {
		                        xtype: 'timefield',
		                        name: 'startTime',
		                        fieldLabel: 'Time',
		                        format: 'H:i',
		                        minValue: '0:00',
		                        maxValue: '23:45',
		                        allowBlank: false,
		                    }
		                ]
		            },
		            {
		                xtype: 'fieldcontainer',
		                fieldLabel: 'End Date & Time',
		                combineErrors: true,
		                msgTarget : 'under',
		                defaults: {
		                    flex: 1
		                },
		                items: [
		                    {
		                        xtype     : 'datefield',
		                        name      : 'endDate',
		                        fieldLabel: 'Date',
		                        allowBlank: false,
		                        format: 'd/m/Y'
		                    },
		                    {
		                        xtype: 'timefield',
		                        name: 'endTime',
		                        fieldLabel: 'Time',
		                        format: 'H:i',
		                        minValue: '0:00',
		                        maxValue: '23:59',
		                        allowBlank: false,
		                    }
		                ]
		            }
		       ]
        });
        this.add(formPanel);

    }


});
