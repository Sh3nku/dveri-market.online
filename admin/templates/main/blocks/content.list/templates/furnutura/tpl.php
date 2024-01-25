<?php
global $Includes;
$classFiles = new Files();

if ( $arResult['items'] ) {?>

    <div class="row" itemscope itemtype="https://schema.org/OfferCatalog">
        <?foreach ( $arResult['items'] as $key => $arItem ) {
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

            $url = '/furnitura/' . $arSectionTree[0]['code'] . '/' . ( ( $arSectionTree[0]['children'][0]['code'] ) ? $arSectionTree[0]['children'][0]['code'] . '/' : '' );?>

            <div class="col-xx-2 col-x-3 col-l-4 catalog-item-col">
                <div class="catalog-item _furnitura" itemprop="itemListElement" itemscope itemtype="https://schema.org/Offer">
                    <a itemprop="url" href="<?=$url . $arItem['code']?>/" class="catalog-item-link">

                        <?if ( !empty( $arItem['discount'] ) ) {?>

                            <div class="catalog-item-discount">-<?=$arItem['discount'] . ( ( $arItem['discount_type'] == 1 ) ? '<span class="rub rub-16">Р</span>' : '%' )?></div>

                        <?}

                        if ( $arItem['available']['code'] == 'order' ) {?>

                            <div class="catalog-item-available _<?=$arItem['available']['code']?>"><?=$arItem['available']['name']?></div>

                        <?}?>

                        <div class="catalog-item-picture">
                            <?$picture = $classFiles -> Resize( $arItem['picture']['path'], 184, 224, false, 'catalog_list_' )?>

                            <img src="<?=$picture?>" itemprop="image" alt="<?=$arItem['name']?>">
                        </div>

                        <div class="catalog-item-name" itemprop="name"><?=$arItem['name']?></div>

                        <?$price = number_format( $arItem['discount_price'], 0, '', ' ' )?>

                        <meta itemprop="price" content="<?=$arItem['discount_price']?>">
                        <meta itemprop="priceCurrency" content="RUB">
                        <div class="catalog-item-price"><?=$price?> <span class="rub">Р</span></div>

                        <link itemprop="availability" href="http://schema.org/InStock">

                        <div class="catalog-item-button-wrapper">
                            <div class="button catalog-item-button js-in-basket" data-code="<?=$arItem['code']?>" data-type="furnitura">В корзину</div>
                        </div>

                    </a>
                </div>
            </div>
        <?}?>
    </div>

    <?//$Functions -> Pre( $arResult );

    if ( $arResult['pages_count'] > 1 ) {

        global $arFilter;

        unset( $arFilter['iblock_id'] );
        unset( $arFilter['page'] )?>

        <tr>
            <td colspan="100%">
                <?$Includes::Template(
                    'main:pagination',
                    'catalog',
                    array(
                        'current_page' => $_GET['page'] ?? 1,
                        'pages' => ceil( $arResult['items_count'] / $arParams['nav']['count_on_page'] ),
                        'parameters' => $arParams
                    )
                )?>
            </td>
        </tr>

    <?}

}