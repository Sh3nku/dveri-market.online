<?function get404 () {

    header( 'HTTP/1.0 404 Not Found' );
    exit( include( $_SERVER['DOCUMENT_ROOT'] . '/404.php' ) );

}

$arResult = $Content -> GetList(
    array(),
    array(
        'iblock_id' => 32,
        'url_chpu' => ( ( str_contains( $_SERVER['REQUEST_URI'], '?' ) ) ? strstr( $_SERVER['REQUEST_URI'], '?', true ) : $_SERVER['REQUEST_URI'] )
    )
);

$arItem = $arResult['items'][0];

if ( !empty( $arItem ) ) {

    $section_code = '';

    if ( !empty( $_GET['product_code'] ) ) {
        $section_code = $_GET['section_parent'];
    } else if ( !empty( $_GET['section_parent'] ) ) {
        $section_code = $_GET['section_code'];
    }

    $params = explode( '?', $arItem['url'] );
    $arParams = explode( '&', $params[1] );
    $arTagParams = array();

    foreach ( $arParams as $arParam ) {
        $exp = explode( '=', $arParam );

        $key = preg_replace( '/\[\]/', '', $exp[0] );

        if ( !empty( $arTagParams[$key] ) ) {
            $arTagParams[$key] = array_merge( $arTagParams[$key], array( $exp[1] ) );
        } else {
            $arTagParams[$key] = array( $exp[1] );
        }

    }

    if ( !empty( $section_code ) ) $arTagParams['section_code'] = $section_code;

    $arTagPage = array(
        'h1' => $arItem['h1'],
        'name' => $arItem['name'],
        'title' => $arItem['title'],
        'text' => $arItem['text'],
        'description' => $arItem['description'],
        'url_chpu' => $arItem['url_chpu'],
        'filter' => $arTagParams
    );

    $is_section = true;

}

if ( empty( $arTagPage ) ) {

    if ( !empty( $_GET['section_code'] ) ) {

        $rsSection = $mysql -> queryList( 'SELECT `id`, `name` FROM `sections` WHERE `iblock_id` = ?i AND `code` = ?s AND `parent_id` = 0', 29, $_GET['section_code'] );

        if ( empty( $rsSection ) ) get404();

    }

    $is_section = false;
    $product_code = '';

    if ( !empty( $_GET['section_parent'] ) && !empty( $_GET['section_code'] ) && !empty( $_GET['product_code'] ) ) {

        $product_code = $_GET['product_code'];

        $rsSection = $mysql -> queryList( 'SELECT `id`, `name` FROM `sections` WHERE `iblock_id` = ?i AND `code` = ?s AND `parent_id` != 0', 29, $_GET['section_parent'] );
        if ( empty( $rsSection ) ) get404();

        $rsProduct = $mysql -> queryList( 'SELECT `name` FROM `i_catalog` WHERE `code` = ?s', $product_code );
        if ( empty( $rsProduct ) ) get404();

    } else if ( !empty( $_GET['section_parent'] ) && !empty( $_GET['section_code'] ) ) {

        if ( $mysql -> query( 'SELECT `id` FROM `i_catalog` WHERE `code` = ?s', $_GET['section_parent'] ) -> num_rows ) {
            $product_code = $_GET['section_parent'];
        } else {

            $rsSection = $mysql -> queryList( 'SELECT `id`, `name` FROM `sections` WHERE `iblock_id` = ?i AND `code` = ?s AND `parent_id` != 0', 29, $_GET['section_parent'] );
            if ( empty( $rsSection ) ) get404();

            $section_code = $_GET['section_parent'];
            $is_section = true;

        }

    } else if ( !empty( $_GET['section_code'] ) ) {

        $is_section = true;
        $section_code = $_GET['section_code'];

    }

}