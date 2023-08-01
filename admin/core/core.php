<?session_start();

require $_SERVER['DOCUMENT_ROOT'] . '/admin/core/config.php';

$LastModified_unix = strtotime(date("D, d M Y H:i:s", filectime($_SERVER['SCRIPT_FILENAME'])));
$LastModified = gmdate("D, d M Y H:i:s \G\M\T", $LastModified_unix);
$IfModifiedSince = false;

if ( isset( $_ENV['HTTP_IF_MODIFIED_SINCE'] ) ) $IfModifiedSince = strtotime( substr ( $_ENV['HTTP_IF_MODIFIED_SINCE'], 5 ) );

if ( isset( $_SERVER['HTTP_IF_MODIFIED_SINCE'] ) ) $IfModifiedSince = strtotime( substr ( $_SERVER['HTTP_IF_MODIFIED_SINCE'], 5 ) );

if ( $IfModifiedSince && $IfModifiedSince >= $LastModified_unix ) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified');
    exit;
}

header('Last-Modified: '. $LastModified);

if ( !function_exists( 'AutoLoader' ) ) {

    function AutoLoader ( string $className ) {
        require_once __DIR__ . '/classes/' . str_replace('\\', '/', $className) . '.php';
    }

    spl_autoload_register( 'AutoLoader' );

}

$MAIN = new \Main\Main();
$BREADCRUMB = new \Main\BreadCrumb();
$ASSET = new \Includes\Asset();
$BUFFER = new \Buffer\Buffer();

if ( !function_exists( 'myAutoload' ) ) {

    function myAutoload ( $className ) {

        $split = explode( '\\', $className );

        if ( file_exists( $_SERVER['DOCUMENT_ROOT'] . '/admin/core/classes/' . $split[0] . '.php' ) ) require $_SERVER['DOCUMENT_ROOT'] . '/admin/core/classes/' . $split[0] . '.php';

    }

    spl_autoload_register( 'myAutoload' );

}

$mysql = new Mysql(
    array(
        'host' => $DB_HOST,
        'user' => $DB_USER,
        'pass' => $DB_PASS,
        'db' => $DB_NAME
    )
);

$Content = new Content();
$Functions = new Functions();
$Includes = new Includes();
$Page = new Pages();
$User = new User();

$arResPage = $mysql -> queryList( 'SELECT `h1`, `title`, `description`, `keywords`, `robots`, `text` FROM `pages` WHERE `url` = ?s LIMIT 1', preg_replace( '/index.php/', '', $_SERVER['PHP_SELF'] ) );

$PAGE_H1 = ( ( !empty( $arResPage[0]['h1'] ) ) ? $arResPage[0]['h1'] : '' );
$PAGE_TITLE = ( ( !empty( $arResPage[0]['title'] ) ) ? $arResPage[0]['title'] : '' );
$PAGE_DESCRIPTION = ( ( !empty( $arResPage[0]['description'] ) ) ? $arResPage[0]['description'] : '' );
$PAGE_KEYWORDS = ( ( !empty( $arResPage[0]['keywords'] ) ) ? $arResPage[0]['keywords'] : '' );
$PAGE_ROBOTS = ( ( !empty( $arResPage[0]['robots'] ) ) ? $arResPage[0]['robots'] : '' );
$PAGE_TEXT = ( ( !empty( $arResPage[0]['text'] ) ) ? $arResPage[0]['text'] : '' );

$MAIN -> SetTitle( $PAGE_TITLE );
$BUFFER -> SetBuffer( 'description', $PAGE_DESCRIPTION );
$BUFFER -> SetBuffer( 'keywords', $PAGE_KEYWORDS );

$arResSetting = $Content -> GetList(
    array(),
    array(
        'iblock_id' => 8
    )
);

$arSetting = $arResSetting['items'][0];

define( 'DEFAULT_NAME', $arSetting['name'] );
define( 'DEFAULT_EMAIL', $arSetting['email'] );
$protocol = ( !empty( $_SERVER['HTTPS'] ) && 'off' !== strtolower( $_SERVER['HTTPS'] ) ? 'https://': 'http://' );
define( 'DOMAIN', $protocol . $_SERVER['HTTP_HOST'] );

// --- Шаблоны сайта ---

$arResResult = $mysql -> query('
    SELECT
        `id`, `active`, `sort`, `date_create`, `date_update`, `code`, `name`, `condition_type`, `condition`, `for_user`
    FROM `site_templates`
    ORDER BY `sort` asc
');

$arResSiteTemplates = array();

while ( $row = mysqli_fetch_assoc( $arResResult ) ) {

    $arResSiteTemplates[$row['id']]['id'] = $row['id'];
    $arResSiteTemplates[$row['id']]['active'] = $row['active'];
    $arResSiteTemplates[$row['id']]['sort'] = $row['sort'];
    $arResSiteTemplates[$row['id']]['date_create'] = $row['date_create'];
    $arResSiteTemplates[$row['id']]['date_update'] = $row['date_update'];
    $arResSiteTemplates[$row['id']]['code'] = $row['code'];
    $arResSiteTemplates[$row['id']]['name'] = $row['name'];
    $arResSiteTemplates[$row['id']]['condition_type'] = $row['condition_type'];
    $arResSiteTemplates[$row['id']]['condition'] = $row['condition'];
    $arResSiteTemplates[$row['id']]['for_user'] = ( ( !empty( $row['for_user'] ) ) ? explode( '||', $row['for_user'] ) : '' );

}

$arResSiteTemplates = $Functions -> ResetKeys( $arResSiteTemplates );

$tpl = '';

//$Functions -> Pre( $arResSiteTemplates );

if ( !empty( $arResSiteTemplates ) ) {

    foreach ( $arResSiteTemplates as $arTemplate ) {

        $authorized = array();

        for ( $i = 0; $i < count( $arTemplate['for_user'] ); $i++ ) {
            $authorized[] = $arTemplate['for_user'][$i];
        }

        if (
            preg_match( "/" . preg_quote( $arTemplate['condition'], '/' ) . "/si", $_SERVER['REQUEST_URI'] )
            && ( ( $User -> IsAuthorized() && in_array( 'auth', $authorized ) ) || ( !$User -> IsAuthorized() && in_array( 'non_auth', $authorized ) ) )
        ) {

            $is_auth = false;
            if ( !$User -> IsAuthorized() && in_array( 'non_auth', $authorized ) ) $is_auth = true;

            $tpl = $arTemplate['code'];

            break;

        }

    }

}

if ( is_file( $_SERVER['DOCUMENT_ROOT'] . $_SERVER['PHP_SELF'] ) ) {

    $url = preg_replace( '/' . basename($_SERVER['PHP_SELF']) . '/', '', $_SERVER['PHP_SELF'] );

}

$WS_Access = $Page -> CheckAccess(
    array(
        'url' => $url,
        'user_id' => $_SESSION['user']['id']
    )
);

//$Functions -> Pre( $is_auth );
//$Functions -> Pre( $WS_Access );

if (
    ( count( $WS_Access ) == 1 && $WS_Access['close'] == 'Y' )
    || ( empty( $WS_Access ) && !$is_auth )
) {
    die( 'Доступ закрыт' );
}

define( 'TPL', '/admin/templates/' . $tpl );

if ( file_exists( $_SERVER['DOCUMENT_ROOT'] . str_replace( '/' . basename( $_SERVER['PHP_SELF'] ), '', $_SERVER['PHP_SELF'] ) . '/rules.php' ) ) {

    include_once $_SERVER['DOCUMENT_ROOT'] . str_replace( '/' . basename( $_SERVER['PHP_SELF'] ), '', $_SERVER['PHP_SELF'] ) . '/rules.php';

}

// --- Шаблоны сайта ---