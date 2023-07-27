<?php

$arBreadcrumb = $BREADCRUMB -> Get();

$htmlBreadcrumb = '';

if ( !empty( $arBreadcrumb ) ) {

    $htmlBreadcrumb .= '<nav class="breadcrumb"><ul>';

    foreach ( $arBreadcrumb as $key => $arItem ) {
        $htmlBreadcrumb .= '<li>' . ( !empty( $arItem['url'] && count($arBreadcrumb ) != ( $key + 1 ) ) ? '<a class="_underline" href="' . $arItem['url'] . '">' . $arItem['title'] . '</a>' : $arItem['title'] ) . '</li>';
    }

    $htmlBreadcrumb .= '</ul></nav>';

}

$BUFFER -> SetBuffer( 'breadcrumb', $htmlBreadcrumb );