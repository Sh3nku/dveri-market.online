<?php

global $MAIN;

//$Functions -> Pre( $_GET );

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