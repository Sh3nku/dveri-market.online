<?
global $MAIN;
global $BUFFER;

if ( !empty( $arResult['items'] ) ) {

    $arItem = $arResult['items'][0];

    $arSeo = \Iblock\Seo::GetList(
        array(
            'filter' => array(
                'iblock_id' => 33,
                'type' => 'E',
                'element_id' => $arItem['id']
            )
        )
    )['items'][0];

    $title = '';
    $description = '';

    if ( !empty( $arSeo['page_title'] ) ) {
        $page_title = $arSeo['page_title'];
    } else {
        $page_title = $arItem['name'];
    }

    if ( !empty( $arSeo['title'] ) ) {
        $title = $arSeo['title'];
    } else {
        $title = $arItem['name'] . ' купить в Санкт-Петербурге по цене ' . number_format( $arItem['discount_price'], 0, '', ' ' ) . ' рублей';
    }

    if ( !empty( $arSeo['description'] ) ) {
        $description = $arSeo['description'];
    } else {
        $description = $arItem['name'] . ' ✦ Выгодная цена ✦ Качественные материалы ☎ +7 (981) 718-01-10 - покупайте в интернет-магазине «Двери Маркет»!';
    }

    $MAIN -> SetTitle( $title );
    $BUFFER -> SetBuffer( 'description', $description );
    $BUFFER -> SetBuffer( 'canonical', '/furnitura' . $arItem['detail_page_url'] );

    $arGroup = $Content -> GetList(
        array( 'bind' ),
        array(
            'iblock_id' => 34,
            'bind' => $arItem['id']
        )
    );

    $arBindIds = array();

    foreach ( $arGroup['items'][0]['bind'] as $arBind ) {
        $arBindIds[] = $arBind['value'];
    }

    $arGroupItems = array();

    if ( !empty( $arBindIds ) ) {

        $arGroupItems = $Content -> GetList(
            array( 'code', 'sample' ),
            array(
                'iblock_id' => 33,
                'id' => $arBindIds
            )
        );

    }?>

    <div itemscope itemtype="https://schema.org/Product">
        <div class="product-wrapper">
            <div class="product-picture _large">
                <div class="product-available <?=$arItem['available']['code']?>"><?=$arItem['available']['name']?></div>

                <img class="js-picture" src="<?=$arItem['picture']['path']?>" itemprop="image">
            </div>

            <div class="product">
                <h1 itemprop="name"><?=$page_title?></h1>

                <form id="choiceOffer">
                    <input type="hidden" name="code" value="<?=$arItem['code']?>">
                    <input type="hidden" name="type" value="furnitura">

                    <?if ( !empty( $arGroupItems ) ) {?>

                        <div class="product-property">

                            <div class="product-property-name">
                                Цвет
                            </div>

                            <div class="product-property-values">

                                <?foreach ( $arGroupItems['items'] as $key_val => $arGroupItem ) {

                                    if ( $arGroupItem['sections'] ) {

                                        for ( $ii = 0; $ii < count( $arGroupItem['sections'] ); $ii++ ) {

                                            $arSection = $arGroupItem['sections'][$ii];

                                            $arSections[$arSection['id']] = array(
                                                'id' => $arSection['id'],
                                                'name' => $arSection['name'],
                                                'code' => $arSection['code'],
                                                'parent_id' => $arSection['parent_id']
                                            );

                                        }

                                    }

                                    $arSectionTree = $Functions -> BuildTree( $arSections );

                                    $url = '/furnitura/' . $arSectionTree[0]['code'] . '/' . ( ( $arSectionTree[0]['children'][0]['code'] ) ? $arSectionTree[0]['children'][0]['code'] . '/' : '' ) . $arGroupItem['code'] . '/';

                                    $selected = $arGroupItem['id'] == $arItem['id'];?>

                                    <div class="product-property-value">

                                        <?if ( $selected !== true ) {?>
                                            <a href="<?=$url?>">
                                                <img src="<?=$arGroupItem['sample']['path']?>">
                                            </a>
                                        <?} else {?>
                                            <div class="product-property-color-selected">
                                                <img src="<?=$arGroupItem['sample']['path']?>">
                                            </div>
                                        <?}?>
                                    </div>

                                <?}?>

                            </div>

                        </div>

                    <?}?>

                    <div class="product-price-wrapper" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                        <div class="product-price-item">
                            <meta itemprop="price" content="<?=$arItem['discount_price']?>">
                            <meta itemprop="priceCurrency" content="RUB">
                            <link itemprop="availability" href="http://schema.org/InStock">
                            <div class="product-price"><span class="js-price"><?=number_format( $arItem['discount_price'], 0, '', ' ' )?></span> <span class="rub">Р</span></div>

                            <?if ( !empty( $arItem['discount'] ) ) {

                                //$Functions -> Pre( $arOffer );?>

                                <div class="product-price-base-wrapper">

                                    <div class="product-price-base"><span class="js-price-old-price"><?=number_format( $arItem['price'], 0, '', ' ' )?></span> <span class="rub">Р</span></div>

                                    <div class="product-price-discount">-<?=$arItem['discount'] . ( ( $arItem['discount_type'] == 1 ) ? '<span class="rub rub-16">Р</span>' : '%' )?></div>

                                </div>

                            <?}?>
                        </div>
                    </div>

                    <div class="wrapper-product-button">
                        <div class="button product-button-in-basket js-in-basket">В корзину</div>
                    </div>
                </form>

            </div>

        </div>

        <div class="product-tabs">
            <div class="product-tabs-buttons">
                <input type="radio" name="tab" id="description">
                <label class="product-tabs-label" for="description">Описание</label>
            </div>

            <div class="product-tabs-sections">
                <section class="product-tabs-sections-content" data-tab="description" itemprop="description">
                    <?=$arItem['description']?>
                </section>
            </div>
        </div>

    </div>

<?}