<?if ( $arParams['pages'] > 1 ) {

    $arGet = array();

    if ( !empty( $_GET ) ) {

        foreach ( $_GET as $key => $value ) {
            if (
                $key === 'section_code'
                || $key === 'section_parent'
                || $key === '_'
                || $key === 'page'
            ) continue;

            if ( $key === 'offer_price' ) {
                if ( !empty( $value['min'] ) ) $arGet[] = $key . '[min]' . '=' . $value['min'];
                if ( !empty( $value['max'] ) ) $arGet[] = $key . '[max]' . '=' . $value['max'];
            } else if ( is_array( $value ) ) {
                for ( $i = 0; $i < count( $value ); $i++ ) {
                    $arGet[] = $key . '[]' . '=' . $value[$i];
                }
            } else {
                $arGet[] = $key . '=' . $value;
            }
        }

    }

    $get_str = ( ( !empty( $arGet ) ) ? implode( '&', $arGet ) : '' )?>

    <nav class="pagination">

        <ul class="pagination-ul">

        <?if ( $arParams['pages'] <= 6 ) {

            $start = 1;
            $end = $arParams['pages'];

        } else {

            if ( ( $arParams['current_page'] - 3 ) < 1 ) {

                $start = 1;
                $end = 6;

            } elseif ( ( $arParams['current_page'] + 3 ) > $arParams['pages'] ) {

                $start = $arParams['pages'] - 6;
                $end = $arParams['pages'];

            } else {

                $start = $arParams['current_page'] - 3;
                $end = $arParams['current_page'] + 3;

            }
        }

        if ( $start >= 3 ) {?>

            <li class="pagination-li"><a class="pagination-a" href="?page=1<?=( ( !empty( $get_str ) ) ? '&' . $get_str : '' )?>">1</a></li>
            <li class="pagination-li pagination-li-no-active">...</li>

        <?}

        for ( $i = $start; $i <= $end; $i++ ) {

            if ( $arParams['current_page'] == $i ) {?>

                <li class="pagination-li pagination-li-no-active pagination-li-active"><?=$i?></li>

            <?} else {?>

                <li class="pagination-li"><a class="pagination-a" href="?page=<?=$i . ( ( !empty( $get_str ) ) ? '&' . $get_str : '' )?>"><?=$i?></a></li>

            <?}

        }

        if ( $end < $arParams['pages'] ) {

            if ( $end != ( $arParams['pages'] - 1 ) ) {?>

                <li class="pagination-li pagination-li-no-active">...</li>

            <?}?>

            <li class="pagination-li"><a class="pagination-a" href="?page=<?=$arParams['pages'] . ( ( !empty( $get_str ) ) ? '&' . $get_str : '' )?>"><?=$arParams['pages']?></a></li>

        <?}?>

    </ul></nav>

<?}