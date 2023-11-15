<?require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/header.php';

if (
    !isset( $_GET['section_code'] )
    && !isset( $_GET['section_parent'] )
    && !isset( $_GET['product_code'] )
) {

    $Includes::Template(
        'main:section.list',
        'catalog_main',
        array(
            'select' => array(),
            'filter' => array(
                'iblock_id' => 29,
                'active' => 1
            ),
            'order' => array(
                'sort' => 'asc'
            ),
            'params' => array(
                'reset_keys' => 'N'
            )
        )
    );

} else {

    $Includes -> AddScript( '/katalog/script.js' );

    $is_section = true;
    $section_code = !empty( $_GET['section_parent'] ) ? $_GET['section_parent'] : $_GET['section_code'];
    $product_code = '';

    $arResult = $Content -> GetList(
        array(),
        array(
            'iblock_id' => 32,
            'url_chpu' => ( ( str_contains( $_SERVER['REQUEST_URI'], '?' ) ) ? strstr( $_SERVER['REQUEST_URI'], '?', true ) : $_SERVER['REQUEST_URI'] )
        )
    );

    $arItem = $arResult['items'][0];

    $arTagPage = array();

    if ( !empty( $arItem ) ) {

        $section_code = '';

        if ( !empty( $_GET['product_code'] ) ) {
            $section_code = $_GET['section_parent'];
        } else if ( !empty( $_GET['section_parent'] ) ) {
            $section_code = $_GET['section_code'];
        }

        $params = explode( '?', $arItem['url'] );
        $arParams = explode( '&', $params[1] );
        $arTagParams = array();

        foreach ( $arParams as $arParam ) {
            $exp = explode( '=', $arParam );

            $key = preg_replace( '/\[\]/', '', $exp[0] );

            if ( !empty( $arTagParams[$key] ) ) {
                $arTagParams[$key] = array_merge( $arTagParams[$key], array( $exp[1] ) );
            } else {
                $arTagParams[$key] = array( $exp[1] );
            }

        }

        if ( !empty( $section_code ) ) $arTagParams['section_code'] = $section_code;

        $arTagPage = array(
            'h1' => $arItem['h1'],
            'name' => $arItem['name'],
            'title' => $arItem['title'],
            'text' => $arItem['text'],
            'description' => $arItem['description'],
            'url_chpu' => $arItem['url_chpu'],
            'filter' => $arTagParams
        );

    }

    if (
        (
            !empty( $_GET['product_code'] )
            && empty( $arTagPage )
        ) || (
            !empty( $_GET['section_parent'] )
            && empty( $_GET['product_code'] )
            && $mysql -> query( 'SELECT `id` FROM `i_catalog` WHERE `code` = ?s', $_GET['section_parent'] ) -> num_rows
        )
    ) {
        $product_code = $_GET['product_code'] ?? $_GET['section_parent'];
        $is_section = false;
    }

    if ( $is_section ) {

        if ( !empty( $arTagPage ) ) {

            //if ( $_SESSION['user']['id'] == 1 ) $Functions -> Pre( $arTagPage );

            $MAIN -> SetTitle( $arTagPage['title'] );
            $BUFFER -> SetBuffer( 'description', $arTagPage['description'] );
            $BUFFER -> SetBuffer( 'H1', $arTagPage['h1'] );

            $BREADCRUMB -> Add(
                array(
                    'title' => $arTagPage['name'],
                    'url' => $arTagPage['url_chpu']
                )
            );

        }?>

        <div class="show-filter-wrapper">

            <div class="show-filter js-show-filter">Фильтр</div>

        </div>

        <div class="filter-wrapper">

            <div class="filter-name-wrapper">

                <div class="filter-name">Фильтр</div>

                <div class="filter-close js-show-filter"></div>

            </div>

            <?$Includes::Template(
                'main:filter',
                'catalog',
                array(
                    'filter' => array(
                        'iblock_id' => 29,
                        'section_code' => $section_code
                    )
                )
            )?>

        </div>

        <div id="catalogResult" class="catalog-wrapper">

            <?$arFilter = array(
                'iblock_id' => 29,
                'active' => 1,
                'offer_active' => 1
            );

            $arOrder = array();

            $page = 1;

            foreach ( $_GET as $key => $arItem ) {

                if ( $key == 'offer_price' ) {

                    if ( $arItem['min'] ) $arFilter['>=offer_discount_price'] = $arItem['min'];
                    if ( $arItem['max'] ) $arFilter['<=offer_discount_price'] = $arItem['max'];

                } else if ( $key == 'order' ) {

                    if ( preg_match( '/\-/si', $arItem ) ) {

                        $order = explode( '-', $arItem );
                        $arOrder[( ( $order[0] == 'price' ) ? 'offer_discount_price' : $order[0] )] = $order[1];

                    } else {
                        $arOrder[$arItem] = 'asc';
                    }

                } else if ( $key == 'page' ) {

                    $page = $arItem;

                } else {

                    $arFilter[$key] = $arItem;

                }

            }

            if ( !$arOrder ) {

                $arOrder['sort'] = 'asc';
                $arOrder['id'] = 'desc';

            }

            $arOrder['offer_sort'] = 'asc';
            $arOrder['offer_id'] = 'asc';

            if ( !empty( $arTagPage ) && $_GET['tagPage'] !== 'N' ) {

                unset($arFilter['section_code']);

                $arFilter = array_merge(
                    $arTagPage['filter'],
                    $arFilter
                );

            } else {
                $arFilter['section_code'] = $section_code;
            }

            $arCurrentPage = $Content -> GetList(
                array( 'id', 'name', 'h1', 'url_chpu', 'bind' ),
                array(
                    'iblock_id' => 32,
                    'url_chpu' => explode( '?', $_SERVER['REQUEST_URI'] )[0]
                ),
                array( 'count_on_page' => 1 ),
                array( 'sort' => 'asc' )
            );

            $section_id = 0;

            if ( empty( $arCurrentPage['items'] ) && ( !empty( $_GET['section_parent'] ) || !empty( $_GET['section_code'] ) ) ) {

                $arCurrentSection = $Content -> GetSectionList(
                    array( 'id' ),
                    array(
                        'iblock_id' => 29,
                        'code' => $_GET['section_parent'] ?? $_GET['section_code']
                    ),
                    array( 'limit' => 1 )
                );

                if ( !empty( $arCurrentSection['items'] ) ) $section_id = $arCurrentSection['items'][0]['id'];

            }

            if ( !empty( $arCurrentPage['items'] ) || !empty( $section_id ) ) {

                $arFilterTags = array(
                    'iblock_id' => 32
                );

                if ( !empty( $section_id ) ) {
                    $arFilterTags['bind_section'] = $section_id;
                } else {
                    $arBind = $arCurrentPage['items'][0]['bind'];
                    $arValues = array();

                    if ( !empty( $arBind ) ) {
                        foreach ( $arBind as $arItem ) {
                            $arValues[] = $arItem['value'];
                        }
                    }

                    if ( !empty( $arValues ) ) $arFilterTags['id'] = $arValues;
                }

                //if ( $_SESSION['user']['id'] ) $Functions -> Pre( $arFilterTags );

                if ( !empty( $arFilterTags['id'] ) || $arFilterTags['bind_section'] ) {

                    $arTagPages = $Content -> GetList(
                        array(
                            'name', 'h1', 'url_chpu'
                        ),
                        $arFilterTags
                    );

                }

                if ( $arTagPages['items'] ) {?>
                    <div class="catalog-tag__list">
                        <div class="catalog-tag__title">Выберите подраздел:</div>

                        <div class="buttons">
                            <?foreach ( $arTagPages['items'] as $arItem ) {
                                $name = ( ( !empty( $arItem['name'] ) ) ? $arItem['name'] : $arItem['h1'] )?>
                                <a class="button _with-icon _light small" href="<?=$arItem['url_chpu']?>">
                                    <span><?=$name?></span>
                                    <svg class="_icon-arrow-link">
                                        <use xlink:href="<?=TPL?>/images/icons/arrow-link.svg#arrow-link"></use>
                                    </svg>
                                </a>
                            <?}?>
                        </div>

                        <div class="catalog-tag__more">
                            <div class="button _with-icon small js-short-view">
                                <span>Показать всё</span>
                                <svg class="_icon-arrow-link">
                                    <use xlink:href="<?=TPL?>/images/icons/arrow-link.svg#arrow-link"></use>
                                </svg>
                            </div>
                        </div>
                    </div>
                <?}
            }

            //$MAIN::Show404();

            //$Functions -> Pre( $_GET );
            //$Functions -> Pre( $arFilter );

            $Includes::Template(
                'main:catalog.offers.list',
                'catalog',
                array(
                    'select' => array(
                        'name',
                        'model',
                        'code',
                        'picture',
                        'picture_2',
                        'offer_name',
                        'offer_code',
                        'offer_size',
                        'offer_available',
                        'offer_price',
                        'offer_discount',
                        'offer_discount_price',
                        'offer_discount_type'
                    ),
                    'filter' => $arFilter,
                    'nav' => array(
                        'page' => $page,
                        'count_on_page' => 60
                    ),
                    'order' => $arOrder,
                    'params' => array(
                        'section_children' => 'Y'
                    )
                )
            );

            $arResSectionParent = $mysql -> queryList( 'SELECT `description` FROM `sections` WHERE `iblock_id` = ?i AND `code` = ?s', 29, $_GET['section_parent'] ?? $_GET['section_code'] )?>

        </div>

        <? // Вывод описание раздела

        $detail_text = $arResSectionParent[0]['description'];

        if ( !empty( $arTagPage ) ) $detail_text = $arTagPage['text'];

        if ( !empty( $detail_text ) ) {?>

            <div class="catalog-text"><?=$detail_text?></div>

        <?}

    } else {

        $arFilter = array(
            'iblock_id' => 29,
            'code' => $product_code,
            'offer_active' => 1
        );

        $Includes::Template(
            'main:catalog.offers.item',
            'catalog',
            array(
                'select' => array(
                    'name',
                    'model',
                    'code',
                    'description',
                    'picture',
                    'picture_2',
                    'offer_price',
                    'offer_discount',
                    'offer_discount_price',
                    'offer_discount_type',
                    'offer_name',
                    'offer_code',
                    'offer_sample',
                    'offer_size',
                    'offer_available',
                    'offer_accessories'
                ),
                'filter' => $arFilter,
                'order' => array( 'id' => 'asc' )
            )
        );

    }

}?>

<?require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/footer.php';