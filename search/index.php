<?require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/header.php';?>

<div class="search-form">
    <form action="">
        <input type="text" name="q" placeholder="Введите фразу для поиска" value="<?=$_GET['q']?>">
        <input type="submit" class="button" value="Найти">
    </form>
</div>

<?$BUFFER -> SetBuffer( 'H1', !empty( $_GET['q'] ) ? 'Результаты поиска по запросу: "' . $_GET['q'] . '"' : 'Поиск' )?>

<?$arFilter = array(
    'iblock_id' => 29
);

if ( !empty( $_GET['q'] ) ) {
    $arFilter['%name'] = $_GET['q'];
}

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
            'page' => 1,
            'count_on_page' => 20
        ),
        'order' => array(),
        'params' => array(
            'section_children' => 'Y'
        )
    )
);?>

<?require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/footer.php';