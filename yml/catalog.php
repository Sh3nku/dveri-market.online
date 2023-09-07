<?php require '/var/www/u1603621/data/www/dev.dveri-market.online/admin/core/core.php';

define( 'LOCAL_DOMAIN', 'https://dveri-market.online' );

$arSections = $mysql -> queryList('
    SELECT `id`, `name`, `parent_id`
    FROM `sections`
    WHERE `iblock_id` = 29
    ORDER BY `id`
');

$rsProducts = $mysql -> query('
    SELECT
        t1.`id`, t1.`name`,
        t2.`id` AS product_id, t2.`name` AS product_name, t2.`code` AS product_code, t2.`description` AS product_description,
        t4.`id` AS section_id, t4.`name` AS section_name, t4.`code` AS section_code, t4.`parent_id` AS section_parent_id,
        t5.`price`, t5.`discount`, t5.`discount_price`,
        t6.`path`
    FROM `i_catalog_offers` t1
    INNER JOIN `i_catalog` AS t2 ON t2.`id` = t1.`product_id`
    LEFT JOIN `sections_bind` AS t3 ON t3.`iblock_id` = 29 AND t3.`element_id` = t2.`id`
    LEFT JOIN `sections` AS t4 ON t4.`id` = t3.`section_id`
    LEFT JOIN `i_catalog_offers_prices` AS t5 ON t5.`product_id` = t1.`id`
    LEFT JOIN `files` AS t6 ON t6.`id` = t2.`picture`
    WHERE t1.`active` = 1
    ORDER BY t1.`id`, t4.`id`, t4.`parent_id`
');

$arProducts = array();

while ( $row = mysqli_fetch_assoc( $rsProducts ) ) {
    $arProducts[$row['id']]['id'] = $row['id'];
    $arProducts[$row['id']]['name'] = $row['name'];

    $arProducts[$row['id']]['product']['id'] = $row['product_id'];
    $arProducts[$row['id']]['product']['name'] = $row['product_name'];
    $arProducts[$row['id']]['product']['code'] = $row['product_code'];
    $arProducts[$row['id']]['product']['picture']['path'] = $row['path'];
    $arProducts[$row['id']]['product']['description'] = strip_tags( $row['product_description'], '<br><p>' );

    $arProducts[$row['id']]['sections'][$row['section_id']]['id'] = $row['section_id'];
    $arProducts[$row['id']]['sections'][$row['section_id']]['name'] = $row['section_name'];
    $arProducts[$row['id']]['sections'][$row['section_id']]['code'] = $row['section_code'];
    $arProducts[$row['id']]['sections'][$row['section_id']]['parent_id'] = $row['section_parent_id'];

    $arProducts[$row['id']]['price']['price'] = $row['price'];
    $arProducts[$row['id']]['price']['discount'] = $row['discount'];
    $arProducts[$row['id']]['price']['discount_price'] = $row['discount_price'];
}

$html = '<?xml version="1.0" encoding="UTF-8"?>';

$html .= '<yml_catalog date="' . date( 'c', time() ) . '">';
$html .= '<shop>';
$html .= '<name>Двери-Маркет</name>';

$html .= '<company>ИП Светловидов Игорь Валентинович</company>';

$html .= '<url>' . LOCAL_DOMAIN . '</url>';

$html .= '<currencies>';
$html .= '<currency id="RUR" rate="1"/>';
$html .= '</currencies>';

$html .= '<categories>';
foreach ( $arSections as $arSection ) {
    $html .= '<category id="' . $arSection['id'] . '"' . ( !empty( $arSection['parent_id'] ) ? ' parentId="' . $arSection['parent_id'] . '"' : '' ) . '>' . $arSection['name'] . '</category>';
}
$html .= '</categories>';

$html .= '<offers>';
foreach ( $arProducts as $arProduct ) {

    $arSectionTree = $Functions -> BuildTree( $arProduct['sections'] );

    $section_id = !isset( $arSectionTree[0]['children'] ) ? $arSectionTree[0]['id'] : $arSectionTree[0]['children'][0]['id'];

    $html .= '<offer id="' . $arProduct['id'] . '">';
    $html .= '<name>' . $arProduct['name'] . '</name>';

    $html .= '<url>' . LOCAL_DOMAIN . '/katalog/' . $arSectionTree[0]['code'] . '/' . ( ( $arSectionTree[0]['children'][0]['code'] ) ? $arSectionTree[0]['children'][0]['code'] . '/' : '' ) . $arProduct['product']['code'] . '/</url>';

    $arPrice = $arProduct['price'];

    if ( $arPrice['price'] !== $arPrice['discount_price'] ) {
        $html .= '<price>' . $arPrice['discount_price'] . '</price>';
        $html .= '<oldprice>' . $arPrice['price'] . '</oldprice>';
    } else {
        $html .= '<price>' . $arPrice['price'] . '</price>';
    }

    $html .= '<currencyId>RUB</currencyId>';
    $html .= '<categoryId>' . $section_id . '</categoryId>';
    $html .= '<picture>' . $arProduct['product']['picture']['path'] . '</picture>';
    $html .= '<description><![CDATA[' . stripslashes($arProduct['product']['description']) . ']]></description>';

    $html .= '</offer>';
}
$html .= '</offers>';

$html .= '</shop>';
$html .= '</yml_catalog>';

//header( 'Content-Type: text/xml; charset=utf-8' );

$fd = fopen( '/var/www/u1603621/data/www/dev.dveri-market.online/yml/catalog.xml', 'w' );
fwrite( $fd, $html );
fclose( $fd );

//echo $html;

exit;