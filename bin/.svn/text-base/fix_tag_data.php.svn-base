<?php

include 'config.php';

$tags = new Dase_DBO_Tag;

foreach ($tags->find() as $tag) {
	$tag->updated = date(DATE_ATOM,strtotime($tag->updated));
	$tag->created = date(DATE_ATOM,strtotime($tag->created));
	if (!$tag->eid) {
		$u = new Dase_DBO_DaseUser;
		$u->load($tag->dase_user_id);
		$tag->eid = $u->eid;
	}
	$tag->eid = strtolower($tag->eid);
	if (!$tag->is_public) {
		$tag->is_public = 0;
	}
	print "updating $tag->ascii_id\n";
	//$tag->update();
}

