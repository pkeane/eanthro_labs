$(document).ready(function() {
	chart2 = new Highcharts.Chart({
		chart: {
			renderTo: 'chart2',
			type: 'bar'
		},
		title: {
			text: 'Fruit Consumption'
		},
		xAxis: {
			categories: ['Apples', 'Bananas', 'Oranges']
			},
			yAxis: {
				title: {
					text: 'Fruit eaten'
				}
			},
			series: [{
				name: 'Jane',
				data: [1, 0, 4]
				}, {
					name: 'John',
					data: [5, 7, 3]
					}]
				});
			});

