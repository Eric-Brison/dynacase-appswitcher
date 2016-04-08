<?php
/*
 * @author Anakeen
 * @package FDL
*/

include_once "FDL/freedom_util.php";
/**
 * Generate basic app switcher
 *
 * @param Action $action
 *
 */
function app_switcher(Action & $action)
{
    $action->parent->addCssRef("css/dcp/jquery-ui.css");
    $action->parent->addCssRef("APP_SWITCHER:app_switcher.css");
    
    $user = new_Doc('', $action->user->fid);
    $action->lay->eSet("NAME", $user->getTitle());
    /** For authent mecanism */
    $action->lay->eSet("PHP_AUTH_USER", $_SERVER['PHP_AUTH_USER']);
    /**
     * Add widget code
     */
    $action->lay->set("WIDGET_PASSWORD", $action->parent->getJsLink("CORE:dcpui.passwordModifier.js.xml", true));
    /**
     * Test user have admin access rights
     */
    $action->lay->set('DISPLAY_ADMIN_ACCESS_BUTTON', (file_exists('admin.php') && $action->canExecute("CORE_ADMIN_ROOT", "CORE_ADMIN") === ''));
    /**
     * Test if can change password
     */
    $action->lay->set('DISPLAY_CHANGE_BUTTON', ("" === $user->canEdit()));
    
    $displayableApplication = getDisplayableApplication($action);
    
    if (isset($displayableApplication["FGSEARCH"])) {
        $action->lay->set("DISPLAY_SEARCH_ZONE", true);
    } else {
        $action->lay->set("DISPLAY_SEARCH_ZONE", false);
    }
    
    $action->lay->setBlockData('MENU_APPLICATIONS', $displayableApplication);
}
/**
 * Check if an application need to be display
 *
 * @param Action $action current action
 *
 * @return array
 */
function getDisplayableApplication(Action $action)
{
    $applications = array();
    $query = <<< 'SQL'
SELECT
    application.name,
    application.id,
    application.icon,
    application.short_name,
    application.description,
    application.with_frame,
    action.acl
FROM application
LEFT JOIN action
ON application.id = action.id_application
WHERE
    (application.tag is null
    OR application.tag !~* E'\\yadmin\\y' )
    AND application.displayable='Y'
    AND application.available = 'Y'
    AND application.name != 'APP_SWITCHER'
    AND action.root = 'Y';
SQL;
    
    simpleQuery('', $query, $applications, false, false, true);
    
    $displayableApplications = array();
    
    foreach ($applications as $currentApplication) {
        if ($action->user->id != 1) { // no control for user Admin
            if (!$action->hasPermission($currentApplication["acl"], $currentApplication["id"])) {
                continue;
            }
        }
        $appUrl = "?app=" . $currentApplication["name"];
        if ($currentApplication["with_frame"] !== 'Y') {
            $appUrl.= "&sole=A";
        }
        $displayableApplications[$currentApplication["name"]] = array(
            "NAME" => $currentApplication["name"],
            "URL" => $appUrl,
            "ICON_SRC" => $action->parent->getImageLink($currentApplication["icon"], false, 20) ,
            "ICON_ALT" => $currentApplication["name"],
            "TITLE" => _($currentApplication["short_name"]) ,
            "DESCRIPTION" => _($currentApplication["description"])
        );
    }
    
    $collator = new Collator($action->GetParam('CORE_LANG', 'fr_FR'));
    
    uasort($displayableApplications, function ($app1, $app2) use ($collator)
    {
        /** @var Collator $collator */
        return $collator->compare($app1["TITLE"], $app2["TITLE"]);
    });
    
    return $displayableApplications;
}

