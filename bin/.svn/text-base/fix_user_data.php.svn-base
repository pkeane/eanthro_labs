<?php

include 'config.php';

$users = new Dase_DBO_DaseUser;

$update = false;

foreach ($users->find() as $user) {
	$user->updated = date(DATE_ATOM,strtotime($user->updated));
	$user->created = date(DATE_ATOM,strtotime($user->created));
	if ($user->eid != strtolower($user->eid)) {
		$user->eid = strtolower($user->eid);
	}
	if (!$user->has_access_exception) {
		$user->has_access_exception = 0;
	}
	print "updating user $user->eid\n";
	if ($update) {
		$user->update();
	}
}

$cms = new Dase_DBO_CollectionManager;
foreach ($cms->find() as $cm) {
	if ($cm->dase_user_eid != strtolower($cm->dase_user_eid)) {
		print "updating manager $cm->dase_user_eid\n";
		$cm->dase_user_eid = strtolower($cm->dase_user_eid);
		if ($update) {
			$cm->update();
		}
	}
}

$items = new Dase_DBO_Item;
foreach ($items->find() as $item) {
	if ($item->created_by_eid != strtolower($item->created_by_eid)) {
		print "updating item $item->created_by_eid\n";
		$item->created_by_eid = strtolower($item->created_by_eid);
		if ($update) {
			$item->update();
		}
	}
}

$vrhs = new Dase_DBO_ValueRevisionHistory;
foreach ($vrhs->find() as $vrh) {
	if ($vrh->dase_user_eid != strtolower($vrh->dase_user_eid)) {
		print "updating history $vrh->dase_user_eid\n";
		$vrh->dase_user_eid = strtolower($vrh->dase_user_eid);
		if ($update) {
			$vrh->update();
		}
	}
}
