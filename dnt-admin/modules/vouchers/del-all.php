<?php

$where = array('vendor_id' => Vendor::getId());
$db->delete('dnt_vouchers', $where);
$dnt->redirect(WWW_PATH_ADMIN_2 . "index.php?src=" . $rest->get("src") . "");
