var Dase = {};
Dase.user = {};

//note: since modules create a module-specific base href, we need to strip module/<mod_name>
var base = document.getElementsByTagName('base');
if (base.length) {
	Dase.base_href = base[0].href.replace(/\/modules\/[^/]*/,'');
}

/* from DOM Scripting p. 103 */
Dase.addLoadEvent = function(func) {
	var oldonload = window.onload;
	if (typeof window.onload != 'function') {
		window.onload = func;
	} else {
		window.onload = function() {
			if (oldonload) {
				oldonload();
			}
			func();
		};
	}
};

/****** utiltities **************/

Dase.$ = function(id) {
	if (!id) return;
	return document.getElementById(id);
};

Dase.trim = function(str) {
	//from: http://blog.stevenlevithan.com/archives/faster-trim-javascript
	var	str = str.replace(/^\s\s*/, ''),
	ws = /\s/,
	i = str.length;
	while (ws.test(str.charAt(--i)));
	return str.slice(0, i + 1);
}

Dase.addClass = function(elem,cname) {
	if (!elem || !cname) return false;
	if (elem.className) {
		elem.className = elem.className + " " + cname;
	} else {
		elem.className = cname;
	}
	return true;
};

Dase.removeClass = function(elem,cname) {
	if (!elem || !cname) return false;
	var cnames = elem.className.split(" ");
	var newClassName = '';
	for (var i=0;i<cnames.length;i++) {
		if (cname != cnames[i]) {
			newClassName = newClassName + " " + cnames[i];
		}
	}
	elem.className = newClassName;
	return true;
};

Dase.hasClass = function(elem,cname) {
	if (!elem || !cname) return false;;
	var cnames = elem.className.split(" ");
	for (var i=0;i<cnames.length;i++) {
		if (cname == cnames[i]) {
			return true;
		}
	}
	return false;
};

Dase.getLinkByRel = function(rel) {
	//return FIRST hit
	var links = document.getElementsByTagName('link');
	for (var i=0;i<links.length;i++) {
		if (rel == links[i].rel) {
			return links[i].href;
		}
	}
	return;
};

Dase.getMeta = function(name) {
	//return FIRST hit
	var metas = document.getElementsByTagName('meta');
	for (var i=0;i<metas.length;i++) {
		if (name == metas[i].name) {
			return metas[i].content;
		}
	}
	return;
};

Dase.toggle = function(el) {
	if (Dase.hasClass(el,'hide')) {
		Dase.removeClass(el,'hide');
		return true;
	} else {
		Dase.addClass(el,'hide');
		return false;
	}
};

Dase.showElem = function(elem) {
	if (elem.scrollIntoView) {
		elem.scrollIntoView();
	}
}

Dase.createElem = function(parent,value,tagName,className,id) {
	if (!parent) {
		//alert('no parent');
		return;
	}
	var element = document.createElement(tagName);
	element.style.visibility = 'hidden';
	if (value) {
		element.appendChild(document.createTextNode(value));
	}
	parent.appendChild(element);
	if (className) {
		element.className = className;
	}
	if (id) {
		element.id = id;
	}
	element.style.visibility = 'visible';
	return element;
};

Dase.removeChildren = function(target) {
	if (!target) return;
	while (target.childNodes[0]) {
	target.removeChild(target.childNodes[0]);
}
}

Dase.highlight = function(target,time,cname) {
	if (!time) {
		time = 1500;
	}
	if (!cname) {
		cname = 'highlight';
	}
	Dase.addClass(target,cname);
	setTimeout(function() {
		Dase.removeClass(target,cname);
	},time);
}

Dase.countdown = function(element_id,num,interval) {
	if (!interval) { interval = 800; }
function show(j) { return (function(){ Dase.$(element_id).innerHTML=num-j; })};
for (var i = 0; i <= num; i += 1) {
	var ref = show(i);
	setTimeout(ref,i*interval);
}
};

Dase.pageReload = function(msg) {
	if (msg) {
		alert(msg);
	} 
	var date = new Date();
	var rand = date.getTime() + '';
	rand = rand.substring(9);

	var curr = window.location.href;
	if (-1 == curr.indexOf('?')) {
		curr = curr + '?cb=' + rand;
	} else {
		curr = curr + '&cb=' + rand;
	}
	window.location.href = curr;
}

Dase.truncate = function(str,len) {
	if (str.length <= len ) return str;
	var small = str.slice(0,len);
	small = small + '...';
	return small.toString();
};

/*** end utils ****/

Dase.logoff = function() {
	if (Dase.user.eid) {
		Dase.ajax(Dase.base_href + 'login/' + Dase.user.eid,'DELETE',
		function(resp) { 
			var jsonObj = JSON.parse(resp);
			if ('location' in jsonObj) {
				window.location.href = jsonObj.location;
			} else {
				window.location.href = Dase.base_href+'login/form';
			}
		});
	} else {
		window.location.href = Dase.base_href+'login/form';
	}
}

Dase.getEid = function() {
	var base = Dase.base_href;
	var d = new Date();
	if (!base) return;
	var cookiename = base.substr(7,base.length-8).replace(/\/|\./g,'_') + '_' + 'DASE_USER';
	//adapted from rhino 5th ed. p 460
	var allcookies = document.cookie;
	if (!allcookies) return;
	var pos = allcookies.indexOf(cookiename + "=");
	if (pos != -1) {
		var start = pos + cookiename.length + 1;
		var end = allcookies.indexOf(";",start); 
		if (end == -1) end = allcookies.length;
		var value = allcookies.substring(start,end);
		return decodeURIComponent(value);
	} else {
		return false;
	}
};

Dase.checkTokenDate = function(my_func) {
	var url = Dase.base_href + "date";
	Dase.ajax(url,'GET',function(resp) {
		if (Dase.user.token_date != resp) {
			alert('uh oh '+resp+' != '+Dase.user.token_date);
		} else {
			my_func();
		}
	});
}

Dase.initUser = function() {
	var eid = Dase.getEid();
	if (!eid) {
		Dase.removeClass(Dase.$('loginControl'),'hide');
		return;
	}
	Dase.loadingMsg(true);
	//per HATEOS, ought to come from a URI template in hypertext
	var url = Dase.base_href + "user/"+eid+ "/data"
	Dase.getJSON(url,function(json){
		for (var eid in json) {
			Dase.user.eid = eid;
			Dase.dbname = json[eid].dbname;
			Dase.user.htpasswd = json[eid].htpasswd;
			Dase.user.name = json[eid].name;
			Dase.user.tags = json[eid].tags;
			Dase.user.collections = json[eid].collections;
			Dase.user.recent_views = json[eid].recent_views;
			Dase.user.recent_searches = json[eid].recent_searches;
			Dase.user.current_collections = json[eid].current_collections;
			Dase.user.is_superuser = json[eid].is_superuser;
			Dase.user.cart_count = json[eid].cart_count;
			//use this to verify that browser & server have same date
			//used in token seeding
			Dase.user.token_date = json[eid].token_date;
			//whether or not ot display editing controls
			Dase.user.controls = json[eid].controls;
			if ('dase_prod' != Dase.dbname) {
				Dase.placeDbName(Dase.dbname);
			}
			Dase.placeUserName(eid);
			Dase.placeUserTags(Dase.user);
			Dase.placeRecentViews(Dase.user);
			Dase.placeRecentSearches(Dase.user);
			Dase.placeUserCollections(eid);
			Dase.placePreferredCollections(eid);
			Dase.placeCollectionManageLink(eid);
			Dase.placeAdminLink(eid);
			Dase.initCart();
			Dase.initAddToCart();
			//our generic page-specific function
			//that requires knowledge of the eid
			if (Dase.pageInitUser && typeof Dase.pageInitUser === 'function') {
				Dase.pageInitUser(eid);
			} 
			Dase.addClass(Dase.$('menuGrayed'),'hide');
			Dase.removeClass(Dase.$('menu'),'hide');
		}
		Dase.loginControl(Dase.user.eid);
		Dase.multicheck("checkedCollection");
	});
};

Dase.loginControl = function(eid) {
	if (eid) {
		Dase.removeClass(Dase.$('logoffControl'),'hide');
	} else {
		Dase.removeClass(Dase.$('loginControl'),'hide');
	}
};

Dase.initLogoff = function() {
	var link = Dase.$('logoff-link');
	if (!link) return;
	link.onclick = function() {
		Dase.logoff();
		return false;
	};
};

Dase.placeDbName = function(dbname) {
	var el = Dase.$('dbName');
	if (el) {
		el.innerHTML = dbname;
	}
};

Dase.placeUserName = function(eid) {
	var nameElem = Dase.$('userName');
	if (nameElem) {
		/*	nameElem.innerHTML = Dase.user.name;
		 */
		nameElem.innerHTML = eid;
		var settingsElem = Dase.$('settings-link');
		settingsElem.href = 'user/'+eid+'/settings';
		var settingsMenuLinkElem = Dase.$('settings-menu-link');
		if (settingsMenuLinkElem) {
			settingsMenuLinkElem.href = 'user/'+eid+'/settings';
		}
		var eidElem = Dase.$('eid');
		eidElem.innerHTML = eid;
	}
};

Dase.checkAdminStatus = function(eid) {
	var current_coll_elem = Dase.$('collectionAsciiId');  
	if (!current_coll_elem) return;
	var current_coll = current_coll_elem.innerHTML;  
	//for (var i=0;i<Dase.user.collections.length;i++) {
	for (var i in Dase.user.collections) {
		var c = Dase.user.collections[i];
		//display link to administer collection if user has privs
		if (current_coll && (c.ascii_id == current_coll)) {  
			var auth_info = {
				'collection_ascii_id':current_coll,
				'eid':eid,
				'auth_level':c.auth_level,
				'collection_name':c.collection_name
			}
			return auth_info;
		}
	}
	return false;
};

Dase.placeAdminLink = function(eid) {
	var adminLink = Dase.$('adminLink');
	if (adminLink && Dase.user.is_superuser) {
		adminLink.innerHTML = 'DASe Archive Admin';
		Dase.removeClass(adminLink,'hide');
	}
};

Dase.placeCollectionManageLink = function(eid) {
	var auth_info = Dase.checkAdminStatus(eid);
	if (!auth_info) return;
	var manageLink = Dase.$('manageLink');
	var manageLinkHeader = Dase.$('manageLinkHeader');
	//set footer link
	if (auth_info.auth_level == 'write' || auth_info.auth_level == 'admin') {
		manageLink.setAttribute('href','manage/'+auth_info.collection_ascii_id);
		manageLink.innerHTML = 'Manage '+auth_info.collection_name;
		Dase.removeClass(manageLink,'hide');
		//set menu link
		if (manageLinkHeader && auth_info.collection_ascii_id) {
		//	manageLinkHeader.setAttribute('href','manage/'+auth_info.collection_ascii_id);
		//	manageLinkHeader.innerHTML = '[manage collection]';
		//	Dase.removeClass(manageLinkHeader,'hide');
		}
	}
};

Dase.placeUserCollections = function(eid) {
	var cartLink = Dase.$('cartLink');
	if (cartLink) {
		cartLink.setAttribute('href','user/'+eid+'/cart');
	}
	var hasSpecial = 0;
	var coll_list = Dase.$('collectionList');
	if (!coll_list) return;
	//for (var i=0;i<Dase.user.collections.length;i++) {
	for (var i in Dase.user.collections) {
		var c = Dase.user.collections[i];
		if ("1" != c.is_public) {
			//todo: replace w/ htmlbuilder
			hasSpecial++;
			var li = document.createElement('li');
			li.setAttribute('id',c.ascii_id);
			var input = document.createElement('input');
			input.setAttribute('type','checkbox');
			input.setAttribute('name','c');
			input.setAttribute('value',c.ascii_id);
			//input.setAttribute('checked','checked');
			li.appendChild(input);
			li.appendChild(document.createTextNode(' '));
			var a = document.createElement('a');
			a.setAttribute('href','collection/'+c.ascii_id);
			a.setAttribute('class','checkedCollection');
			a.className = 'checkedCollection';
			a.appendChild(document.createTextNode(c.collection_name));
			li.appendChild(a);
			li.appendChild(document.createTextNode(' '));
			var span = document.createElement('span');
			span.setAttribute('class','tally');
			span.className = 'tally';
			span.appendChild(document.createTextNode('('+c.item_count+')'));
			li.appendChild(span);
			coll_list.appendChild(li);
		}
	}
	if (hasSpecial) {
		//this simply shows the "Special Access Collections" subhead
		Dase.removeClass(Dase.$('specialAccessLabel'),'hide');
	}
};

Dase.placePreferredCollections = function(eid) {
	if (!Dase.user.current_collections) return;
	var form = Dase.$('homeSearchForm');
	if (!form) return;
	var colls = Dase.user.current_collections.split('|');
	if (1 == colls.length) {
		if (!Dase.$(colls[0])) {
			return;
		}
	}
	var preferred = {};
	for (var i=0;i<colls.length;i++) {
		preferred[colls[i]] = true;
	}
	inputs = form.getElementsByTagName('input');
	var prefs = new Array();
	for (var i=0;i<inputs.length;i++) {
		var inp = inputs[i];
		if ('c' == inp.name) {
			if (preferred[inp.value]) {
				inp.checked = true;
				var link = inp.parentNode.getElementsByTagName('a')[0];
				link.className = 'checkedCollection';
			} else {
				inp.checked = false;
				inp.className = 'check';
				var link = inp.parentNode.getElementsByTagName('a')[0];
				link.className = '';
			}
		}
	}
};

Dase.initMenu = function(id) { 
	var menu = Dase.$(id);
	if (menu) {
		var listItems = menu.getElementsByTagName('li');
		for (var i=0;i<listItems.length;i++) {
			var listItem = listItems[i];
			var sub = listItem.getElementsByTagName('ul');
			if (sub) {
				var listItemLink = listItem.getElementsByTagName('a')[0];
				if (listItemLink) {
					listItemLink.onclick = function() {
						if (!Dase.user.eid) {
							Dase.logoff();
							return false;
						} 
						var child_ul = this.parentNode.getElementsByTagName('ul')[0];
						if (child_ul) {
							Dase.toggle(child_ul);
							return false;
						} else {
							return true;
						}
					};
				}
			}
		}
	}
};

Dase.multicheckItems = function(className) {
	if (!className) {
		className = 'check';
	}
	var item_set = Dase.$('itemSet');
	if (!item_set)  return; 
	target = Dase.$('checkall');
	if (!target)  return; 
	target.className = className;
	var boxes = item_set.getElementsByTagName('input');
	if (!boxes.length) {
		target.className = 'hide';
		var tag_name_el = Dase.$('tag_name');
		if (tag_name_el) {
			//todo: this should REALLY be implemented as a 
			//'delete' request (using XHR to hijack form submit)
			var button = Dase.$('removeFromSet');
			button.name = 'delete_tag';
			button.value = 'Delete '+tag_name_el.innerHTML;
			button.onclick = null;
		}
		return;
	}
	target.onclick = function() {
		if ('uncheck' == this.className) {
			for (var i=0; i<boxes.length; i++) {
				boxes[i].checked = false;
			}	   
			this.className = 'check';
		} else {
			for (var i=0; i<boxes.length; i++) {
				boxes[i].checked = true;
			}	   
			this.className = 'uncheck';
		}
		Dase.multicheckItems(this.className);
		return false;
	};
};

Dase.multicheck = function(c) { 
	var coll_list = Dase.$('collectionList');
	if (!coll_list) { return; }
	target = Dase.$('checkall');
	if (!target) { return; }
	//class of the link determines its behaviour
	target.className = 'uncheck';
	var boxes = coll_list.getElementsByTagName('input');
	target.onclick = function() {
		for (var i=0; i<boxes.length; i++) {
			var box = boxes[i];
			if ('uncheck' == this.className) {
				box.checked = null;
				box.parentNode.getElementsByTagName('a')[0].className = '';
			} else {
				box.checked = true;
				box.parentNode.getElementsByTagName('a')[0].className = c;
			}
		}	   
		if ('uncheck' == this.className) {
			this.className = 'check';
		} else {
			this.className = 'uncheck';
		}
		return false;
	};
	/* changes the color of the collection name when box
	 * next to it is checked/unchecked
	 */
	for (var i=0; i<boxes.length; i++) {
		boxes[i].onclick = function() {
			var link = this.parentNode.getElementsByTagName('a')[0];
			if (c == link.className) {
				link.className = '';
			} else {
				link.className = c;
			}
		};
	}	   
};

Dase.loadingMsg = function(displayBool) {
	var loading = Dase.$('ajaxMsg');
	if (!loading) return;
	if (displayBool) {
		Dase.removeClass(loading,'hide');
		//loading.innerHTML = 'loading page data...';
		setTimeout('Dase.loadingMsg(false)',1500);
	} else {
		Dase.addClass(loading,'hide');
	}
}

Dase.placeRecentSearches = function(user) {
	if (!Dase.$('searches-submenu')) return;
	// user sets menu 
	var h = new Dase.htmlbuilder;
	for (var i=0; i<user.recent_searches.length;i++) {
		var recent = user.recent_searches[i];
		var li = h.add('li');
		var a = li.add('a');
		a.set('href',encodeURI(recent.url));
		a.setText(recent.title+' ('+recent.count+')');
	}
	var li = h.add('li').add('a',{'href':'#','id':'clearRecentSearches','class':'edit'},'clear all');
	h.attach(Dase.$('searches-submenu')); //append
	Dase.initClearRecentSearches(user);
};

Dase.placeRecentViews = function(user) {
	if (!Dase.$('recent-submenu')) return;
	// user sets menu 
	var h = new Dase.htmlbuilder;
	for (var i=0; i<user.recent_views.length;i++) {
		var recent = user.recent_views[i];
		var li = h.add('li');
		var a = li.add('a');
		a.set('href',encodeURI(recent.url));
		a.setText(recent.title);
	}
	var li = h.add('li').add('a',{'href':'#','id':'clearRecentViews','class':'edit'},'clear all');
	h.attach(Dase.$('recent-submenu')); //append
	Dase.initClearRecent(user);
};

Dase.placeUserTags = function(user) {
	if (!Dase.$('sets-submenu')) return;
	// user sets menu 
	var h = new Dase.htmlbuilder;
	var li = h.add('li').add('a',{'href':'new','id':'createNewSet','class':'edit'},'create new set');
	for (var i in user.tags) {
	//for (var i=0;i<user.tags.length;i++) {
		var tag = user.tags[i];
		if ('set' == tag.type || 'slideshow' == tag.type) {
			var li = h.add('li');
			var a = li.add('a');
			a.set('href','tag/'+user.eid+'/'+tag.ascii_id);
			a.setText(tag.name+' ('+tag.item_count+')');
		}
	}
	h.attach(Dase.$('sets-submenu')); //append

	//save to select menu
	var h = new Dase.htmlbuilder;
	var sel = h.add('select',{'id':'saveToSelect','name':'collection_ascii_id'});
	sel.add('option',{'value':''},'save checked items to...');
	//for (var n in user.tags) {
	for (var i=0;i<user.tags.length;i++) {
		var tag = user.tags[i];
		if ('admin' != tag.type) {
			var opt = sel.add('option',{'value':tag.ascii_id});
			opt.setText(tag.name+' ('+tag.item_count+')');
		}
	}
	var inp = h.add('input',{'type':'submit','value':'add'});
	var item_set = Dase.$('itemSet');
	if (item_set) {
		var items = item_set.getElementsByTagName('td');
	}
	if (Dase.$('saveChecked') && item_set && items.length) {
		h.attach(Dase.$('saveChecked'));
	}
	Dase.initCreateNewSet();
};

Dase.initClearRecent = function(user) {
	var clearRecentViewsLink = Dase.$('clearRecentViews');
	if (clearRecentViewsLink) {
		clearRecentViewsLink.onclick = function() {
			url = Dase.base_href+'user/'+Dase.user.eid+'/recent';
			Dase.ajax(url,'delete',function(resp) {
				alert(resp);
			},null,Dase.user.eid,Dase.user.htpasswd); 
			user.recent_views = [];
			Dase.placeRecentViews(user);
			return false;
		};
	}
};

Dase.initClearRecentSearches = function(user) {
	var clearRecentSearchesLink = Dase.$('clearRecentSearches');
	if (clearRecentSearchesLink) {
		clearRecentSearchesLink.onclick = function() {
			url = Dase.base_href+'user/'+Dase.user.eid+'/recent_searches';
			Dase.ajax(url,'delete',function(resp) {
				alert(resp);
			},null,Dase.user.eid,Dase.user.htpasswd); 
			user.recent_searches = [];
			Dase.placeRecentSearches(user);
			return false;
		};
	}
};

Dase.initCreateNewSet = function() {
	var createNewSetLink = Dase.$('createNewSet');
	if (createNewSetLink) {
		createNewSetLink.onclick = function() {
			tag_name = prompt("Enter name of set","");
			Dase.ajax(Dase.base_href+'tags','POST',function(resp) { 
				Dase.initUser(); 
				Dase.initSaveTo();
				alert(resp);
			},tag_name);
			return false;
		};
	}
};

Dase.createXMLHttpRequest = function() {
	var xmlhttp;
	if (window.XMLHttpRequest) {
		xmlhttp = new XMLHttpRequest();
	} else if (window.ActiveXObject) {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	} else {
		alert('Perhaps your browser does not support xmlhttprequests?');
	}
	return xmlhttp;
};

Dase.ajax = function(url,method,my_func,msgBody,username,password,content_headers,error_func) {
	if (!method) {
		method = 'POST';
	}
	var xmlhttp = Dase.createXMLHttpRequest();
	xmlhttp.open(method,url,true);
	if (username && password) {
		xmlhttp.setRequestHeader('Authorization','Basic '+Base64.encode(username+':'+password));
	}
	if (content_headers) {
		for (var k in content_headers) {
			xmlhttp.setRequestHeader(k,content_headers[k]);
		}
	}
	xmlhttp.setRequestHeader("X-Requested-With", "XMLHttpRequest");
	if (msgBody) {
		xmlhttp.send(msgBody);
	} else {
		xmlhttp.send(null);
	}
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4) {
			if (xmlhttp.status == 200) {
				if (my_func) {
					my_func(xmlhttp.responseText);
				}
			} 
			if (xmlhttp.status == 201) {
				if (my_func) {
					//todo: think about this
					my_func(xmlhttp.getResponseHeader('Location'));
				}
			} 
			if (xmlhttp.status != 200 && xmlhttp.status != 201) {
				if (error_func) {
					error_func(xmlhttp.status+' '+xmlhttp.responseText);
				}
			} 
		}
	};
};

Dase.getJSON = function(url,my_func,error_func,params,username,password) {
	var xmlhttp = Dase.createXMLHttpRequest();
	// this is to deal with IE6 cache behavior
	// also note that JSON data needs to be up-to-the-second
	// accurate given the way we currently do deletes!
	var date = new Date();

	//per http://www.subbu.org/weblogs/main/2005/10/xmlhttprequest.html
	//this may be unnecessary
	if (params) {
		//url = url + '?' + params +'&format=json';
		url = url + '?' + params + '&cache_buster=' + date.getTime()+'&format=json';
	} else {
		//url = url + '?format=json';
		url = url + '?cache_buster=' + date.getTime()+'&format=json';
	}

	xmlhttp.open('get',url,true);
	if (username && password) {
		xmlhttp.setRequestHeader('Authorization','Basic '+Base64.encode(username+':'+password));
	}
	xmlhttp.setRequestHeader("X-Requested-With", "XMLHttpRequest");
	//xmlhttp.setRequestHeader('If-Modified-Since', 'Wed, 15 Nov 1970 00:00:00 GMT');
	xmlhttp.send(null);
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4) {
			if (xmlhttp.status == 200 && xmlhttp.responseText) {
				//alert(xmlhttp.responseText);
				var jsonObj = JSON.parse(xmlhttp.responseText);
				//todo: decide on whether to wrap json in 'json' or not
				if (jsonObj.json){ 
					var json = jsonObj.json;
				} else {
					var json = jsonObj;
				}
				if (my_func) {
					my_func(json);
				} else {
					return json;
				}
				} else { //non 200 status returned
				var json = {};
				if (my_func) {
					my_func(json);
				} else {
					return json;
				}
			}
		} 
		return false;
	};
};

Dase.initAddToCart = function() {
	var tag_type_data = Dase.$('tagType');
	if (tag_type_data) {
		var tag_type = tag_type_data.innerHTML;
		//do not display 'add to cart' for user colls & slideshows
		if ('slideshow' == tag_type || 'set' == tag_type) {
			//Dase.initCart(); 
			return;
		}
	}
	var sr = Dase.$('itemSet');
	if (!sr) {
		sr = Dase.$('item');
	}
	if (!sr) return;
	var anchors = sr.getElementsByTagName('a');
	for (var i=0;i<anchors.length;i++) {
		if ('add to cart' == anchors[i].innerHTML) {
			anchors[i].onclick = function(e) {
				this.innerHTML = '(remove)';
				Dase.removeClass(this.parentNode.getElementsByTagName('span')[0],'hide');
				item_unique = this.href;
				Dase.ajax(Dase.base_href + 'user/' + Dase.user.eid + "/cart",'POST',
				function(resp) { 
					//alert(resp);
					Dase.initUser(); 
					Dase.initSaveTo();
				},item_unique);
				return false;
			};
			Dase.removeClass(anchors[i],'hide');
		}
	}
};

Dase.initCart = function() {
	Dase.loadingMsg(true);
	var cartCount = Dase.$('cartCount');
	if (cartCount) {
		/*
		 this messed up style in IE7, see below for DOM method
		cartCount.innerHTML = Dase.user.cart_count;
		*/
	   Dase.removeChildren(cartCount);
	   cartCount.appendChild(document.createTextNode(Dase.user.cart_count));
	}
	var sr = Dase.$('itemSet');
	if (!sr) {
		sr = Dase.$('item');
	}
	if (!sr) return;
	Dase.getJSON(Dase.base_href + 'user/' + Dase.user.eid + "/cart",
	function(json) { 
		for (var i=0;i<json.length;i++) {
			var in_cart = Dase.$('addToCart_'+ json[i].item_unique);
			if  (in_cart) {
				//by default all search result thumbnails have an 'add to cart' link
				//with id = addToCart_{item_unique} when this initCart function runs,
				//items currently in cart have link changed to '(remove)', the
				//'in cart' label is unhidden, and the link id is set to removeFromCart_{tag_item_id}
				//and the href is created that, sent with 'delete' http method, will
				//delete item from user's cart
				in_cart.innerHTML = '(remove)';
				in_cart.id = 'removeFromCart_'+json[i].tag_item_id;
				in_cart.href=Dase.base_href + 'user/' + Dase.user.eid + '/tag_items/' + json[i].tag_item_id;
				Dase.removeClass(in_cart.parentNode.getElementsByTagName('span')[0],'hide');
				Dase.addClass(in_cart,'inCart');
				in_cart.item_unique = json[i].item_unique;
				in_cart.onclick = function() {
					//first, optimistically assume delete will work
					//and reset this link to be an 'add to cart' link
					this.innerHTML = 'add to cart';
					this.id = 'addToCart_' + this.item_unique;
					var delete_url = this.href;
					this.href = this.item_unique;
					Dase.addClass(this.parentNode.getElementsByTagName('span')[0],'hide');
					Dase.ajax(delete_url,'DELETE',function(resp) {
						Dase.initUser(); 
						Dase.initSaveTo();
					});
					return false;
				};
			}
		}
	});
};

/* Looks for any link w/ class 'toggle'.  That link should have
 * an id that begins 'toggle_' and the remaining string is the
 * id of the element-to-be-toggled.
 * 
 */

Dase.initToggle = function() {
	var links = document.getElementsByTagName('a');
	for (var i=0;i<links.length;i++) {
		if (Dase.hasClass(links[i],'toggle')) {
			var toggle = links[i];
			toggle.onclick = function() {
				var target = this.id.substr(7);
				Dase.toggle(Dase.$(target));
				return false;
			}
		}
	}
};

Dase.initSaveTo = function() {
	var form = Dase.$('saveToForm');
	if (!form) return;
	var itemSet = Dase.$('itemSet');
	if (!itemSet) return;
	form.onsubmit = function() {
		var saveToSelect = Dase.$('saveToSelect');
		var tag_ascii_id = saveToSelect.options[saveToSelect.options.selectedIndex].value;
		var item_uniques_array = [];
		var inputs = itemSet.getElementsByTagName('input');
		for (var i=0;i<inputs.length;i++) {
			if ('item_unique[]' == inputs[i].name && true == inputs[i].checked) {
				//item_uniques_array[item_uniques_array.length] = encodeURIComponent(inputs[i].value);
				item_uniques_array[item_uniques_array.length] = inputs[i].value;
				inputs[i].checked = false;
			}
		}
		if (!item_uniques_array.length) {
			alert('Please check at least one item.');
			return false;
		}
		if (!tag_ascii_id) {
			alert('Please select a user collection/slideshow/cart to save items to.');
			return false;
		}
		Dase.ajax(Dase.base_href + 'tag/' + Dase.user.eid + "/"+tag_ascii_id,'POST',
		function(resp) { 
			alert(resp); 
			Dase.initUser();
			Dase.initSaveTo();
		},item_uniques_array.join(','));
		return false;
	};
};

Dase.initRemoveItems = function() {
	var tag_name_el = Dase.$('tagName');
	if (tag_name_el) {
		tag_name = tag_name_el.innerHTML;
	}
	var tag_ascii_id_el = Dase.$('tagAsciiId');
	if (tag_ascii_id_el) {
		tag_ascii_id = tag_ascii_id_el.innerHTML;
	}
	var remove_form = Dase.$('removeFromForm');
	var button = Dase.$('removeFromSet');
	if (!button) return;
	var itemSet = Dase.$('itemSet');
	if (!itemSet) return;
	var items = itemSet.getElementsByTagName('input');
	//place the button on the page
	/*
	if (items.length > 3) {
		units = Dase.$('content').clientWidth - Dase.$('itemSet').clientWidth - 45;
		button.style.marginRight =  units+'px';
	}
	*/
	button.onclick = function() {
		var item_uniques_array = [];
		var inputs = itemSet.getElementsByTagName('input');
		if (!inputs.length) return false;
		for (var i=0;i<inputs.length;i++) {
			if ('item_unique[]' == inputs[i].name && true == inputs[i].checked) {
				item_uniques_array[item_uniques_array.length] = encodeURIComponent(inputs[i].value);
			}
		}
		if (!item_uniques_array.length) {
			alert('Please check at least one item.');
			return false;
		}
		if (confirm('Remove '+item_uniques_array.length+' item(s) from '+tag_ascii_id+'?')) {
			var item_uniques = item_uniques_array.join(',');
			var url = Dase.base_href + 'tag/'+Dase.user.eid+'/'+tag_ascii_id+'/items?uniques='+item_uniques;
			Dase.ajax(url,'DELETE',function(resp) {
				alert(resp);
				remove_form.submit();
			},null,null,null,null, function(resp) {
				alert('ERROR: '+resp);
			});
		}
		return false;
	};
};

Dase.getFeedUrl = function() {
	var links = document.getElementsByTagName('link');
	for (var i=0;i<links.length;i++) {
		if (links[i].type == 'application/atom+xml') {
			return links[i].href;
		}
	}
	return false;
}

Dase.getJsonUrl = function() {
	var links = document.getElementsByTagName('link');
	for (var i=0;i<links.length;i++) {
		if (links[i].type == 'application/json') {
			return links[i].href;
		}
	}
	return false;
}

/* generic, declarative form submission confirmation */
Dase.initSubmitConfirm = function() {
	elems = document.getElementsByName('submit_confirm');
	for (var i=0;i<elems.length;i++) {
		elems[i].parentNode.onsubmit = function() {
			return confirm(this.submit_confirm.value);
		}
	}
}

Dase.addLoadEvent(function() {
	Dase.initUser();
	Dase.initMenu('menu');
	Dase.multicheckItems();
	Dase.initToggle();
	Dase.initSaveTo();
	Dase.initRemoveItems();
	Dase.initSubmitConfirm();
	Dase.initLogoff();
	if (Dase.pageInit && typeof Dase.pageInit === 'function') {
		Dase.pageInit();
	}
});
