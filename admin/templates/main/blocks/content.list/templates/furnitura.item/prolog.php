<?php

global $MAIN;
global $product_code;

if ( !empty( $_GET['section_code'] ) ) {
    $rsSection = $mysql -> queryList('
        SELECT `id`, `name`
        FROM `sections`
        WHERE `iblock_id` = ?i AND `code` = ?s AND `parent_id` = 0
    ',
        33,
        $_GET['section_code']
    );

    if ( empty( $rsSection ) ) $MAIN::Show404();
}

if ( !empty( $_GET['product_code'] ) ) {
    if ( !$mysql -> query( 'SELECT `id` FROM `i_furnituraa` WHERE `code` = ?s', $_GET['product_code'] ) -> num_rows ) {
        $MAIN::Show404();
    }
}