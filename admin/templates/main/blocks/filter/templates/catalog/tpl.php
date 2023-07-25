<?global $Content;
global $arTagPage;

$arParams = $_GET;

if ( !empty( $arTagPage ) ) {

    $arParams = array_merge(
        $arTagPage['filter'],
        $arParams
    );

}

if ( $arResult ) {?>

    <form id="Filter" autocomplete="off">

        <input type="hidden" name="section_code" value="<?=$arParams['section_code']?>">

        <div class="row mb-16">

            <div class="col-s-9 main-filter">

                <div class="row">

                    <?foreach ( $arResult['properties'] as $code => $arProperty ) {

                        $filter_type = $arProperty['filter_type'] ?? $arProperty['type']?>

                        <?if ( $filter_type == 'string' || $filter_type == 'number' || $filter_type == 'number_dot' ) {?>

                            <div class="col-m-3 filter-item-col ">
                                <input name="<?=$arProperty['code']?>" value="<?=$arParams[$arProperty['code']]?>" />
                            </div>

                        <?} else if ( $filter_type == 'range' && ( $arProperty['values']['min'] || $arProperty['values']['max'] ) && ( $arProperty['values']['min'] != $arProperty['values']['max'] ) ) {?>

                            <div class="col-m-3 filter-item-col ">
                                <input class="js-filter-range" type="text" name="<?=$arProperty['code']?>[min]" placeholder="Цена от <?=number_format( $arProperty['values']['min'], 0, '', ' ' )?>" value="<?=$arParams[$arProperty['code']]['min']?>" />
                            </div>

                            <div class="col-m-3 filter-item-col ">
                                <input class="js-filter-range" type="text" name="<?=$arProperty['code']?>[max]" placeholder="Цена до <?=number_format( $arProperty['values']['max'], 0, '', ' ' )?>" value="<?=$arParams[$arProperty['code']]['max']?>" />
                            </div>

                        <?} else if ( $filter_type == 'list' || $filter_type == 'choice' ) {

                            if ( $arProperty['values'] ) {?>

                                <div class="col-m-3 filter-item-col ">

                                    <div class="catalog-select">

                                        <div class="catalog-select-wrapper">

                                            <div id="<?=$arProperty['code']?>Sel" class="catalog-select-name"><?=$arProperty['name']?></div>

                                            <div class="catalog-select-list">

                                                <?$arChecked = array();

                                                foreach ( $arProperty['values'] as $key => $arValue ) {

                                                    if ( $arParams[$arProperty['code']] && in_array( $key, $arParams[$arProperty['code']] ) ) $arChecked[] = $arValue['name'];

                                                    $checkbox_id = $arProperty['code'] . $arValue['code']?>

                                                    <div class="catalog-select-item">

                                                        <input class="checkbox" id="<?=$checkbox_id?>" type="checkbox" name="<?=$arProperty['code']?>[]" value="<?=$key?>"<?=( ( $arParams[$arProperty['code']] && in_array( $key, $arParams[$arProperty['code']] ) ) ? ' checked' : '' )?>/>
                                                        <label for="<?=$checkbox_id?>"><span><?=$arValue['name']?></span></label>

                                                    </div>

                                                <?}?>

                                            </div>

                                        </div>

                                    </div>

                                    <?if ( $arChecked ) {?>
                                        <script>$( '#<?=$arProperty['code']?>Sel' ).text( '<?=implode( ', ', $arChecked )?>' )</script>
                                    <?}?>

                                </div>
                            <?}

                        }

                    }?>

                </div>

            </div>

            <div class="col-s-3 catalog-order">

                <div class="catalog-select">

                    <div class="catalog-select-wrapper">

                        <?if ( isset( $arParams['order'] ) ) {

                            $order_name = 'По умолчанию';

                            switch ( $arParams['order'] ) {

                                case 'price-asc';
                                    $order_name = 'Дешевые';
                                    break;

                                case 'price-desc';
                                    $order_name = 'Дорогие';
                                    break;

                                case 'id-desc';
                                    $order_name = 'По умолчанию';
                                    break;

                            }?>

                            <div class="catalog-select-name" data-placeholder="По умолчанию"><?=$order_name?></div>

                        <?} else {?>

                            <div class="catalog-select-name">По умолчанию</div>

                        <?}?>

                        <div class="catalog-select-list">

                            <div class="catalog-select-item">

                                <input class="radiobox" id="order_cheap" type="radio" name="order" value="price-asc"<?=( ( $arParams['order'] == 'price-asc' ) ? ' checked' : '' )?>>
                                <label for="order_cheap">по возрастанию цены</label>

                            </div>

                            <div class="catalog-select-item">

                                <input class="radiobox" id="order_expensive" type="radio" name="order" value="price-desc"<?=( ( $arParams['order'] == 'price-desc' ) ? ' checked' : '' )?>>
                                <label for="order_expensive">по убыванию цены</label>

                            </div>

                            <div class="catalog-select-item">

                                <input class="radiobox" id="order_default" type="radio" name="order" value="default" <?=( ( empty( $arParams['order'] ) ) ? ' checked' : '' )?>>
                                <label for="order_default">по популярности</label>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <input class="button filter-button js-show-filter" type="submit" value="Фильтровать" />

    </form>

<?}