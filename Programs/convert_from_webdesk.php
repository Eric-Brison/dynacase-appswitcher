#!/usr/bin/env php
<?php
/*
 * @author Anakeen
 * @package FDL
*/

$WIFF_ROOT = getenv("WIFF_ROOT");
if ($WIFF_ROOT === false) {
    print "WIFF_ROOT environment variable is not set!\n";
    exit(1);
}

$WIFF_CONTEXT_ROOT = getenv("WIFF_CONTEXT_ROOT");
if ($WIFF_CONTEXT_ROOT === false) {
    print "WIFF_CONTEXT_ROOT environment variable not set!\n";
    exit(1);
}

set_include_path(get_include_path() . PATH_SEPARATOR . $WIFF_CONTEXT_ROOT . PATH_SEPARATOR . "$WIFF_ROOT/include");

$prefix = $WIFF_CONTEXT_ROOT . "/WHAT/Lib.Prefix.php";
require_once $prefix;


include_once 'lib/Lib.Cli.php';
include_once 'WHAT/Lib.Common.php';
include_once 'WHAT/Class.User.php';


$changeParameter = <<< 'SQL'
insert into paramv(name, type, appid, val)
(select
    '%s' as name,
    paramv.type,
    (select id from application where application.name='APP_SWITCHER') as appid,
    val
from paramv
where
    paramv.name='%s'
    and paramv.type ~* 'U'
    and paramv.appid=(select id from application where application.name='WEBDESK')
);
SQL;

$err = "";

simpleQuery("", "begin;");

$err .= simpleQuery("", sprintf($changeParameter, "DEFAULT_APPLICATION_NAME", "WDK_DEFAPP"));
$err .= simpleQuery("", sprintf($changeParameter, "SHORTCUT_APPLICATION", "WDK_BARAPP"));

if ($err) {
    simpleQuery("", "rollback;");
    print $err."\n";
    exit(1);
} else {
    simpleQuery("", "commit;");

    $defaultApplication = ApplicationParameterManager::getUserParameterDefaultValue('WEBDESK', "WDK_DEFAPP");
    ApplicationParameterManager::setUserParameterDefaultValue("APP_SWITCHER", "DEFAULT_APPLICATION_NAME", $defaultApplication);

    $defaultShortcut = ApplicationParameterManager::getUserParameterDefaultValue('WEBDESK', "WDK_BARAPP");
    ApplicationParameterManager::setUserParameterDefaultValue("APP_SWITCHER", "SHORTCUT_APPLICATION", $defaultShortcut);
}

exit(0);
?>