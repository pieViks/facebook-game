Ext.define('Admin.controller.Players', {
    extend: 'Ext.app.Controller',

    requires: ['Ext.Date.*'],

    views: [
            'MainMenu.Players',
            'Players.BrowsePlayersWindow',
            'Players.PlayerDetailWindow'
            ],
    stores: ['Players'],

    models: ['Players'],

    openWindows:[],

    init: function() {

        this.control({
            '[xtype="MainMenu.Players"] button[action="browse"]': {
                click: this.browsePlayers
            },
            '[xtype="Players.BrowsePlayersWindow"] grid': {
            	 itemclick: this.openPlayerDetail
            },
            '[xtype="Players.BrowsePlayersWindow"] grid toolbar textfield': {
            	keydown: this.browsePlayers_search
            },

            '[xtype="Players.PlayerDetailWindow"] button[action="details"]': {
            	click: this.openPlayerDetail_details
            },
            '[xtype="Players.PlayerDetailWindow"] button[action="items"]': {
            	click: this.openPlayerDetail_items
            },
            '[xtype="Players.PlayerDetailWindow"] button[action="playgame"]': {
            	click: this.openPlayerDetail_playgame
            },
            '[xtype="Players.PlayerDetailWindow"] button[action="playerlogs"]': {
            	click: this.openPlayerDetail_playerlogs
            }

        });

        this.callParent(arguments);


    },

	browsePlayers: function() {

		if(this.openWindows['win_browse_players'] != undefined) {
			this.openWindows['win_browse_players'].show();
			return;
		}

		var browsePlayersWindow	= this.getPlayersBrowsePlayersWindowView();

		this.openWindows['win_browse_players'] = new browsePlayersWindow();
		this.openWindows['win_browse_players'].show();
	},

	browsePlayers_search : function(field,event) {

		if(event.button == '12' || event.button == '13')
		{
			var val = field.getValue();

			var playersStore 	= this.getPlayersStore();

			playersStore.filters.add('search', new Ext.util.Filter({
				  property: '*',
				  value: val
				})
			);

			playersStore.loadPage(1);
		}
	},

	openPlayerDetail: function(grid, record) {

		var userId = record.get('pkUserID');
		var userConfig = record.data;

		if(this.openWindows['win_browse_player_'+userId] != undefined) {
			this.openWindows['win_browse_player_'+userId].show();
			return;
		}

		var playerDetailWindow = this.getPlayersPlayerDetailWindowView();
		this.openWindows['win_browse_player_'+userId] = new playerDetailWindow(userConfig);
		this.openWindows['win_browse_player_'+userId].show();
	},

	openPlayerDetail_details: function(el,event) {
		var userId = el.userId;
		this.openWindows['win_browse_player_'+userId].openDetails();
	},

	openPlayerDetail_items: function(el,event) {
		var userId = el.userId;
		this.openWindows['win_browse_player_'+userId].openItems();
	},

	openPlayerDetail_playgame: function(el,event) {
		var userId = el.userId;
		this.openWindows['win_browse_player_'+userId].openPlayGame();
	},

	openPlayerDetail_playerlogs: function(el,event) {
		var userId = el.userId;
		this.openWindows['win_browse_player_'+userId].openPlayerLogs();
	}
});
