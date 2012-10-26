<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <title></title>
  <script type="text/javascript" src="http://www.google.com/jsapi"></script>
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
  <script type="text/javascript">
  	window.data = {};
  	window.chart = {};
  	window.dataDyn = {};
	window.trailsize = 50;
	window.interval	= 5000;
	window.chartOptions = {'colors':['green','red','grey','blue']};

    google.load('visualization', '1', {packages: ['corechart']});
    function drawVisualization() {

    	var date = new Date();
    	window.data = new google.visualization.DataTable();

    	window.data.addColumn('datetime', 'Time');
    	window.data.addColumn('number', 'Ranch');
    	window.data.addColumn('number', 'Poker');
    	window.data.addColumn('number', 'Idle');
    	window.data.addColumn('number', 'Total');
    	window.data.addRows(window.trailsize);



     	window.chart = new google.visualization.LineChart(document.getElementById('visualization'));
      	window.chart.draw(window.data, window.chartOptions);
    }

    google.setOnLoadCallback(drawVisualization);

	$(document).ready(function() {

		getOnlineUsersData();
		window.interval = setInterval('getOnlineUsersData()', window.interval);

	});

	function getOnlineUsersData()
	{
		$.post('/statistics/onlineuserslive/getdata.php', function(data) {
			addDataToChart(data);

		}, "json");
	}

	function addDataToChart(data)
	{
		var i,j;
		for(i=0; i<	window.trailsize; i++)
		{
			if(dataDyn[i+1] == undefined)
			{
				dataDyn[i+1] = {datetime: new Date(), ranch: 0, poker: 0, idle: 0, total: 0};
			}
			dataDyn[i] = dataDyn[i+1];
		}

		dataDyn[ window.trailsize-1 ] = {datetime: new Date(), ranch: data.ranch, poker: data.poker, idle: data.idle, total: data.total};

		for(i=0; i<	window.trailsize; i++)
		{
			window.data.setValue(i, 0, dataDyn[i].datetime);
			window.data.setValue(i, 1, dataDyn[i].ranch);
			window.data.setValue(i, 2, dataDyn[i].poker);
			window.data.setValue(i, 3, dataDyn[i].idle);
			window.data.setValue(i, 4, dataDyn[i].total);

		}
		window.chart.draw(window.data, window.chartOptions);
	}

  </script>
  <style type="text/css">
  html, body {
  	font-family: Arial;
  	border: 0 none;
  	margin: 0;
  	padding: 0;
  	height: 100%;
  }
  </style>
</head>
<body>

<div id="visualization" style="width: 100%; height: 400px;"></div>
</body>
</html>