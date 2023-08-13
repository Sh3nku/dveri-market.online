<?require $_SERVER['DOCUMENT_ROOT'] . '/admin/core/core.php';

$Shop = new Shop();

if ( isset( $_POST['basketDelete'] ) ) {

    $answer = $Shop -> DeleteBasket( $_POST['basketDelete'] );

    if ( !$answer['errors'] ) {

        $basket_count = $Shop -> GetBasketCount(
            array('sum'),
            array(
                'cookie' => $_COOKIE['basket']
            ),
        );

        $answer['success']['basket_count'] = $basket_count;

        $arItems = $Shop -> GetBasketList(
            array(),
            array(
                'cookie' => $_COOKIE['basket']
            ),
            array(),
            array(
                25 => array(
                    'currency' => 2,
                    'currency_rate' => 1
                ),
                30 => array(
                    'currency' => 2,
                    'currency_rate' => 1
                )
            )
        );

        if ( $arItems ) {

            $summ = 0;
            $summ_with_discount = 0;

            foreach ( $arItems['items'] as $arItem ) {

                if ( $arItem['offer'] ) {
                    $arPrice = $arItem['offer'];
                } else {
                    $arPrice = $arItem['element'];
                }

                $summ += $arPrice['price'] * $arItem['count'];
                $summ_with_discount += $arPrice['discount_price'] * $arItem['count'];

            }

            $answer['success']['summ']['price'] = $Functions -> NumberFormat( $summ, 2, '.', ' ' ) . '₽';
            $answer['success']['summ']['discount_price'] = $Functions -> NumberFormat( $summ_with_discount, 2, '.', ' ' ) . '₽';

        }

    }

    echo json_encode( $answer, JSON_UNESCAPED_UNICODE );

}

if ( isset( $_POST['basketEdit'] ) ) {

    $arResult = $_POST['basketEdit'];

    $answer = $Shop -> EditBasket( $arResult['item'], $arResult );

    if ( !is_array( $answer['errors'] ) ) {

        $basket_count = $Shop -> GetBasketCount(
            array('sum'),
            array(
                'cookie' => $_COOKIE['basket']
            ),
        );

        $answer['success']['basket_count'] = $basket_count;

        $arItems = $Shop -> GetBasketList(
            array(),
            array(
                'cookie' => $_COOKIE['basket']
            ),
            array(),
            array()
        );

        if ( $arItems ) {

            $summ = 0;
            $summ_with_discount = 0;

            foreach ( $arItems['items'] as $arItem ) {

                if ( $arItem['offer'] ) {
                    $arPrice = $arItem['offer'];
                } else {
                    $arPrice = $arItem['element'];
                }

                $summ += $arPrice['price'] * $arItem['count'];
                $summ_with_discount += $arPrice['discount_price'] * $arItem['count'];

                if ( $arItem['id'] == $arResult['item'] ) {

                    $answer['success']['item']['price'] = $Functions -> NumberFormat( $arPrice['price'] * $arItem['count'], 2, '.', ' ' ) . '₽';

                    if ( $arPrice['discount'] ) $answer['success']['item']['discount_price'] = $Functions -> NumberFormat( $arPrice['discount_price'] * $arItem['count'], 2, '.', ' ' ) . '₽';

                }

            }

            $answer['success']['summ']['price'] = $Functions -> NumberFormat( $summ, 2, '.', ' ' ) . '₽';
            $answer['success']['summ']['discount_price'] = $Functions -> NumberFormat( $summ_with_discount, 2, '.', ' ' ) . '₽';

        }

    }

    echo json_encode( $answer, JSON_UNESCAPED_UNICODE );

}