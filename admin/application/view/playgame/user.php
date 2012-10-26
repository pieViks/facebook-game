<!DOCTYPE html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<title>playgame</title>

	<script src="https://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>
	<style type="text/css">
	html,body { margin:0; padding: 0; }
	</style>
</head>
<body>
<div id="gameObject">loading</div>
<script>
	window.version 			= '0.4.0';
	window.gamelocation		= 'gopsocial.swf?v='+window.version;
	window.api_root			= '<?= $api_root ?>';

	window.environment		= '<?= $environment ?>';
	window.token			= '<?= $token ?>';

	window.flashArguments 	= {
		token 		: window.token,
		environment	: window.environment,
		version		: window.version,
		gatewayurl	: window.api_root + 'gateway/amf.php'
	}

	swfobject.embedSWF(window.gamelocation,"gameObject","100%","800","9.0.0","expressInstall.swf", window.flashArguments,
		{
				bgcolor: "#ffffff",
				allowscriptaccess: "always",
				wmode: "opaque",
				scale: "default",
				align: "left",
				allowscale: "false"
			},
		{}
	);

	function outOfSync() {
		window.location.href = window.location.href;
	}

	function openTab(label) {
		alert('This feature is disabled in the admin');
	}
	function sendRequest(msgId, dynId, userId) {
		alert('This feature is disabled in the admin');
	}
	function sendFriendRequest(msgId, dynId, userId, json) {
		alert('This feature is disabled in the admin');
	}
	function sendWallPost(msgId,userId,json) {
		alert('This feature is disabled in the admin');
	}

	</script>
</body>
</html>
