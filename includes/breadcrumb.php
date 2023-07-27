<?php

$BREADCRUMB -> Add(
    array(
        'title' => 'Главная',
        'url' => '/'
    )
);

$arSections = $mysql -> queryList('
    SELECT
        t1.`id` sect_1_id, t1.`url` sect_1_url, t1.`h1` sect_1_h1,
        t4.`id` sect_2_id, t4.`url` sect_2_url, t4.`h1` sect_2_h1,
        t6.`id` sect_3_id, t6.`url` sect_3_url, t6.`h1` sect_3_h1
    FROM `pages` t1
    LEFT JOIN `pages_in_menu` t2 ON t2.`page` = t1.`id`
    LEFT JOIN `pages_in_menu` t3 ON t3.`id` = t2.`parent`
    LEFT JOIN `pages` t4 ON t4.`id` = t3.`page`
    LEFT JOIN `pages_in_menu` t5 ON t5.`id` = t3.`parent`
    LEFT JOIN `pages` t6 ON t6.`id` = t5.`page`
    WHERE t1.`url` = ?s
',
    preg_replace( '/index.php/', '', $_SERVER['PHP_SELF'] )
);

$arPages = array();

if ( !empty( $arSections ) ) {

    foreach ( $arSections[0] as $key => $value ) {
        if ( empty( $value ) ) continue;
        preg_match( '/(sect_\d)_(.*)/', $key, $key_menu );
        $arPages[$key_menu[1]][$key_menu[2]] = $value;
    }

    $arPages = array_values( array_reverse( $arPages ) );

}

if ( !empty( $arPages ) ) {
    foreach ( $arPages as $arPage ) {
        $BREADCRUMB -> Add(
            array(
                'title' => $arPage['h1'],
                'url' => $arPage['url']
            )
        );
    }
}