<?php
require_once 'init.php';
$theme  = $ini['general']['theme'];
$class  = isset($_REQUEST['class']) ? $_REQUEST['class'] : '';
$public = in_array($class, $ini['permission']['public_classes']);
new TSession;

if ( TSession::getValue('logged') )
{
    $content     = file_get_contents("app/templates/{$theme}/layout.html");
    $menu_string = SideMenuCreate::createUserMenu( $theme );
    $content     = str_replace('{MENU}', $menu_string, $content);
    $content  = str_replace('{frontpage}', TSession::getValue('frontpage'), $content);
    $content  = str_replace('{username}', TSession::getValue('username'), $content);
    $content  = str_replace('{userimage}', "app/templates/{$theme}/images/people.svg", $content);
    $content  = str_replace('{matricula}', '2017592342', $content);

    if ((TSession::getValue('login') == 'admin') && isset($ini['general']['token']))
    {
        $content = str_replace('{IF-BUILDER}', '', $content);
        $content = str_replace('{/IF-BUILDER}','', $content);
    }
}
else
{
    $content = file_get_contents("app/templates/{$theme}/login.html");
}

$content = str_replace('{IF-BUILDER}', '<!--', $content);
$content = str_replace('{/IF-BUILDER}', '-->', $content);
$content  = ApplicationTranslator::translateTemplate($content);
$content  = str_replace('{LIBRARIES}', file_get_contents("app/templates/{$theme}/libraries.html"), $content);
$content  = str_replace('{class}', $class, $content);
$content  = str_replace('{template}', $theme, $content);
$content  = str_replace('{query_string}', $_SERVER["QUERY_STRING"], $content);
$content  = str_replace('{APP_NAME}', "Kiron Saúde", $content);
$css      = TPage::getLoadedCSS();
$js       = TPage::getLoadedJS();
$content  = str_replace('{HEAD}', $css.$js, $content);

echo $content;

if (TSession::getValue('logged') OR $public)
{
    if ($class)
    {
        $method = isset($_REQUEST['method']) ? $_REQUEST['method'] : NULL;
        AdiantiCoreApplication::loadPage($class, $method, $_REQUEST);
    }
}
else
{
    AdiantiCoreApplication::loadPage('LoginForm', '', $_REQUEST);
}
