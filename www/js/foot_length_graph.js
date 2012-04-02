var mychart;
$(document).ready(function() {
	var url = $('link[rel="foot_length_data"]').attr('href');
	$.getJSON(url,function(series_data) {
		mychart = new Highcharts.Chart({
			chart: {
				renderTo: 'foot_length_graph', 
				defaultSeriesType: 'scatter',
				zoomType: 'xy'
			},
			title: {
				text: 'Foot Length Versus Height by Gender'
			},
			xAxis: {
				title: {
					enabled: true,
					text: 'Foot Length (cm)'
				},
				startOnTick: true,
				endOnTick: true,
				showLastLabel: true
			},
			yAxis: {
				title: {
					text: 'Height (cm)'
				}
			},
			tooltip: {
				formatter: function() {
					return ''+
					this.x +' cm, '+ this.y +' cm';
				}
			},
			legend: {
				layout: 'vertical',
				align: 'left',
				verticalAlign: 'top',
				x: 100,
				y: 70,
				floating: true,
				//backgroundColor: Highcharts.theme.legendBackgroundColor || '#FFFFFF',
				borderWidth: 1
			},
			plotOptions: {
				scatter: {
					marker: {
						radius: 5,
						states: {
							hover: {
								enabled: true,
								lineColor: 'rgb(100,100,100)'
							}
						}
					},
					states: {
						hover: {
							marker: {
								enabled: false
							}
						}
					}
				}
			},
			series: series_data.data
		});
	});
});

