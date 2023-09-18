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

            $MAIN -> SetTitle( $arSeo['title'] ?? $arResSection[0]['name'] );
            $BUFFER -> SetBuffer( 'description', $arSeo['description'] );
            $BUFFER -> SetBuffer( 'H1', $arSeo['page_title'] ?? $arResSection[0]['name'] );

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

        $arResSection = $mysql -> queryList( 'SELECT `name` FROM `sections` WHERE `iblock_id` = ?i AND `code` = ?s', 27, $_GET['section_code'] );

        if ( $arResSection ) {

            $MAIN -> SetTitle( $arResSection[0]['name'] );
            $BUFFER -> SetBuffer( 'H1', $arResSection[0]['name'] );

            $arBreadCrumbs[] = array(
                'name' => $arResSection[0]['name'],
                'path' => '/furnitura/' . $_GET['section_code'] . '/'
            );

        }

    }

    if ( !empty( $_GET['product_code'] ) ) {

        $arResProduct = $mysql -> queryList( 'SELECT `name` FROM `i_furnitura` WHERE `code` = ?s', $_GET['product_code'] );

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