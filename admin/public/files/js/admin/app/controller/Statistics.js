Ext.define('Admin.controller.Statistics', {
    extend: 'Ext.app.Controller',

    views: [
            'Statistics.OnlineUsersLiveWindow'
            ],
    stores: [],

    models: [],

    openWindows:[],

    init: function() {
        this.control({
            '[xtype="MainMenu.Statistics"] button[action="onlineuserslive"]': {
                click: this.openOnlineUsersLive
            }

        });

        this.callParent(arguments);
    },

    openOnlineUsersLive: function() {
		if(this.openWindows['win_onlineuserslive'] != undefined) {
			this.openWindows['win_onlineuserslive'].show();
			return;
		}

		var onlineUsersLive	= this.getStatisticsOnlineUsersLiveWindowView();

		this.openWindows['win_onlineuserslive'] = new onlineUsersLive();
		this.openWindows['win_onlineuserslive'].show();
    }

});
