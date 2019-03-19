<?php
$rest = new Rest;
$session = new Sessions;
$dnt 	= new Dnt;
$db 	= new Db;


if($rest->get("post_id") && $rest->get("action") == "edit")
{
	include "edit.php";
}
elseif($rest->get("post_id") && $rest->get("action") == "edit_action")
{
	//default article view action add
	include "update.php";
}
elseif($rest->get("action") == "add")
{
	//default article view action add
	include "add.php";
}
elseif($rest->get("action") == "del")
{
	//default article view action add
	include "del.php";
}
elseif($rest->get("action") == "show_hide")
{
	//default article view action add
	include "show_hide.php";
}
elseif($rest->get("action") == "move_up")
{
	//default article view action add
	include "move_up.php";
}
elseif($rest->get("action") == "move_down")
{
	//default article view action add
	include "move_down.php";
}
elseif($rest->get("action") == "add_cat")
{
	//add category
	include "add_cat.php";
}
elseif($rest->get("action") == "edit_cat")
{
	//add category
	include "edit_cat.php";
}
else{
	if($rest->get("services")){
		include "modules/services/webhook.php";
	}else{
		//default article list template
		include "tpl.php";
	}
}