<?php

global $MAIN;

//$Functions -> Pre( $_GET );

if ( !empty( $_GET['section_code'] ) ) {

    $rsSection = $mysql -> queryList('
        SELECT `id`, `name`
        FROM `sections`
        WHERE `iblock_id` = ?i AND `code` = ?s AND `parent_id` = 0
    ',
        29,
        $_GET['section_code']
    );

    if ( empty( $rsSection ) ) $MAIN::Show404();

}

if ( !empty( $_GET['section_parent'] ) && !empty( $_GET['section_code'] ) ) {

    if ( !$mysql -> query( 'SELECT `id` FROM `i_catalog` WHERE `code` = ?s', $_GET['section_parent'] ) -> num_rows ) {
        $rsSection = $mysql -> queryList('
            SELECT `id`, `name`
            FROM `sections`
            WHERE `iblock_id` = ?i AND `code` = ?s AND `parent_id` != 0
        ',
            29,
            $_GET['section_parent']
        );
    }

    if ( empty( $rsSection ) && !$mysql -> query( 'SELECT `id` FROM `i_tag_page` WHERE `url_chpu` = ?s', ( ( str_contains( $_SERVER['REQUEST_URI'], '?' ) ) ? strstr( $_SERVER['REQUEST_URI'], '?', true ) : $_SERVER['REQUEST_URI'] ) ) -> num_rows ) {
        $MAIN::Show404();
    }


}