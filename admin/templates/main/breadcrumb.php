<?php

$arBreadcrumb = $BREADCRUMB -> Get();

$htmlBreadcrumb = '';

if ( !empty( $arBreadcrumb ) ) {

    $htmlBreadcrumb .= '<nav class="breadcrumb"><ul class="breadcrumb__ul" itemscope itemtype="https://schema.org/BreadcrumbList">';

    foreach ( $arBreadcrumb as $key => $arItem ) {
        $htmlBreadcrumb .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . ( !empty( $arItem['url'] && count($arBreadcrumb ) != ( $key + 1 ) ) ? '<a itemprop="item" class="_underline" href="' . $arItem['url'] . '"><span itemprop="name">' . $arItem['title'] . '</span></a>' : '<span itemprop="name">' . $arItem['title'] . '</span>' ) . '<meta itemprop="position" content="' . ( $key + 1 ) . '" /></li>';
    }

    $htmlBreadcrumb .= '</ul></nav>';

}

$BUFFER -> SetBuffer( 'breadcrumb', $htmlBreadcrumb );