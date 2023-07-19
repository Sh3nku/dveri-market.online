<?if ( !empty( $arResult['items'] ) ) {

    global $Includes;
    $classFiles = new Files();?>

    <div class="row">

        <?foreach ( $arResult['items'] as $key => $arItem ) {

            $arOffer = $arItem['offers'][0];

            $arSections = array();

            if ( $arItem['sections'] ) {

                for ( $ii = 0; $ii < count( $arItem['sections'] ); $ii++ ) {

                    $arSection = $arItem['sections'][$ii];

                    $arSections[$arSection['id']] = array(
                        'id' => $arSection['id'],
                        'name' => $arSection['name'],
                        'code' => $arSection['code'],
                        'parent_id' => $arSection['parent_id']
                    );

                }

            }

            $arSectionTree = $Functions -> BuildTree( $arSections );

            //$Functions -> Pre( $arSectionTree );

            $url = '/katalog/' . $arSectionTree[0]['code'] . '/' . ( ( $arSectionTree[0]['children'][0]['code'] ) ? $arSectionTree[0]['children'][0]['code'] . '/' : '' );?>

            <div class="col-xx-2 col-x-3 col-l-4 catalog-item-col">
                <div class="catalog-item">
                    <a href="<?=$url . $arItem['code']?>/" class="catalog-item-link">

                        <?if ( !empty( $arOffer['discount'] ) ) {?>

                            <div class="catalog-item-discount">-<?=$arOffer['discount'] . ( ( $arOffer['discount_type'] == 1 ) ? '<span class="rub rub-16">Р</span>' : '%' )?></div>

                        <?}

                        if ( $arOffer['available']['code'] == 'order' ) {?>

                            <div class="catalog-item-available <?=$arOffer['available']['code']?>"><?=$arOffer['available']['name']?></div>

                        <?}?>

                        <div class="catalog-item-picture<?=( ( !empty( $arItem['picture_2'] ) ) ? ' _double' : '' )?>">

                            <?$picture = $classFiles -> Resize( $arItem['picture']['path'], 184, 224, false, 'catalog_list_' )?>

                            <img src="<?=$picture?>">

                            <?if ( !empty( $arItem['picture_2'] ) ) {

                                $picture = $classFiles -> Resize( $arItem['picture_2']['path'], 184, 224, false, 'catalog_list_' )?>

                                <img src="<?=$picture?>">

                            <?}?>

                        </div>

                        <div class="catalog-item-name"><?=$arItem['name']?></div>

                        <div class="catalog-item-offer-name"><?=$arItem['model']?></div>

                        <div class="catalog-item-price"><?=number_format( $arOffer['discount_price'], 0, '', ' ' )?> <span class="rub">Р</span></div>

                        <?$size = $arOffer['size'][0]['code'];

                        if ( !empty( $arParams['filter']['offer_size'] ) ) {

                            foreach ( $arOffer['size'] as $arSize ) {
                                if ( $arSize['code'] === $arParams['filter']['offer_size'][0] ) $size = $arSize['code'];
                            }

                        }?>

                        <div class="catalog-item-button-wrapper">

                            <div class="button catalog-item-button js-in-basket" data-code="<?=$arItem['code']?>" data-offer="<?=$arOffer['id']?>" data-offer_code="<?=$arOffer['code']?>" data-sample="' .  . '" data-size="<?=$size?>">В корзину</div>

                        </div>

                    </a>
                </div>
            </div>

        <?}?>

    </div>

    <?$Includes::Template(
        'main:pagination',
        'catalog',
        array(
            'current_page' => $_GET['page'] ?? 1,
            'pages' => ceil( $arResult['items_count'] / $arParams['nav']['count_on_page'] ),
            'parameters' => $arParams
        )
    );

}

//$Functions -> Pre( $arResult );

/*if ( $arResult['items'] ) {

    $html = '<div class="row">';

        foreach ( $arResult['items'] as $key => $arItem ) {

            $arOffers = array();
            $prev_offer_id = '';
            $prev_offer_code = '';
            $next_offer_id = '';
            $next_offer_code = '';

            $i = 0;

            foreach ( $arItem['offers'] as $key_offers => $arOffer ) {

                if ( empty( $arOffer['sample'] ) ) continue;

                if ( $i == count( $arItem['offers'] ) - 1 ) {
                    $prev_offer_id = $arOffer['id'];
                    $prev_offer_code = $arOffer['code'];
                }

                if ( $i == 1 ) {
                    $next_offer_id = $arOffer['id'];
                    $next_offer_code = $arOffer['code'];
                }

                $arOffers[$arOffer['code']] = array(
                    'id' => $arOffer['id'],
                    'name' => $arOffer['name'],
                    'code' => $arOffer['code'],
                    'sample' => $arOffer['sample']['path']
                );

                $i++;

            }

            //$Functions -> Pre( $arOffers );
            //$Functions -> Pre( $arItem );

            $arOffer = $arItem['offers'][0];

            $arSections = array();

            if ( $arItem['sections'] ) {

                for ( $ii = 0; $ii < count( $arItem['sections'] ); $ii++ ) {

                    $arSection = $arItem['sections'][$ii];

                    $arSections[$arSection['id']] = array(
                        'id' => $arSection['id'],
                        'name' => $arSection['name'],
                        'code' => $arSection['code'],
                        'parent_id' => $arSection['parent_id']
                    );

                }

            }

            $arSectionTree = $Functions -> BuildTree( $arSections );

            $url = '/katalog/' . $arSectionTree[0]['code'] . '/' . ( ( $arSectionTree[0]['children'][0]['code'] ) ? $arSectionTree[0]['children'][0]['code'] . '/' : '' );

            $html .= '<div class="col-xx-2 col-x-3 col-l-4 catalog-item-col">';

                $html .= '<div class="catalog-item">';

                    $html .= '<a href="' . $url . $arItem['code'] . '/?offer_code=' . $arOffer['code'] . '" class="catalog-item-link">';

                        if ( !empty( $arOffer['discount'] ) ) {

                            $html .= '<div class="catalog-item-discount">-' . $arOffer['discount'] . '%</div>';

                        }

                        if ( $arOffer['available']['code'] == 'order' ) {

                            $html .= '<div class="catalog-item-available ' . $arOffer['available']['code'] . '">' . $arOffer['available']['name'] . '</div>';

                        }

                        $html .= '<div class="catalog-item-picture' . ( ( !empty( $arOffer['picture_2'] ) ) ? ' _double' : '' ) . '">';

                            $picture = $classFiles -> Resize( $arOffer['picture']['path'], 184, 224, false, 'catalog_list_' );

                            $html .= '<img src="' . $picture . '">';

                            if ( !empty( $arOffer['picture_2'] ) ) {

                                $picture = $classFiles -> Resize( $arOffer['picture_2']['path'], 184, 224, false, 'catalog_list_' );

                                $html .= '<img src="' . $picture . '">';

                            }

                        $html .= '</div>';

                        if ( $arOffers ) {

                            if ( count( $arOffers ) > 1 ) {

                                $html .= '<div class="catalog-item-left js-choice-offer-in-list" data-product_id="' . $prev_offer_id . '" data-code="' . $prev_offer_code . '"></div>';
                                $html .= '<div class="catalog-item-right js-choice-offer-in-list"data-product_id="' . $next_offer_id . '" data-code="' . $next_offer_code . '"></div>';

                            }

                            $html .= '<div class="catalog-item-samples">';

                            $i = 0;

                            foreach ( $arOffers as $key_offer => $arOff ) {

                                $html .= '<div class="catalog-item-sample js-choice-offer-in-list' . ( ( $i < 4 ) ? ' visible' : '' ) . ( ( $arOff['code'] == $arOffer['code'] ) ? ' selected' : '' ) . '" data-product_id="' . $arOff['id'] . '" data-code="' . $arOff['code'] . '">';

                                    $html .= '<img class="catalog-item-sample-img" src="' . $arOff['sample'] . '" alt="' . $arOff['name'] . '">';

                                $html .= '</div>';

                                $i++;

                            }

                            $html .= '</div>';

                        }

                        $html .= '<div class="catalog-item-name">' . $arItem['name'] . '</div>';

                        $html .= '<div class="catalog-item-offer-name">' . $arOffer['name'] . '</div>';

                        $html .= '<div class="catalog-item-price">' . number_format( $arOffer['discount_price'], 0, '', ' ' ) . ' <span class="rub">Р</span></div>';

                        $size = $arOffer['size'][0]['code'];

                        if ( !empty( $arParams['filter']['offer_size'] ) ) {

                            foreach ( $arOffer['size'] as $arSize ) {

                                if ( $arSize['code'] === $arParams['filter']['offer_size'][0] ) $size = $arSize['code'];

                            }

                        }

                        $html .= '<div class="catalog-item-button-wrapper">';

                        $html .= '<div class="button catalog-item-button js-in-basket" data-code="' . $arItem['code'] . '" data-offer="' . $arOffer['id'] . '" data-offer_code="' . $arOffer['code'] . '" data-sample="' . ( ( !empty( $arOffer['sample'] ) ) ?$arOffer['sample']['id'] : '' ) . '" data-size="' . $size . '">В корзину</div>';

                        $html .= '</div>';

                    $html .= '</a>';

                $html .= '</div>';

            $html .= '</div>';

        }

    $html .= '</div>';



}

$AnswerCatalog['html'] = $html;*/

//$Functions -> Pre( $arResult );