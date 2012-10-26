Ext.onReady(function() {
	var login = new Ext.FormPanel(
	{
		labelWidth : 50,
		url : window.cfg.gatekeeper,
		frame : true,
		defaultType : 'textfield',
		monitorValid : true,

		items : [
		         {
					fieldLabel : 'Username',
					name : 'username',
					allowBlank : false
		         },
		         {
					fieldLabel : 'Password',
					name : 'password',
					inputType : 'password',
					allowBlank : false
		         }],

		buttons : [
		            {
		            	text : 'Login',
						formBind : true,
						handler : function() {
							login.getForm().submit(
							{
								method : 'POST',
								waitTitle : 'Authenticating',
								waitMsg : 'Sending credentials...',
								success : function()
								{
									Ext.Msg.alert(
										'Status',
										'Login Successful! <br />You are being redirected...',
										function(btn, text) {
											if (btn == 'ok') {
												window.location = window.cfg.redirect;
											}
										}
									);
									window.location = window.cfg.redirect;
								},

								failure : function(form, action) {

										Ext.Msg.alert('Login Failed', 'Incorrect username/password combination.');

										login.getForm().reset();
									}
								});
							}
						}
		            ]
	});

	var win = new Ext.Window({
		layout : 'fit',
		width : 300,
		y: 30,
		closable : false,
		resizable : false,
		draggable : false,
		plain : true,
		border : false,
		items : [ login ],
		title : 'Governor of Poker Social Admin ~ Please Login'
	}).show();

});