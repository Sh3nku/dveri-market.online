<?

//$Functions -> Pre( $_GET );

if ( preg_match( '/katalog/', $_SERVER['REQUEST_URI'] ) ) {

    //$Functions -> Pre( $_GET );

    $parent_code = $_GET['section_parent'] ?? $_GET['section_code'];

    if ( !empty( $_GET['section_parent'] ) && !empty( $_GET['section_code'] ) ) {

        $arResSectionParent = $mysql -> queryList( 'SELECT `name` FROM `sections` WHERE `iblock_id` = ?i AND `code` = ?s', 29, $_GET['section_code'] );

        if ( $arResSectionParent ) {

            $arBreadCrumbs[] = array(
                'name' => $arResSectionParent[0]['name'],
                'path' => '/katalog/' . $_GET['section_code'] . '/'
            );

        }

    }

    if ( !empty( $parent_code ) ) {

        $arResSection = $mysql -> queryList( 'SELECT `id`, `name` FROM `sections` WHERE `iblock_id` = ?i AND `code` = ?s', 29, $parent_code );

        if ( $arResSection ) {

            $arSeo = \Iblock\Seo::GetList(
                array(
                    'filter' => array(
                        'iblock_id' => 29,
                        'type' => 'S',
                        'element_id' => $arResSection[0]['id']
                    )
                )
            )['items'][0];

            if ( !empty( $arSeo['page_title'] ) ) {
                $page_title = $arSeo['page_title'];
            } else if ( preg_match( '/mezhkomnatnye-dveri/', $_SERVER['REQUEST_URI'] ) ) {
                $page_title = $arResSection[0]['name'];
            } else if ( preg_match( '/vhodnye-dveri/', $_SERVER['REQUEST_URI'] ) ) {
                $page_title = $arResSection[0]['name'];
            } else {
                $page_title = $arResSection[0]['name'];
            }

            if ( !empty( $arSeo['title'] ) ) {
                $title = $arSeo['title'];
            } else if ( preg_match( '/mezhkomnatnye-dveri/', $_SERVER['REQUEST_URI'] ) ) {
                $title = 'Межкомнатные двери купить в Санкт-Петербурге недорого с установкой - цены от «Двери Маркет»';
            } else if ( preg_match( '/vhodnye-dveri/', $_SERVER['REQUEST_URI'] ) ) {
                $title = 'Двери входные купить в Санкт-Петербурге недорого | Интернет-магазин «Двери Маркет»';
            } else {
                $title = $arResSection[0]['name'] . ' купить в Санкт-Петербурге | Интернет-магазин «Двери-Маркет»';
            }

            if ( !empty( $arSeo['description'] ) ) {
                $description = $arSeo['description'];
            } else if ( preg_match( '/mezhkomnatnye-dveri/', $_SERVER['REQUEST_URI'] ) ) {
                $description = 'Межкомнатные двери - широкий выбор, недорого. ✏ В наличии и под заказ. ✏ Доставка и установка. ☎ +7 (981) 718-01-08';
            } else if ( preg_match( '/vhodnye-dveri/', $_SERVER['REQUEST_URI'] ) ) {
                $description = 'Двери входные с доставкой и установкой. ✏ Готовые и под заказ. ✏ Доступные цены! ☎ +7 (981) 718-01-08 Заказывайте в «Двери Маркет»! ➠';
            } else {
                $description = $arResSection[0]['name'] . ' в Санкт-Петербурге ✏ Широкий ассортимент! ✏ Доступные цены! ☎ +7 (981) 718-01-08 Покупайте в «Двери Маркет»! ➠';
            }

            $BUFFER -> SetBuffer( 'H1', $page_title );
            $MAIN -> SetTitle( $title );
            $BUFFER -> SetBuffer( 'description', $description );

            $arBreadCrumbs[] = array(
                'name' => $arResSection[0]['name'],
                'path' => '/katalog/' . ( ( !empty( $_GET['section_code'] ) ) ? $_GET['section_code'] . '/' : '' ) . $parent_code . '/'
            );

        } else {

            $arResProduct = $mysql -> queryList( 'SELECT `name` FROM `i_catalog` WHERE `code` = ?s', $parent_code );

            if ( $arResProduct ) {

                $arBreadCrumbs[] = array(
                    'name' => $arResProduct[0]['name']
                );

            }

        }

    }

    if ( !empty( $_GET['product_code'] ) ) {

        $arResProduct = $mysql -> queryList( 'SELECT `name` FROM `i_catalog` WHERE `code` = ?s', $_GET['product_code'] );

        if ( $arResProduct ) {

            //$Functions -> Pre( $arResProduct );

            $arBreadCrumbs[] = array(
                'name' => $arResProduct[0]['name']
            );

        }

    }

}

if ( preg_match( '/furnitura/', $_SERVER['REQUEST_URI'] ) ) {

    $parent_code = $_GET['section_parent'] ?? $_GET['section_code'];

    if ( !empty( $_GET['section_code'] ) ) {

        $arResSection = $mysql -> queryList( 'SELECT `id`, `name` FROM `sections` WHERE `iblock_id` = ?i AND `code` = ?s', 33, $_GET['section_code'] );

        if ( $arResSection ) {

            $arSeo = \Iblock\Seo::GetList(
                array(
                    'filter' => array(
                        'iblock_id' => 33,
                        'type' => 'S',
                        'element_id' => $arResSection[0]['id']
                    )
                )
            )['items'][0];

            if ( !empty( $arSeo['page_title'] ) ) {
                $page_title = $arSeo['page_title'];
            } else {
                $page_title = $arResSection[0]['name'];
            }

            if ( !empty( $arSeo['title'] ) ) {
                $title = $arSeo['title'];
            } else {
                $title = $arResSection[0]['name'] . ' купить в Санкт-Петербурге | Интернет-магазин «Двери-Маркет»';
            }

            if ( !empty( $arSeo['description'] ) ) {
                $description = $arSeo['description'];
            } else {
                $description = $arResSection[0]['name'] . ' в Санкт-Петербурге ✏ Широкий ассортимент! ✏ Доступные цены! ☎ +7 (981) 718-01-08 Покупайте в «Двери Маркет»! ➠';
            }

            $BUFFER -> SetBuffer( 'H1', $page_title );
            $MAIN -> SetTitle( $title );
            $BUFFER -> SetBuffer( 'description', $description );
            $BUFFER -> SetBuffer( 'keywords', '' );

            $arBreadCrumbs[] = array(
                'name' => $arResSection[0]['name'],
                'path' => '/furnitura/' . $_GET['section_code'] . '/'
            );

        }

    }

    if ( !empty( $_GET['product_code'] ) ) {

        $arResProduct = $mysql -> queryList( 'SELECT `name` FROM `i_furnituraa` WHERE `code` = ?s', $_GET['product_code'] );

        if ( $arResProduct ) {

            $arBreadCrumbs[] = array(
                'name' => $arResProduct[0]['name']
            );

        }

    }

}

if ( !empty( $arBreadCrumbs ) ) {

    foreach ( $arBreadCrumbs as $arItem ) {
        $BREADCRUMB -> Add(
            array(
                'title' => $arItem['name'],
                'url' => $arItem['path']
            )
        );
    }

}