<?require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/header.php';

$Includes -> AddScript( '/furnitura/script.js' );

$is_section = false;

if ( empty( $_GET['product_code'] ) )
    $is_section = true;

if ( $is_section ) {?>

    <div class="show-filter-wrapper">
        <div class="show-filter js-show-filter">Фильтр</div>
    </div>

    <div class="filter-wrapper">
        <div class="filter-name-wrapper">
            <div class="filter-name">Фильтр</div>
            <div class="filter-close js-show-filter"></div>
        </div>

        <?$arFilter['iblock_id'] = 33;
        if ( !empty( $_GET['section_code'] ) ) $arFilter['section_code'] = $_GET['section_code'];

        $Includes::Template(
            'main:filter',
            'catalog',
            array(
                'filter' => $arFilter
            )
        )?>
    </div>

    <div id="catalogResult" class="catalog-wrapper">
        <?$arFilter = array(
            'iblock_id' => 33,
            'active' => 1
        );

        $arOrder = array();
        $page = 1;

        foreach ( $_GET as $key => $arItem ) {

            if ( $key == 'price' ) {

                if ( $arItem['min'] ) $arFilter['>=discount_price'] = $arItem['min'];
                if ( $arItem['max'] ) $arFilter['<=discount_price'] = $arItem['max'];

            } else if ( $key == 'order' ) {

                if ( preg_match( '/\-/si', $arItem ) ) {

                    $order = explode( '-', $arItem );
                    $arOrder[( ( $order[0] == 'price' ) ? 'discount_price' : $order[0] )] = $order[1];

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

        $arOrder['sort'] = 'asc';
        $arOrder['id'] = 'asc';

        $Includes::Template(
            'main:content.list',
            'furnutura',
            array(
                'select' => array(
                    'name',
                    'code',
                    'picture',
                    'price',
                    'discount',
                    'discount_price',
                    'discount_type'
                ),
                'filter' => $arFilter,
                'nav' => array(
                    'page' => $page,
                    'count_on_page' => 60
                ),
                'order' => $arOrder
            )
        );?>
    </div>
<?} else {
    $arFilter = array(
        'iblock_id' => 33,
        'code' => $_GET['product_code']
    );

    $Includes::Template(
        'main:content.list',
        'furnitura.item',
        array(
            'select' => array(
                'name',
                'code',
                'picture',
                'available',
                'price',
                'discount',
                'discount_price',
                'discount_type'
            ),
            'filter' => $arFilter,
            'nav' => array(
                'count_on_page' => 1
            )
        )
    );
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/footer.php';