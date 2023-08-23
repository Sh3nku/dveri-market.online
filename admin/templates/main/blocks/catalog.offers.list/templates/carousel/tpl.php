<?global $Includes;

if ( $arResult['items'] ) {

    $prefix = rand( 0, 99 );

    $classFiles = new Files();?>

    <div class="container carousel-wrapper">

        <div class="swiper carousel-<?=$prefix?>">
            <div class="swiper-wrapper">

                <?foreach ( $arResult['items'] as $arItem ) {

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

                    $url = '/katalog/' . $arSectionTree[0]['code'] . '/' . ( ( $arSectionTree[0]['children'][0]['code'] ) ? $arSectionTree[0]['children'][0]['code'] . '/' : '' ) . $arItem['code'] . '/';

                    foreach ( $arItem['offers'] as $arOffer ) {?>

                        <div class="swiper-slide">
                            <a href="<?=$url?>">
                                <div class="catalog-item is-carousel">

                                    <?if ( !empty( $arOffer['discount'] ) ) {?>
                                        <div class="catalog-item-discount">-<?=$arOffer['discount'] . ( ( $arOffer['discount_type'] == 1 ) ? '<span class="rub rub-16">Р</span>' : '%' )?></div>
                                    <?}

                                    if ( !empty( $arOffer['available']['code'] ) ) {?>
                                        <div class="catalog-item-available _<?=$arOffer['available']['code']?>"><?=$arOffer['available']['name']?></div>
                                    <?}

                                    $picture = $classFiles -> Resize( $arItem['picture']['path'], 224, 224, false, 'catalog_list_' )?>

                                    <div class="catalog-item-picture">
                                        <img src="<?=$picture?>">
                                    </div>
                                    <div class="catalog-item-name"><?=$arItem['name']?></div>
                                    <div class="catalog-item-offer-name"><?=$arItem['model']?></div>
                                    <div class="catalog-item-price"><?=number_format( $arOffer['discount_price'], 0, '', ' ' )?> <span class="rub">Р</span></div>

                                </div>
                            </a>
                        </div>

                    <?}

                }?>

            </div>

        </div>

        <div class="swiper-button-next js-carousel-next-<?=$prefix?> is-yellow"></div>
        <div class="swiper-button-prev js-carousel-prev-<?=$prefix?> is-yellow"></div>

    </div>

    <script>
        new Swiper(
            '.carousel-<?=$prefix?>', {
                slidesPerView: 2,
                spaceBetween: 16,
                loop: true,
                navigation: {
                    nextEl: '.js-carousel-next-<?=$prefix?>',
                    prevEl: '.js-carousel-prev-<?=$prefix?>',
                },
                autoplay: {
                    delay: 3000,
                },
                breakpoints: {
                    768: {
                        slidesPerView: 3,
                        spaceBetween: 24
                    },
                    1300: {
                        slidesPerView: 4
                    },
                    1700: {
                        slidesPerView: 6
                    },
                    2000: {
                        slidesPerView: 8
                    }
                }
            }
        );
    </script>

<?}