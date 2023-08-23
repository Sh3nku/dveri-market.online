<?require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/core/core.php';

if ( isset( $_POST['OrderSend'] ) ) {

    $arResult = $Functions -> SerializeArray( $_POST['OrderSend'] );
    $answer = array();

    //$Functions -> Pre( $arResult );

    if ( empty( $arResult['email'] ) ) {
        $answer['errors'][] = array(
            'input' => 'email',
            'text' => 'Заполните E-mail'
        );
    } else if ( !$Functions -> ValidationEmail( $arResult['email'] ) ) {
        $answer['errors'][] = array(
            'input' => 'email',
            'text' => 'E-mail заполнен некорректно'
        );
    }

    if ( empty( $arResult['phone'] ) ) {
        $answer['errors'][] = array(
            'input' => 'phone',
            'text' => 'Заполните Телефон'
        );
    }

    if ( !isset( $arResult['payment'] ) ) {
        $answer['errors'][] = array(
            'block' => 'payment',
            'text' => 'Выберите способ оплаты'
        );
    }

    if ( !isset( $arResult['delivery'] ) ) {
        $answer['errors'][] = array(
            'block' => 'delivery',
            'text' => 'Выберите способ доставки'
        );
    }

    $user_id = 0;

    $classMail = new Mail();

    if ( !$User -> IsAuthorized() && !isset( $answer['errors'] ) ) {

        $generate_password = $Functions -> GenerateString( 10 );

        $answer = $User -> Registration(
            array(
                'email' => $arResult['email'],
                'password' => $generate_password,
                're_password' => $generate_password,
                'last_name' => $arResult['last_name'],
                'first_name' => $arResult['first_name'],
                'middle_name' => $arResult['middle_name'],
                'phone' => $arResult['phone'],
                'groups' => array( 3 )
            )
        );

        $user_id = $answer['success']['id'];

        if ( isset( $answer['errors'] ) ) {

            $err = $answer['errors'][0];
            $answer = array();

            $answer['errors'][] = array(
                'block' => 'email',
                'text' => $err
            );

        }

        if ( !empty( $answer['success'] ) ) {

            $classMail -> SendEmail( 2, array(
                'default_email' => DEFAULT_EMAIL,
                'default_name' => DEFAULT_NAME,
                'email' => $arResult['email'],
                'password' => $generate_password,
                'domain' => DOMAIN
            ));

            $User -> Auth(
                array(
                    'email' => $arResult['email'],
                    'password' => $generate_password
                )
            );

        }

    } else {
        $user_id = $_SESSION['user']['id'];
    }

    if ( !isset( $answer['errors'] ) ) {

        $Shop = new Shop();

        $arResDelivery = $Shop -> DeliveryGetList(
            array(),
            array(
                'code' => $arResult['delivery']
            )
        );

        $delivery_id = $arResDelivery['items'][0]['id'];

        $arResPayment = $Shop -> PaymentGetList(
            array(),
            array(
                'code' => $arResult['payment']
            )
        );

        $payment_id = $arResPayment['items'][0]['id'];

        //$Functions -> Pre( $arResult ); die();

        $answer = $Shop -> AddOrder(
            array(
                'status_id' => 1,
                'delivery_id' => $delivery_id,
                'payment_id' => $payment_id,
                'user_id' => $user_id,
                'properties' => array(
                    'last_name' => $arResult['last_name'],
                    'first_name' => $arResult['first_name'],
                    'middle_name' => $arResult['middle_name'],
                    'email' => $arResult['email'],
                    'phone' => $arResult['phone'],
                    'comment' => $arResult['comment'],
                    'city' => $arResult['city'],
                    'street' => $arResult['street'],
                    'house' => $arResult['house'],
                    'flat' => $arResult['flat']
                ),
                'basket' => array(
                    'select' => array(
                        29 => array( 'name', 'model', 'picture' ),
                        30 => array( 'name' ),
                        21 => array( 'name', 'picture' )
                    ),
                    'filter' => array(
                        'cookie' => $_COOKIE['basket']
                    )
                )
            )
        );

        $html = '<div style="font-size: 14px; line-height: 20px; font-family: Calibri, sans-serif; color: #333333">';
        $html .= '<p style="margin: 0 0 5px">Фамилия: ' . $arResult['last_name'] . '</p>';
        $html .= '<p style="margin: 0 0 5px">Имя: ' . $arResult['first_name'] . '</p>';
        $html .= '<p style="margin: 0 0 5px">Отчество: ' . $arResult['middle_name'] . '</p>';
        $html .= '<p style="margin: 0 0 5px">E-mail: ' . $arResult['email'] . '</p>';
        $html .= '<p style="margin: 0 0 5px">Телефон: ' . $arResult['phone'] . '</p>';
        $html .= '<p style="margin: 0 0 5px">Комментарий: ' . $arResult['comment'] . '</p>';
        $html .= '<p style="margin: 0 0 5px">Город: ' . $arResult['city'] . '</p>';
        $html .= '<p style="margin: 0 0 5px">Улица: ' . $arResult['street'] . '</p>';
        $html .= '<p style="margin: 0 0 5px">Дом: ' . $arResult['house'] . '</p>';
        $html .= '<p style="margin: 0">Квартира: ' . $arResult['flat'] . '</p>';

        $html .= '<hr style="margin: 10px 0">';

        $arDelivery = $Shop -> DeliveryGetList(
            array(),
            array(
                'code' => $arResult['delivery']
            )
        );

        $html .= '<p style="margin: 0 0 5px">Доставка: ' . $arDelivery['items'][0]['name'] . ( ( $arDelivery['items'][0]['price'] ) ? ' (' . $arDelivery['items'][0]['price'] . ')' : '' ) . '</p>';

        $arPayment = $Shop -> PaymentGetList(
            array(),
            array(
                'code' => $arResult['payment']
            )
        );

        $html .= '<p style="margin: 0">Оплата: ' . $arPayment['items'][0]['name'] . '</p>';

        $html .= '<hr style="margin: 10px 0">';

        $arOrder = $Shop -> GetOrderItem(
            array(),
            array(
                'id' => $answer['success']['id']
            )
        );

        $summ = ( ( $arDelivery['items'][0]['price'] ) ? $arDelivery['items'][0]['price'] : 0 );
        $summ_with_discount = ( ( $arDelivery['items'][0]['price'] ) ? $arDelivery['items'][0]['price'] : 0 );

        $html .= '<table style="border-collapse: collapse; width: 100%">';

        foreach ( $arOrder['element']['products'] as $key => $arProduct ) {

            $html .= '<tr>';

            $model = '';

            if ( !empty( $arProduct['offer_id'] ) ) {

                $arResOffer = $Content -> GetItemWithOffers(
                    array(),
                    array(
                        'iblock_id' => $arProduct['iblock_id'],
                        'id' => $arProduct['product_id'],
                        'offer_id' => $arProduct['offer_id']
                    )
                );

                $picture_path = $arResOffer['items'][0]['picture']['path'] ?? $arResOffer['items'][0]['offers'][0]['picture']['path'];
                $model = $arResOffer['items'][0]['model'] ?? $arResOffer['items'][0]['offers'][0]['name'];

            } else {

                $arResOffer = $Content -> GetList(
                    array(),
                    array(
                        'iblock_id' => $arProduct['iblock_id'],
                        'id' => $arProduct['product_id']
                    )
                );

                $picture_path = $arResOffer['items'][0]['picture']['path'];

            }

            $arOffer = $arResOffer['items'][0];

            $classFiles = new Files();

            $preview_picture = $classFiles -> Resize( $picture_path, 80, 90, false, 'basket_preview_' );

            $html .= '<td style="border: 1px solid #F4F7FB; padding: 10px 5px; width: 50px"><img width="40" src="' . DOMAIN . $preview_picture . '"></td>';

            $html .= '<td style="border: 1px solid #F4F7FB; padding: 10px 5px">';

            $html .= '<p>' . $arOffer['name'] . ( ( !empty( $model ) ) ? ' ' . $model : '' ) . '</p>';

            if ( is_array( $arProduct['properties'] ) && !empty( $arProduct['properties'] ) ) {

                $arProperties = array();
                $color_group = '';

                foreach ( $arProduct['properties'] as $arProperty ) {

                    $arResProperty = $mysql -> queryList( 'SELECT `name`, `code` FROM `iblock_properties` WHERE `id` = ?i', $arProperty['property_id'] );
                    $arResValue = $mysql -> queryList( 'SELECT `name`, `code` FROM `iblock_values` WHERE `id` = ?i', $arProperty['value_id'] );

                    if ( $arResProperty[0]['code'] == 'color_group' ) {

                        $color_group = $arResValue[0]['name'];

                        continue;

                    }

                    $arProperties[] = $arResProperty[0]['name'] . ': ' . $arResValue[0]['name'];

                }

                $html .= '<p>' . implode( ', ', $arProperties ) . '</p>';

                if ( $arProduct['more_elements'] ) {

                    $arElement = $Content -> GetList(
                        array(),
                        array(
                            'iblock_id' => $arProduct['more_elements'][0]['iblock_id'],
                            'id' => $arProduct['more_elements'][0]['element_id']
                        )
                    );

                    $arColor = $arElement['items'][0];

                    $html .= '<p>' . $color_group . ' - ' . $arColor['ral'] . '</p>';

                }

            }

            $html .= '</td>';

            $html .= '<td style="border: 1px solid #F4F7FB; padding: 10px 5px">';

            if ( $arProduct['price'] == $arProduct['discount_price'] ) {

                $html .= '<p>' . $Functions -> NumberFormat( $arProduct['discount_price'], 2, '.', ' ' ) . '₽</p>';

            } else {

                $html .= '<p style="text-decoration: line-through">' . $Functions -> NumberFormat( $arProduct['price'], 2, '.', ' ' ) . '₽</p>';
                $html .= '<p>' . $Functions -> NumberFormat( $arProduct['discount_price'], 2, '.', ' ' ) . '₽</p>';

            }

            $html .= '</td>';

            $html .= '<td style="border: 1px solid #F4F7FB; padding: 10px 5px">' . $arProduct['count'] . '</td>';

            $html .= '<td style="border: 1px solid #F4F7FB; padding: 10px 5px">';

            if ( $arProduct['price'] === $arProduct['discount_price'] ) {

                $html .= '<p>' . $Functions -> NumberFormat( $arProduct['discount_price'] * $arProduct['count'], 2, '.', ' ' ) . '₽</p>';

            } else {

                $html .= '<p style="text-decoration: line-through">' . $Functions -> NumberFormat( $arProduct['price'] * $arProduct['count'], 2, '.', ' ' ) . '₽</p>';
                $html .= '<p>' . $Functions -> NumberFormat( $arProduct['discount_price'] * $arProduct['count'], 2, '.', ' ' ) . '₽</p>';

            }

            $html .= '</td>';

            $html .= '</tr>';

            $summ += $arProduct['price'] * $arProduct['count'];
            $summ_with_discount += $arProduct['discount_price'] * $arProduct['count'];

        }

        $html .= '</table>';

        $html .= '<p style="font-weight: 700; margin: 0 0 10px">Всего к оплате:</p>';

        if ( $summ === $summ_with_discount ) {

            $html .= '<p style="font-weight: 700; font-size: 20px; line-height: 28px">' . $Functions -> NumberFormat( $summ, 2, '.', ' ' ) . '₽</p>';

        } else {

            $html .= '<p style="text-decoration: line-through; margin: 0 0 5px">' . $Functions -> NumberFormat( $summ, 2, '.', ' ' ) . '₽</p>';
            $html .= '<p style="font-weight: 700; font-size: 20px; line-height: 28px; margin: 0">' . $Functions -> NumberFormat( $summ_with_discount, 2, '.', ' ' ) . '₽</p>';

        }

        $html .= '</div>';

        $classMail -> SendEmail( 'new-order', array(
            'html' => $html,
            'default_email' => DEFAULT_EMAIL,
            'default_name' => DEFAULT_NAME,
            'domain' => DOMAIN
        ));

        $arBasket = $mysql -> queryList( 'SELECT `id` FROM `basket` WHERE `cookie` = ?s', $_COOKIE['basket'] );

        for ( $i = 0; $i < count( $arBasket ); $i++ ) {
            $Shop -> DeleteBasket( $arBasket[$i]['id'] );
        }

        $answer['success']['user']['id'] = $user_id;

    }

    echo json_encode( $answer, JSON_UNESCAPED_UNICODE );

}

if ( isset( $_POST['Recalculate'] ) ) {

    $Shop = new Shop();

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

        }

        $arDelivery = $Shop -> DeliveryGetList(
            array(),
            array(
                'code' => $_POST['Recalculate']['delivery']
            ),
            array(),
            array(
                'id' => 'desc'
            )
        );

        $delivery_price = $arDelivery['items'][0]['price'];

        $summ += $delivery_price;
        $summ_with_discount += $delivery_price;

        $answer['success']['summ']['delivery'] = $Functions -> NumberFormat( $delivery_price, 2, '.', ' ' ) . '₽';
        $answer['success']['summ']['price'] = $Functions -> NumberFormat( $summ, 2, '.', ' ' ) . '₽';
        $answer['success']['summ']['discount_price'] = $Functions -> NumberFormat( $summ_with_discount, 2, '.', ' ' ) . '₽';

    }

    echo json_encode( $answer, JSON_UNESCAPED_UNICODE );

}