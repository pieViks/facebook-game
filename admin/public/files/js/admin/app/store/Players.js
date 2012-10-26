Ext.define('Admin.store.Players', {
	extend: 'Ext.data.Store',
	requires: ['Ext.data.reader.Json'],
    model: 'Admin.model.Players',

    storeId : 'Players',
    pageSize: window.cfg.itemsPerPage,
    remoteSort: true,
    remoteFilter: true,
	proxy: {
		type: 'ajax',
		url: '/playerstores/players.php',
		reader: {
			type: 'json',
			root: 'players',
            totalProperty: 'total'
		}
	},
	sorters: [{
	    property: 'pkUserID',
	    direction: 'DESC'
	}]
});
