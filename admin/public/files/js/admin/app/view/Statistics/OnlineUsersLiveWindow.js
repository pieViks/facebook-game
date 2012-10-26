Ext.define('Admin.view.Statistics.OnlineUsersLiveWindow', {
	extend: 'Ext.window.Window',
	alias: 'widget.Statistics.OnlineUsersLiveWindow',

    closeAction : 'hide',
    height: 440,
    width: 840,
    maximizable: true,
    layout: 'fit',
    renderTo : 'desktop',

    items: [
            {
            	xtype : 'component',
            	id    : 'iframe-win',
            	autoEl : {
            		tag : 'iframe',
            		src : '/statistics/onlineuserslive.php',
            		framwborder: '0',
            		style:'background: #fff; border: none;'
            	}
            }
            ]


});