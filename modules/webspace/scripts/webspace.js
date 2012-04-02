Dase.webspace = {};

Dase.webspace.multicheck = function(c) { 
	var list = Dase.$('fileList');
	if (!list) { return; }
	target = Dase.$('checkall');
	if (!target) { return; }
	//class of the link determines its behaviour
	target.className = 'uncheck';
	var boxes = list.getElementsByTagName('input');
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

Dase.webspace.postUri = function(payload_url,img,a,span,coll,htuser,htpasswd) {
	var content_headers = {
		'Content-Type':'text/uri-list'
	}
	url = Dase.base_href+'collection/'+coll+'/ingester?auth=cookie';
	Dase.ajax(url,'POST',function(resp) {
		Dase.addClass(img,'hide');
		if ('http' == resp.substr(0,4)) {
			a.href = resp;
			a.className = 'uploaded';
			span.innerHTML = 'uploaded '+span.innerHTML; 
		} else {
			alert(resp);
		}
	},payload_url,htuser,htpasswd,content_headers,function(resp) {
		Dase.addClass(img,'hide');
		span.innerHTML = 'sorry, upload did not succeed ('+resp+')';
	});
}

Dase.webspace.initForm = function() {
	var eid = Dase.getEid();
	var form = Dase.$('ingester');
	if (!form) return;
	form.onsubmit = function() {
		var htuser = Dase.user.eid;
		//todo: how does it get htpasswd?
		var htpasswd = Dase.user.htpasswd;
		coll = Dase.$('collectionAsciiId').innerHTML;
		//Dase.addClass(Dase.$('checker'),'hide');
		//Dase.addClass(Dase.$('submitButton'),'hide');
		var list = Dase.$('fileList');
		var files = list.getElementsByTagName('a');
		for (var i=0;i<files.length;i++) {
			var span = files[i].parentNode.getElementsByTagName('span')[0];
			var inp = files[i].parentNode.getElementsByTagName('input')[0];
			if (true == inp.checked && htuser && htpasswd) {
				Dase.addClass(inp,'hide');
				inp.checked = false;
				files[i].className = 'pending';
				var img = files[i].parentNode.getElementsByTagName('img')[0];
				Dase.removeClass(img,'hide');
				var payload_url = files[i].href;
				Dase.webspace.postUri(payload_url,img,files[i],span,coll,htuser,htpasswd);
			}
		}
		return false;
	}
};

Dase.pageInit = function() {
	Dase.webspace.multicheck('checked');
	Dase.webspace.initForm();
};

