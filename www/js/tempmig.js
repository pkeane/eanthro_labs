var eAnthro = {
	eventPainter: function(params) { return new Timeline.OriginalEventPainter(params); },
	timeMap: undefined
};

// Shadow Timeline.Geochrono and Timeline.GeochronoUnit.
var Geochrono = Timeline.Geochrono;
var GeochronoUnit = Timeline.GeochronoUnit;

// Initialize the TimeMap.
eAnthro.initTimeMap = function(params) {
	// Initialize themes for the TimeMap.
	this.initTimeMapThemes();

	var timelineThemes = eAnthro.timelineThemes;
	console.log(timelineThemes);
	var eventSource = new Timeline.DefaultEventSource(
		new SimileAjax.EventIndex(GeochronoUnit)
	);
	
	// Actually initialize the TimeMap.
	this.timeMap = TimeMap.init({
		// Timeline bands.
		bands: [
			Geochrono.createBandInfo({
				eventSource: eventSource,
				eventPainter: this.eventPainter,
				intervalPixels: 550,
				intervalUnit: GeochronoUnit.MA,
				theme: timelineThemes.upper,
				width: '80%',
				zoomIndex: 0,
				zoomSteps: [
					{pixelsPerInterval: 250, unit: GeochronoUnit.MA},
					{pixelsPerInterval: 350, unit: GeochronoUnit.MA},
					{pixelsPerInterval: 450, unit: GeochronoUnit.MA},
					{pixelsPerInterval: 550, unit: GeochronoUnit.MA}
				]
			}),
			Geochrono.createBandInfo({
				highlight: true,
				intervalPixels: 60,
				intervalUnit: GeochronoUnit.EPOCH,
				theme: timelineThemes.lower,
				syncWith: 0,
				width: '20%'
			})
		],

		// Scrolls the Timeline after the data is displayed.
		dataDisplayedFunction: function(timeMap) {
			timeMap.timeline.getBand(0).setMaxVisibleDate(GeochronoUnit.wrapMA(0));
		},

		// TimeMap data sets. 
		datasets: [{
			id: params.id,
			options: {
				dateParser: GeochronoUnit.getParser(),
				eventSource: eventSource,
				items: data,
				theme: 'eanthro'
			},
			title: params.title,
			type: 'basic'
		}],
		
		// The id of the TimeMap map div.
		mapId: 'map',

		// TimeMap options.
		options: {
			centerOnItems: true,
			mapType: 'satellite',
			openInfoWindow: function() {
				var html = params.makeInfoWindowHtml(this);
				if ($('#mapcontainer').is(':visible')) {	
					if (this.getType() == "marker")
						this.placemark.openInfoWindowHtml(html);
					else this.map.openInfoWindowHtml(this.getInfoPoint(), html);
				} else window.location = url;
			},
			syncBands: true
		},

		// Fixes: Uncaught TypeError: Object [object Object] has no method 'getTime'
		scrollTo: false,
		
		// The id of the TimeMap Timeline div.
		timelineId: 'timeline'
	});
};

// Initialize themes for the TimeMap.
eAnthro.initTimeMapThemes = function() {
	var iconPath = 'labs/www/images/icons/';  // Hardcode the path.
	var themes = TimeMap.themes;

	// Create a base theme and GIcon for eAnthro TimeMaps.
	var eAnthroIcon = new GIcon();
	eAnthroIcon.iconAnchor = new GPoint(10, 34);
	eAnthroIcon.iconSize = new GSize(33, 37);
	eAnthroIcon.image = iconPath + 'felis.png';
	var infoWindowAnchor = new GPoint(33, 1);
	eAnthroIcon.infoWindowAnchor = infoWindowAnchor;
	eAnthroIcon.infoWindowShadow = infoWindowAnchor;
	eAnthroIcon.shadow = null;  // These icons already have shadows.
	themes.eanthro = new TimeMapTheme({
		eventIcon: iconPath + 'event-felis.png',
		icon: eAnthroIcon,
		iconImage: iconPath + 'felis.png'
	});

	var as = ['glacial', 'interglacial'];
	var bs = ['archaeology', 'fauna', 'hominin'];

	for (ai in as) {
		var a = as[ai];
		for (bi in bs) {
			var b = bs[bi];
			var name = [a, b].join('-');
	
			TimeMap.themes[name] = TimeMapTheme.create('eanthro', {
				eventIcon: iconPath + 'event-' + name + '.png',
				icon: new GIcon(eAnthroIcon, iconPath + a + '.png')
			});
		}
	}
};

// Initialize themes for the Timeline.
eAnthro.timelineThemes = (function() {
	var ClassicTheme = Timeline.ClassicTheme;

	// Settings for the upper theme.
	var upper = ClassicTheme.create();
	upper.mouseWheel = 'zoom';
	upper.timeline_stop = GeochronoUnit.wrapMA(0);

	var event = upper.event;
	event.tape.height = 10;
	event.track = {
		autoWidthMargin: 1.5,
		gap: 2,
		height: 12,
		offset: 22
	};
	
	return {
		upper: upper,
		lower: ClassicTheme.create()
	};
})();

$(function() {
	eAnthro.initTimeMap({
		id: 'tempmig',
		makeInfoWindowHtml: function(that) {
			var metadata = that.opts.metadata;
			var result = '<table border="0">';
			for (key in metadata)
				result += '<tr><td>'+key+':</td><td>' + metadata[key] + '</td></tr>'
			return result + '</table>';
		}
	});
});
