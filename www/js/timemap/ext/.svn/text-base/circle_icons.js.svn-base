
/**
 * Create the URL for a Google Charts circle image.
 */
TimeMapTheme.getCircleUrl = function(size, color, alpha, includeDomain) {
    return "http://chart.apis.google.com/" + 
        "chart?cht=it&chs=" + size + "x" + size + 
        "&chco=" + color + ",00000001,ffffff01" +
        "&chf=bg,s,00000000|a,s,000000" + alpha + "&ext=.png";
};

/**
 * Creates a flat circle icon. Based on code from MapIconMaker:
 * http://code.google.com/p/gmaps-utility-library-dev/
 * @copyright (c) 2008 Pamela Fox
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License. 
 *  
 * @param {Object} [opts]       Config options
 * @param {Number} [opts.size=20]           Diameter of circle
 * @param {String} [opts.color='1f77b4']    Circle color, in RRGGBB hex or rgb(r,g,b) format
 * @param {String} [opts.alpha='bb']        Circle alpha, in AA hex
 * @return {GIcon}
 */
TimeMapTheme.createCircleIcon = function (opts) {
    var defaults = {
            size: 20,
            color: "1f77b4",
            alpha: "bb"
        },
        opts = TimeMap.util.merge(opts, defaults),
        size = opts.size,
        radius = size / 2,
        image = opts.image,
        polyNumSides = 8,
        polySideLength = 360 / polyNumSides,
        icon = new GIcon(G_DEFAULT_ICON),
        a, aRad, pixelX, pixelY;
    // create image
    if (!image) {
        image = TimeMapTheme.getCircleUrl(size, opts.color, opts.alpha);
    }
    icon.image = image;
    icon.iconSize = new GSize(size, size);
    icon.shadowSize = new GSize(0, 0);
    icon.iconAnchor = new GPoint(radius, radius);
    icon.infoWindowAnchor = new GPoint(radius, radius);
    
    // make img map
    icon.imageMap = [];
    for (a = 0; a < (polyNumSides + 1); a++) {
        aRad = polySideLength * a * (Math.PI / 180);
        pixelX = radius + radius * Math.cos(aRad);
        pixelY = radius + radius * Math.sin(aRad);
        icon.imageMap.push(parseInt(pixelX), parseInt(pixelY));
    }

    return icon;
};

/**
 * Create a timemap theme with matching event icons and sized map circles
 *  
 * @param {Object} [opts]       Config options
 * @param {Number} [opts.size=20]           Diameter of map circle
 * @param {Number} [opts.eventIconSize=10]  Diameter of event circle
 * @param {String} [opts.color='1f77b4']    Circle color (map + event), in RRGGBB hex or rgb(r,g,b) format
 * @param {String} [opts.alpha='bb']        Circle alpha (map), in AA hex
 * @param {String} [opts.eventAlpha='ff']   Circle alpha (event), in AA hex
 */
TimeMapTheme.createCircleTheme = function(opts) {
    var defaults = {
            eventIconSize:10,
            eventAlpha:'ff'
        },
        opts = TimeMap.util.merge(opts, defaults),
        image = opts.eventIconImage;
    // create image
    if (!image) {
        image = TimeMapTheme.getCircleUrl(opts.eventIconSize, opts.color, opts.eventAlpha);
    }
    return new TimeMapTheme({
        icon: TimeMapTheme.createCircleIcon(opts),
        eventIcon: image,
        color: opts.color
    });
};
 