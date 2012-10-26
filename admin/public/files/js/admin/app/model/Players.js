Ext.define('Admin.model.Players', {
    extend: 'Ext.data.Model',

    fields: [
             'pkUserID','facebookID','firstname','lastname','email','chips','gold','ranchvalue','gender','ipaddress','lastPlayed'
    ],
    sorters: [
              'pkUserID','facebookID','firstname','lastname','email','chips','gold','ranchvalue','gender','ipaddress','lastPlayed'
    ]

});