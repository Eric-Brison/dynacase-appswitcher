<?php
/*
 * @author Anakeen
 * @package FDL
*/

$app_desc = array(
    "name" => "APP_SWITCHER",
    "short_name" => N_("APP_SWITCHER:APP_SWITCHER"),
    "description" => N_("APP_SWITCHER:Default app switcher for DCP"),
    "icon" => "app_switcher.png",
    "displayable" => "N",
    "with_frame" => "N",
    "childof" => "",
    "tag" => "SYSTEM"
);
/* ACLs for this application */
$app_acl = array(
    array(
        "name" => "APP_SWITCHER_USER",
        "description" => N_("APP_SWITCHER:APP_SWITCHER access"),
        "group_default" => "Y"
    )
);
/* Actions for this application */
$action_desc = array(
    array(
        "name" => "APP_SWITCHER",
        "short_name" => N_("APP_SWITCHER:Default app switcher for DCP"),
        "script" => "app_switcher.php",
        "function" => "app_switcher",
        "layout" => "app_switcher.html",
        "acl" => "APP_SWITCHER_USER",
        "root" => "Y"
    ),
    array(
        "name" => "SET_DEFAULT_APPLICATION",
        "short_name" => N_("APP_SWITCHER:SETDEFAULTAPPLICATION"),
        "script" => "set_default_application.php",
        "function" => "set_default_application",
        "acl" => "APP_SWITCHER_USER"
    ),
    array(
        "name" => "SHORTCUT_APPLICATION",
        "short_name" => N_("APP_SWITCHER:SHORTCUT_APPLICATION"),
        "acl" => "APP_SWITCHER_USER"
    )
);
