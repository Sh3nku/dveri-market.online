<?if ( !empty( $arResult['items'] ) ) {

    global $Includes;
    $classFiles = new Files();?>

    <div class="row" itemscope itemtype="https://schema.org/OfferCatalog">

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
                <div class="catalog-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/Offer">
                    <a itemprop="url" href="<?=$url . $arItem['code']?>/" class="catalog-item-link">

                        <?if ( !empty( $arOffer['discount'] ) ) {?>

                            <div class="catalog-item-discount">-<?=$arOffer['discount'] . ( ( $arOffer['discount_type'] == 1 ) ? '<span class="rub rub-16">Р</span>' : '%' )?></div>

                        <?}

                        if ( $arOffer['available']['code'] == 'order' ) {?>

                            <div class="catalog-item-available <?=$arOffer['available']['code']?>"><?=$arOffer['available']['name']?></div>

                        <?}?>

                        <div class="catalog-item-picture<?=( ( !empty( $arItem['picture_2'] ) ) ? ' _double' : '' )?>">

                            <?$picture = $classFiles -> Resize( $arItem['picture']['path'], 184, 224, false, 'catalog_list_' )?>

                            <img src="<?=$picture?>" itemprop="image">

                            <?if ( !empty( $arItem['picture_2'] ) ) {

                                $picture = $classFiles -> Resize( $arItem['picture_2']['path'], 184, 224, false, 'catalog_list_' )?>

                                <img src="<?=$picture?>">

                            <?}?>

                        </div>

                        <div class="catalog-item-name" itemprop="name"><?=$arItem['name']?></div>

                        <div class="catalog-item-offer-name"><?=$arItem['model']?></div>

                        <?$price = number_format( $arOffer['discount_price'], 0, '', ' ' )?>

                        <meta itemprop="price" content="<?=$arOffer['discount_price']?>">
                        <meta itemprop="priceCurrency" content="RUB">
                        <div class="catalog-item-price"><?=$price?> <span class="rub">Р</span></div>

                        <link itemprop="availability" href="http://schema.org/InStock">

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