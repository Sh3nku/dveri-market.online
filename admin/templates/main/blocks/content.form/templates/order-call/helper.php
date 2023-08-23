<?require_once $_SERVER['DOCUMENT_ROOT'] . '/admin/core/core.php';

$Mail = new Mail();

if ( $_POST['formSend'] ) {

    $arResult = $Functions -> SerializeArray( $_POST['formSend'] );
    $answer = array();

    $arToken = $Content -> GetIblockIdByToken( $arResult['token_form'] );

    if ( is_array( $arToken ) && $arToken['iblock_id'] ) {

        $arResult = array_merge(
            $arResult,
            array(
                'iblock_id' => $arToken['iblock_id']
            )
        );

    }

    if ( !$arResult['fio'] ) {
        $answer['errors'][] = array(
            'input' => 'fio',
            'text' => 'Введите "ФИО"'
        );
    }

    if ( !$arResult['phone'] ) {
        $answer['errors'][] = array(
            'input' => 'phone',
            'text' => 'Введите "Телефон"'
        );
    }

    if ( !$answer['errors'] ) {

        $answer = $Content -> Add( $arResult );

        if ( $answer['success'] ) {

            $Mail -> SendEmail( 4, array(
                'default_name' => DEFAULT_NAME,
                'default_email' => DEFAULT_EMAIL,
                'fio' => $arResult['fio'],
                'phone' => $arResult['phone'],
                'iblock_id' => $arToken['iblock_id'],
                'id' => $answer['success']['id'],
                'domain' => DOMAIN
            ));

        }

    }

    echo json_encode( $answer, JSON_UNESCAPED_UNICODE );

}