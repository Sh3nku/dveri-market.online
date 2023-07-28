<?php

global $MAIN;
global $product_code;

//$Functions -> Pre( $_GET );

if ( !empty( $product_code ) ) {
    $rsProduct = $mysql -> queryList('
        SELECT `name`
        FROM `i_catalog`
        WHERE `code` = ?s
    ',
        $product_code
    );

    if ( empty( $rsProduct ) ) $MAIN::Show404();
}

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

    if ( empty( $rsSection ) ) $MAIN::Show404();

}