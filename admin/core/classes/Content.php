<?php

class Content
{
    protected $classIblock;
    protected $classFiles;
    protected $classShop;

    public function __construct()
    {
        $this -> classIblock = new Iblock();
        $this -> classFiles = new Files();
        $this -> classShop = new Shop();
    }

    public function Add ( $array )
    {

        if ( !$array['iblock_id'] ) die( 'Нет ID информационного блока' );

        global $Functions;
        global $mysql;

        //$Functions -> Pre( $array ); die();

        $answer = array();

        $arResIblock = $this -> classIblock -> GetList( array(), array( 'id' => $array['iblock_id'] ), array(), array() );
        $arIblock = $arResIblock['items'][0];
        $table_name = ( ( $arIblock['system'] ) ? '' : 'i_' ) . $arIblock['code'];

        //$Functions -> Pre( $array ); die();

        foreach ( $arIblock['properties'] as $key => $arProp ) {

            if ( $arProp['multiple'] ) {

                $check_require = true;

                if ( $array[$key] ) {

                    for ( $i = 0; $i < count( $array[$key] ); $i++ ) {

                        $arValue = $array[$key][$i];
                        $value = '';

                        if ( is_array( $arValue ) && isset( $arValue['value'] ) ) {

                            $value = $arValue['value'];
                            if ( $value && $arProp['type'] == 'number' && !preg_match('/^\+?\d+$/', $value) ) $answer['errors'][] = 'Не является числом "' . $arProp['name'] . '"';

                            if ( $value && $arProp['type'] == 'number_dot' && ( !preg_match( '/^\+?\d+$/', $value ) && !preg_match( '/^\d+[,.]{1}\d+$/', $value ) ) ) $answer['errors'][] = 'Не является числом с точкой "' . $arProp['name'] . '"';

                        } else if ( is_array( $arValue ) ) {

                            $value = $arValue;
                            if ( $value['delete'] == 'Y' ) continue;

                        } else {
                            $value = $arValue;
                        }

                        if ( $value && $arProp['require_prop'] ) $check_require = false;

                    }

                }

                if ( $check_require && $arProp['require_prop'] ) $answer['errors'][] = '"' . $arProp['name'] . '" обязательное поле';

            } else {

                if ( ( !$array[$key] || ( $arProp['type'] == 'file' && $array[$key][0]['delete'] ) ) && $arProp['require_prop'] ) $answer['errors'][] = 'Введите "' . $arProp['name'] . '"';

                if ( $array[$key] && $arProp['type'] == 'number' && !preg_match('/^\+?\d+$/', $array[$key]) ) $answer['errors'][] = 'Не является числом "' . $arProp['name'] . '"';

                if ( $array[$key] && $arProp['type'] == 'number_dot' && ( !preg_match( '/^\+?\d+$/', $array[$key] ) && !preg_match( '/^\d+[,.]{1}\d+$/', $array[$key] ) ) ) $answer['errors'][] = 'Не является числом с точкой "' . $arProp['name'] . '"';

                if ( $arProp['uniq'] ) {

                    $arCheckUniq = $mysql -> query( 'SELECT `id` FROM ?n WHERE ?n = ?s', $table_name, $arProp['code'], $array[$key] );
                    if ( $arCheckUniq -> num_rows ) $answer['errors'][] = 'Значение поля "' . $arProp['name'] . '" должно быть уникальным';

                }

            }

        }

        /*$Functions -> Pre( $answer['errors'] );
        die();*/

        if ( !$answer['errors'] ) {

            $set = array(
                'active' => ( ( $array['active'] ) ? 1 : 0 ),
                'sort' => ( ( $array['sort'] ) ? $array['sort'] : 0 )
            );

            //$Functions -> Pre( $array ); die();

            if ( $arIblock['is_offer'] ) $set = array_merge( $set, array( 'product_id' => ( ( $array['product'] ) ? $array['product'] : NULL ) ) );

            //$Functions -> Pre( $set ); die();

            $mysql -> query( 'INSERT INTO ?n SET ?u', $table_name, $set );
            $id = $mysql -> queryLastId();

            $set_props = array();

            foreach ( $arIblock['properties'] as $key => $arProp ) {

                if ( $arProp['multiple'] ) {

                    if ( $array[$key] ) {

                        for ( $i = 0; $i < count( $array[$key] ); $i++ ) {

                            if ( $arProp['type'] == 'number_dot' ) {
                                $arValue = $Functions -> CommaToPoint( $array[$key][$i] );
                            } else {
                                $arValue = $array[$key][$i];
                            }

                            if ( is_array( $arValue ) && isset( $arValue['value'] ) ) {
                                $value = $arValue['value'];
                            } else if ( is_array( $arValue ) ) {

                                $value = $arValue;
                                if ( $value['delete'] == 'Y' ) continue;

                            } else {
                                $value = $arValue;
                            }

                            if ( $arProp['type'] == 'file' ) {

                                if ( $arProp['width'] || $arProp['height'] ) {
                                    $new_path = $this -> classFiles -> Resize( $value['path'], $arProp['width'], $arProp['height'], $arProp['smart_resize'] );
                                } else {
                                    $new_path = $this -> classFiles -> Move( $value['path'] );
                                }

                                $file_id = $this -> classFiles -> Register(

                                    array(
                                        'active' => ( ( $value['active'] ) ? 1 : 0 ),
                                        'sort' => $value['sort'],
                                        'name' => $value['name'],
                                        'path' => $new_path,
                                        'description' => $value['description']
                                    )

                                );

                                $value = $file_id;

                            }

                            if ( $value ) $set_props[] = $mysql -> parse( '( ?i, ?i, ?s )', $id, $arProp['id'], $value );

                        }

                    }

                } else {

                    if ( $arProp['type'] == 'file' ) {

                        $arFile = $array[$key][0];

                        if ( !$arFile['path'] ) continue;

                        if ( $arProp['width'] || $arProp['height'] ) {
                            $new_path = $this -> classFiles -> Resize( $arFile['path'], $arProp['width'], $arProp['height'], $arProp['smart_resize'] );
                        } else {
                            $new_path = $this -> classFiles -> Move( $arFile['path'] );
                        }

                        $file_id = $this -> classFiles -> Register(

                            array(
                                'active' => ( ( $arFile['active'] ) ? 1 : 0 ),
                                'sort' => $arFile['sort'],
                                'name' => $arFile['name'],
                                'path' => $new_path,
                                'description' => $arFile['description']
                            )

                        );

                        $set[$key] = $file_id;

                    } else if ( $arProp['type'] == 'number' ) {
                        $set[$key] = ( ( $array[$key] || $array[$key] == '0' ) ? $array[$key] : NULL );
                    } else if ( $arProp['type'] == 'number_dot' ) {
                        $set[$key] = ( ( $array[$key] > 0 || $array[$key] == '0' ) ? $Functions -> CommaToPoint( $array[$key] ) : NULL );
                    } else {
                        $set[$key] = $array[$key];
                    }

                }

            }

            if ( $set ) $mysql -> query( 'UPDATE ?n SET ?u WHERE `id` = ?i', $table_name, $set, $id );
            if ( $set_props ) $mysql -> query( 'INSERT INTO ?n ( `element_id`, `property_id`, `value` ) VALUES ?p', $table_name . '_properties', implode( ', ', $set_props ) );

            if ( $arIblock['is_catalog'] ) {

                for ( $i = 0; $i < count( $array['prices'] ); $i++ ) {

                    $this -> AddPrice(
                        array_merge(
                            array(
                                'iblock_id' => $arIblock['id'],
                                'product_id' => $id
                            ),
                            $array['prices'][$i]
                        )
                    );

                }

            }

            if ( $array['parent_id'] ) {

                $insert = array();

                for ( $i = 0; $i < count( $array['parent_id'] ); $i++ ) {

                    $section_id = $array['parent_id'][$i];

                    if ( $section_id ) $insert[] = $mysql -> parse( '( ?i, ?i, ?i )', $arIblock['id'], $section_id, $id );

                }

                if ( $insert ) $mysql -> query( 'INSERT INTO `sections_bind` ( `iblock_id`, `section_id`, `element_id` ) VALUES ?p', implode( ', ', $insert ) );

            }

            $answer['success']['id'] = $id;

        }

        return $answer;
    }

    public function AddPrice ( $array ):array
    {
        global $mysql;
        global $Functions;

        if (
            !$array['iblock_id']
            || !$array['product_id']
            || ( !$array['price'] && $array['price'] != '0' )
        ) {
            return array(
                'errors' => array(
                    'text' => 'ошибка'
                )
            );
        }

        $arResTableName = $mysql -> queryList( 'SELECT `code` FROM `iblock` WHERE `id` = ?i', $array['iblock_id'] );
        $table_name = $arResTableName[0]['code'];

        $discount_price = $Functions -> CalculateDiscountPrice(
            ( ( $array['price'] ) ? $Functions -> CommaToPoint( $array['price'] ) : 0 ),
            ( ( $array['discount'] ) ? $Functions -> CommaToPoint( $array['discount'] ) : 0 ),
            $array['discount_type']
        );

        $set = array(
            'active' => ( ( $array['active'] ) ? 1 : 0 ),
            'sort' => ( ( $array['sort'] ) ? $array['sort'] : 500 ),
            'product_id' => $array['product_id'],
            'type_id' => $array['type_id'],
            'type_main' => ( ( $array['type_main'] ) ? 1 : 0 ),
            'price' => ( ( $array['price'] || $array['price'] == "0" ) ? $Functions -> CommaToPoint( $array['price'] ) : NULL ),
            'discount' => ( ( $array['discount'] ) ? $Functions -> CommaToPoint( $array['discount'] ) : NULL ),
            'discount_price' => $discount_price['discount_price'],
            'discount_type' => $array['discount_type'],
            'quantity_from' => ( ( $array['quantity_from'] || $array['quantity_from'] == "0" ) ? $array['quantity_from'] : NULL ),
            'quantity_to' => ( ( $array['quantity_to'] || $array['quantity_to'] == "0" ) ? $array['quantity_to'] : NULL ),
            'currency' => $array['currency']
        );

        $mysql -> query( 'INSERT INTO ?n SET ?u', 'i_' . $table_name . '_prices', $set );

        return array(
            'success' => array(
                'id' => $mysql -> queryLastId()
            )
        );
    }

    public function AddSection ( $array )
    {
        global $mysql;
        global $Functions;

        $answer = array();

        //$Functions -> Pre( $array );

        if ( !$array['iblock_id'] ) $answer['errors'][] = 'Введите "ID Информационного блока"';

        if ( !$array['name'] ) $answer['errors'][] = 'Введите "Название"';

        if ( !$array['code'] ) {

            if ( !$array['code'] ) $answer['errors'][] = 'Введите "Код"';

        } else {

            if ( $this -> CheckCode( $array['code'] ) ) {

                $answer['errors'][] = 'Код инфоблока только латинскими символами в нижнем регистре, цифрами, тире и нижнее подчеркивание';

            } else {

                $arResCode = $mysql -> query( 'SELECT `id` FROM `sections` WHERE `code` = ?s AND `iblock_id` = ?i', $array['code'], $array['iblock_id'] );

                if ( $arResCode -> num_rows ) $answer['errors'][] = 'Раздел с таким кодом уже существует';

            }

        }

        if ( !$answer['errors'] ) {

            $file_id = 0;

            if ( $array['file'] ) {

                $arFile = $array['file'][0];

                if ( $arFile['delete'] != 'Y' ) {

                    $new_path = $this -> classFiles -> Move( $arFile['path'] );

                    $file_id = $this -> classFiles -> Register(

                        array(
                            'active' => ( ( $arFile['active'] ) ? 1 : 0 ),
                            'sort' => $arFile['sort'],
                            'name' => $arFile['name'],
                            'path' => $new_path,
                            'description' => $arFile['description']
                        )

                    );

                }

            }

            $set = $array;

            unset( $set['file'] );
            $set['file_id'] = $file_id;

            $mysql -> query( 'INSERT INTO `sections` SET ?u', $set );

            $answer['success'] = array(
                'iblock_id' => $array['infoblock_id'],
                'section_id' => ( ( $array['parent_id'] ) ? $array['parent_id'] : '' ),
                'id' => $mysql -> queryLastId()
            );

        }

        return $answer;
    }

    public function CheckCode ( $str )
    {
        $answer = false;

        if ( preg_match( "/[^a-z_0-9-]+/", $str ) ) $answer = true;
        if ( substr( $str, -1 ) == '_' || $str[0] == '_' || substr( $str, -1 ) == '-' || $str[0] == '-' ) $answer = true;

        return $answer;
    }

    public function Delete ( $array )
    {
        global $Functions;
        global $mysql;

        if ( !$array['iblock_id'] || !$array['id'] ) die();

        $arResIblock = $this -> classIblock -> GetList( array(), array( 'id' => $array['iblock_id'] ), array(), array() );
        $arIblock = $arResIblock['items'][0];
        $table_name = ( ( $arIblock['system'] ) ? '' : 'i_' ) . $arIblock['code'];

        $arElement = $this -> GetList( array(), array( 'id' => $array['id'], 'iblock_id' => $array['iblock_id'] ), array(), array() );
        $arElement = $arElement['items'][0];

        if ( $arIblock['is_catalog'] ) {

            $mysql -> query( 'DELETE FROM ?n WHERE `product_id` = ?i', $table_name . '_prices', $array['id'] );

        }

        if ( $arIblock['properties'] ) {

            foreach ( $arIblock['properties'] as $arProp ) {

                if ( !$arElement[$arProp['code']] ) continue;

                if ( $arProp['type'] == 'file' ) {

                    if ( $arProp['multiple'] ) {

                        for ( $i = 0; $i < count( $arElement[$arProp['code']] ); $i++ ) {
                            $this -> classFiles -> Delete( $arElement[$arProp['code']][$i]['id'] );
                        }

                    } else {

                        $this -> classFiles -> Delete( $arElement[$arProp['code']]['id'] );

                    }

                }

            }

        }

        $mysql -> query( 'DELETE FROM ?n WHERE `id` = ?i', $table_name, $array['id'] );
        $mysql -> query( 'DELETE FROM `sections_bind` WHERE `iblock_id` = ?i AND `element_id` = ?i', $arIblock['id'], $array['id'] );

        $arSeo = \Iblock\Seo::GetList(
            array(
                'filter' => array(
                    'iblock_id' => $array['iblock_id'],
                    'type' => 'E',
                    'element_id' => $array['id']
                )
            )
        );

        if ( !empty( $arSeo['items'] ) ) {

            \Iblock\Seo::Delete(
                array(
                    'iblock_id' => $array['iblock_id'],
                    'type' => 'E',
                    'element_id' => $array['id']
                )
            );

        }

    }

    public function DeleteSection ( $id )
    {
        global $mysql;
        global $Functions;

        if ( !$id ) die();

        $answer = array();

        $arCheckEmptySections = $mysql -> query( 'SELECT count(`id`) FROM `sections` WHERE `parent_id` = ?i GROUP BY `id`', $id );

        if ( $arCheckEmptySections -> num_rows ) $answer['errors'][] = 'Сначала необходимо удалить все подразделы';

        if ( !$answer['errors'] ) {

            $arSection = $mysql -> queryList( 'SELECT `iblock_id`, `file_id` FROM `sections` WHERE `id` = ?i', $id );
            if ( $arSection ) $this -> classFiles -> Delete( $arSection[0]['file_id'] );

            $mysql -> query( 'DELETE FROM `sections` WHERE `id` = ?i', $id );
            $mysql -> query( 'DELETE FROM `sections_bind` WHERE `iblock_id` = ?i AND `section_id` = ?i', $arSection[0]['iblock_id'], $id );

        }

        return $answer;
    }

    public function Edit ( $id, $array )
    {
        global $mysql;
        global $Functions;

        //$Functions -> Pre( $array ); die();

        $answer = array();

        $arResIblock = $this -> classIblock -> GetList( array(), array( 'id' => $array['iblock_id'] ), array(), array() );
        $arIblock = $arResIblock['items'][0];
        $table_name = ( ( $arIblock['system'] ) ? '' : 'i_' ) . $arIblock['code'];

        foreach ( $arIblock['properties'] as $key => $arProp ) {

            if ( $arProp['multiple'] ) {

                $check_require = true;

                if ( $array[$key] ) {

                    for ( $i = 0; $i < count( $array[$key] ); $i++ ) {

                        $arValue = $array[$key][$i];
                        $value = '';

                        if ( is_array( $arValue ) && isset( $arValue['value'] ) ) {

                            $value = $arValue['value'];
                            if ( $value && $arProp['type'] == 'number' && !preg_match('/^\+?\d+$/', $value) ) $answer['errors'][] = 'Не является числом "' . $arProp['name'] . '"';

                            if ( $value && $arProp['type'] == 'number_dot' && ( !preg_match( '/^\+?\d+$/', $value ) && !preg_match( '/^\d+[,.]{1}\d+$/', $value ) ) ) $answer['errors'][] = 'Не является числом с точкой "' . $arProp['name'] . '"';

                        } else if ( is_array( $arValue ) ) {

                            $value = $arValue;
                            if ( $value['delete'] == 'Y' ) continue;

                        } else {
                            $value = $arValue;
                        }

                        if ( $value && $arProp['require_prop'] ) $check_require = false;

                    }

                }

                if ( $check_require && $arProp['require_prop'] ) $answer['errors'][] = '"' . $arProp['name'] . '" обязательное поле';

            } else {

                if ( ( !$array[$key] || ( $arProp['type'] == 'file' && $array[$key][0]['delete'] ) ) && $arProp['require_prop'] ) $answer['errors'][] = 'Введите "' . $arProp['name'] . '"';

                if ( $array[$key] && $arProp['type'] == 'number' && !preg_match('/^\+?\d+$/', $array[$key]) ) $answer['errors'][] = 'Не является числом "' . $arProp['name'] . '"';

                if ( $array[$key] && $arProp['type'] == 'number_dot' && ( !preg_match( '/^\+?\d+$/', $array[$key] ) && !preg_match( '/^\d+[,.]{1}\d+$/', $array[$key] ) ) ) $answer['errors'][] = 'Не является числом с точкой "' . $arProp['name'] . '"';

                if ( $arProp['uniq'] ) {

                    $arCheckUniq = $mysql -> query( 'SELECT `id` FROM ?n WHERE ?n = ?s && `id` != ?s', $table_name, $arProp['code'], $array[$key], $id );
                    if ( $arCheckUniq -> num_rows ) $answer['errors'][] = 'Значение поля "' . $arProp['name'] . '" должно быть уникальным';

                }

            }

        }

        if ( !$answer ) {

            $set = array();
            if ( isset( $array['active'] ) ) $set['active'] = ( ( $array['active'] ) ? 1 : 0 );
            if ( isset( $array['sort'] ) ) $set['sort'] = ( ( $array['sort'] ) ? $array['sort'] : 0 );

            if ( $arIblock['is_offer'] ) $set = array_merge( $set, array( 'product_id' => ( ( $array['product'] ) ? $array['product'] : NULL ) ) );

            $set_props = array();

            foreach ( $arIblock['properties'] as $key => $arProp ) {

                if ( $arProp['multiple'] ) {

                    //$Functions -> Pre( $array );
                    //$Functions -> Pre( $arProp );

                    $check_list = array();

                    if ( $arProp['type'] == 'list' || $arProp['type'] == 'choice' || $arProp['type'] == 'bind_sect' ) {

                        $arListValue = $mysql -> query( 'SELECT `id`, `value` FROM ?n WHERE `element_id` = ?i AND `property_id` = ?i', $table_name . '_properties', $id, $arProp['id'] );


                        while ( $row = mysqli_fetch_assoc( $arListValue ) ) {
                            $check_list[$row['value']] = $row['id'];
                        }

                    }

                    if ( $array[$key] ) {

                        for ( $i = 0; $i < count( $array[$key] ); $i++ ) {

                            if ( $arProp['type'] == 'number_dot' ) {
                                $arValue = $Functions -> CommaToPoint( $array[$key][$i] );
                            } else {
                                $arValue = $array[$key][$i];
                            }

                            //$Functions -> Pre( $arValue );

                            if ( $arProp['type'] == 'file' ) {

                                $arFile = $array[$key][$i];

                                if ( $arFile['delete'] == 'Y' ) {

                                    if ( $arFile['id'] ) {

                                        $this -> classFiles -> Delete( $arFile['id'] );
                                        $mysql -> query( 'DELETE FROM ?n WHERE `element_id` = ?i AND `property_id` = ?i AND `value` = ?i', $table_name . '_properties', $id, $arProp['id'], $arFile['id'] );

                                    }

                                } else {

                                    if ( $arFile['id'] ) {

                                        $this -> classFiles -> Update(
                                            $arFile['id'],
                                            array(
                                                'active' => $arFile['active'],
                                                'sort' => $arFile['sort'],
                                                'description' => $arFile['description']
                                            )
                                        );

                                    } else {

                                        if ( $arProp['width'] || $arProp['height'] ) {
                                            $new_path = $this -> classFiles -> Resize( $arFile['path'], $arProp['width'], $arProp['height'], $arProp['smart_resize'] );
                                        } else {
                                            $new_path = $this -> classFiles -> Move( $arFile['path'] );
                                        }

                                        $file_id = $this -> classFiles -> Register(

                                            array(
                                                'active' => ( ( $arFile['active'] ) ? 1 : 0 ),
                                                'sort' => $arFile['sort'],
                                                'name' => $arFile['name'],
                                                'path' => $new_path,
                                                'description' => $arFile['description']
                                            )

                                        );

                                        $set_props[] = $mysql -> parse( '( ?i, ?i, ?s )', $id, $arProp['id'], $file_id );

                                    }

                                }

                            } else if ( $arProp['type'] == 'list' || $arProp['type'] == 'choice' || $arProp['type'] == 'bind_sect' ) {

                                if ( $arValue && !in_array( $arValue, $check_list ) ) {
                                    $set_props[] = $mysql -> parse( '( ?i, ?i, ?s )', $id, $arProp['id'], $arValue );
                                } else {
                                    unset( $check_list[$arValue] );
                                }

                            } else {

                                if ( $arValue['id'] && !$arValue['value'] ) {

                                    $mysql -> query( 'DELETE FROM ?n WHERE `id` = ?i', $table_name . '_properties', $arValue['id'] );
                                    continue;

                                } else if ( $arValue['id'] && $arValue['value'] ) {
                                    $mysql -> query( 'UPDATE ?n SET ?u WHERE `id` = ?i', $table_name . '_properties', array( 'value' => $arValue['value'] ), $arValue['id'] );
                                } else if ( !$arValue['id'] && $arValue['value'] ) {
                                    $set_props[] = $mysql -> parse( '( ?i, ?i, ?s )', $id, $arProp['id'], $arValue['value'] );
                                }

                            }

                        }

                    }

                    //$Functions -> Pre( $check_list );

                    if ( ( $arProp['type'] == 'list' || $arProp['type'] == 'choice' || $arProp['type'] == 'bind_sect' ) ) $mysql -> query( 'DELETE FROM ?n WHERE `id` IN (?a)', $table_name . '_properties', $check_list );

                } else {

                    if ( $arProp['type'] == 'file' && $array[$key] ) {

                        $arFile = $array[$key][0];

                        $file_id = 0;

                        if ( $arFile['delete'] == 'Y' ) {

                            if ( $arFile['id'] ) $this -> classFiles -> Delete( $arFile['id'] );

                        } else {

                            if ( $arFile['id'] ) {

                                $file_id = $this -> classFiles -> Update(
                                    $arFile['id'],
                                    array(
                                        'active' => $arFile['active'],
                                        'sort' => $arFile['sort'],
                                        'description' => $arFile['description']
                                    )
                                );

                            } else {

                                $arCheckFile = $mysql -> queryList( 'SELECT ?n FROM ?n WHERE id = ?i', $key, $table_name, $id );
                                if ( $arCheckFile ) $this -> classFiles -> Delete( $arCheckFile[0][$key] );

                                if ( $arProp['width'] || $arProp['height'] ) {
                                    $new_path = $this -> classFiles -> Resize( $arFile['path'], $arProp['width'], $arProp['height'], $arProp['smart_resize'] );
                                } else {
                                    $new_path = $this -> classFiles -> Move( $arFile['path'] );
                                }

                                $file_id = $this -> classFiles -> Register(

                                    array(
                                        'active' => ( ( $arFile['active'] ) ? 1 : 0 ),
                                        'sort' => $arFile['sort'],
                                        'name' => $arFile['name'],
                                        'path' => $new_path,
                                        'description' => $arFile['description']
                                    )

                                );

                            }

                        }

                        $set[$key] = $file_id;

                    } else if ( $arProp['type'] == 'number' ) {
                        $set[$key] = ( ( $array[$key] || $array[$key] == '0' ) ? $array[$key] : NULL );
                    } else if ( $arProp['type'] == 'number_dot' ) {
                        if ( isset( $array[$key] ) ) $set[$key] = ( ( $array[$key] > 0 || $array[$key] == '0' ) ? $Functions -> CommaToPoint( $array[$key] ) : NULL );
                    } else {
                        if ( isset( $array[$key] ) ) $set[$key] = $array[$key];
                    }

                }

            }

            if ( $set ) $mysql -> query( 'UPDATE ?n SET ?u WHERE `id` = ?i', $table_name, $set, $id );
            if ( $set_props ) $mysql -> query( 'INSERT INTO ?n ( `element_id`, `property_id`, `value` ) VALUES ?p', $table_name . '_properties', implode( ', ', $set_props ) );

            if ( $arIblock['is_catalog'] ) {

                for ( $i = 0; $i < count( $array['prices'] ); $i++ ) {

                    if ( $array['prices'][$i]['id'] ) {

                        $this -> EditPrice(
                            $array['prices'][$i]['id'],
                            array_merge(
                                array(
                                    'iblock_id' => $arIblock['id']
                                ),
                                $array['prices'][$i]
                            )
                        );

                    } else {

                        $this -> AddPrice(
                            array_merge(
                                array(
                                    'iblock_id' => $arIblock['id'],
                                    'product_id' => $id
                                ),
                                $array['prices'][$i]
                            )
                        );

                    }

                }

            }

            $arResSection = $mysql -> query( 'SELECT `section_id` FROM `sections_bind` WHERE `iblock_id` = ?i AND `element_id` = ?i', $arIblock['id'], $id );

            $arSection = array();

            if ( $arResSection -> num_rows ) {

                while ( $row = mysqli_fetch_assoc( $arResSection ) ) {

                    $arSection[$row['section_id']] = $row['section_id'];
                    if ( !in_array( $row['section_id'], $array['parent_id'] ) ) $mysql -> query( 'DELETE FROM `sections_bind` WHERE `iblock_id` = ?i AND `section_id` = ?i AND `element_id` = ?i', $arIblock['id'], $row['section_id'], $id );

                }

                $arSection = $Functions -> resetKeys( $arSection );

            }

            if ( $array['parent_id'] ) {

                $insert = array();

                for ( $i = 0; $i < count( $array['parent_id'] ); $i++ ) {

                    $value = $array['parent_id'][$i];
                    if ( in_array( $value, $arSection ) ) continue;

                    if ( $value ) $insert[] = $mysql -> parse( '( ?i, ?i, ?i )', $arIblock['id'], $value, $id );

                }

                if ( $insert ) $mysql -> query( 'INSERT INTO `sections_bind` ( `iblock_id`, `section_id`, `element_id` ) VALUES ?p', implode( ', ', $insert ) );

            }

            $answer['success']['id'] = $id;

        }

        return $answer;
    }

    public function EditPrice ( $id, $array )
    {
        global $mysql;
        global $Functions;

        if (
            !$id
            || !$array['iblock_id']
        ) return false;

        $arResTableName = $mysql -> queryList( 'SELECT `code` FROM `iblock` WHERE `id` = ?i', $array['iblock_id'] );
        $table_name = $arResTableName[0]['code'];

        if ( !$array['price'] && $array['price'] != '0' ) $mysql -> query( 'DELETE FROM ?n WHERE `id` = ?i', 'i_' . $table_name . '_prices', $id );

        $discount_price = $Functions -> CalculateDiscountPrice(
            ( ( $array['price'] ) ? $Functions -> CommaToPoint( $array['price'] ) : 0 ),
            ( ( $array['discount'] ) ? $Functions -> CommaToPoint( $array['discount'] ) : 0 ),
            $array['discount_type']
        );

        $set = array(
            'active' => ( ( $array['active'] ) ? 1 : 0 ),
            'sort' => ( ( $array['sort'] ) ? $array['sort'] : 500 ),
            'type_id' => $array['type_id'],
            'type_main' => ( ( $array['type_main'] ) ? 1 : 0 ),
            'price' => ( ( $array['price'] || $array['price'] == "0" ) ? $Functions -> CommaToPoint( $array['price'] ) : NULL ),
            'discount' => ( ( $array['discount'] ) ? $Functions -> CommaToPoint( $array['discount'] ) : NULL ),
            'discount_price' => $discount_price['discount_price'],
            'discount_type' => $array['discount_type'],
            'quantity_from' => ( ( $array['quantity_from'] || $array['quantity_from'] == "0" ) ? $array['quantity_from'] : NULL ),
            'quantity_to' => ( ( $array['quantity_to'] || $array['quantity_to'] == "0" ) ? $array['quantity_to'] : NULL ),
            'currency' => $array['currency']
        );

        $mysql -> query( 'UPDATE ?n SET ?u WHERE `id` = ?i', 'i_' . $table_name . '_prices', $set, $id );
    }

    public function EditSection ( $id, $array )
    {
        global $mysql;
        global $Functions;

        $answer = array();
        $arResIblock = $mysql -> queryList( 'SELECT `iblock_id` FROM `sections` WHERE `id` = ?i', $id );
        $iblock_id = $arResIblock[0]['iblock_id'];

        //$Functions -> Pre( $iblock_id ); die();

        if ( !$array['name'] ) $answer['errors'][] = 'Введите "Название"';

        if ( !$array['code'] ) {

            if ( !$array['code'] ) $answer['errors'][] = 'Введите "Код"';

        } else {

            if ( $this -> CheckCode( $array['code'] ) ) {

                $answer['errors'][] = 'Код инфоблока только латинскими символами в нижнем регистре, цифрами, тире и нижнее подчеркивание';

            } else {

                $arResCode = $mysql -> query( 'SELECT `id` FROM `sections` WHERE `iblock_id` = ?i AND `code` = ?s AND `id` != ?i', $iblock_id, $array['code'], $id );

                if ( $arResCode -> num_rows ) $answer['errors'][] = 'Раздел с таким кодом уже существует';

            }

        }

        //$Functions -> Pre( $answer ); die();

        if ( !$answer['errors'] ) {

            $file_id = 0;

            if ( $array['file'] ) {

                $arFile = $array['file'][0];

                if ( $arFile['delete'] == 'Y' ) {

                    if ( $arFile['id'] ) $this -> classFiles -> Delete( $arFile['id'] );

                } else {

                    if ( $arFile['id'] ) {

                        $file_id = $this -> classFiles -> Update(
                            $arFile['id'],
                            array(
                                'active' => $arFile['active'],
                                'sort' => $arFile['sort'],
                                'description' => $arFile['description']
                            )
                        );

                    } else {

                        if ( $arFile['id'] ) {

                            $arCheckFile = $mysql -> queryList( 'SELECT `file_id` FROM `sections` WHERE `id` = ?i', $arFile['id'] );
                            if ( $arCheckFile ) $this -> classFiles -> Delete( $arCheckFile[0]['file_id'] );

                        }

                        $new_path = $this -> classFiles -> Move( $arFile['path'] );

                        $file_id = $this -> classFiles -> Register(

                            array(
                                'active' => ( ( $arFile['active'] ) ? 1 : 0 ),
                                'sort' => $arFile['sort'],
                                'name' => $arFile['name'],
                                'path' => $new_path,
                                'description' => $arFile['description']
                            )

                        );

                    }

                }

            }

            $set = $array;

            unset( $set['file'] );
            $set['file_id'] = $file_id;

            $mysql -> query( 'UPDATE `sections` SET ?u WHERE `id` = ?i', $set, $id );

            $answer['success'] = array(
                'iblock_id' => $array['infoblock_id'],
                'section_id' => ( ( $array['parent_id'] ) ? $array['parent_id'] : '' ),
                'id' => $id
            );

        }

        return $answer;
    }

    public function DefiningCondition ( $str )
    {
        $answer = array( 'key' => $str );

        if ( preg_match( '/(<=|>=|!=|<|>|%)(.*)/', $str, $arPreg ) ) {

            $answer['condition'] = $arPreg[1];
            $answer['key'] = $arPreg[2];

        }

        return $answer;
    }

    public function GetFilterProperties ( $arSelect, $arFilter, $arOrder, $arParams )
    {
        global $mysql;
        global $Functions;

        if ( !$arFilter['iblock_id'] ) return false;

        $sectionIds = array();

        if ( $arFilter ) {

            foreach ( $arFilter as $key => $filter ) {

                switch ( $key ) {

                    case 'section_code':

                        $section_id = $mysql -> queryList( 'SELECT `id` FROM `sections` WHERE `code` = ?s AND `iblock_id` = ?i LIMIT 1', $arFilter['section_code'], $arFilter['iblock_id'] );
                        $sectionIds = array_merge( array( $section_id[0]['id'] ), $this -> GetSectionChildren( $section_id[0]['id'] ) );

                        break;

                }

            }

        }

        //$Functions -> Pre( $arSelect );
        //$Functions -> Pre( $arFilter );

        $arResIblock = $mysql -> queryList( 'SELECT `id`, `code`, `system`, `is_catalog`, `is_offer` FROM `iblock` WHERE `id` = ?i', $arFilter['iblock_id'] );
        $arIblock = $arResIblock[0];
        $table_name = ( ( $arIblock['system'] ) ? '' : 'i_' ) . $arIblock['code'];

        $arResCheckOffer = $mysql -> queryList( 'SELECT `id` FROM `iblock` WHERE `is_offer` = ?i', $arIblock['id'] );
        $offer_id = ( ( $arResCheckOffer ) ? $arResCheckOffer[0]['id'] : '' );

        $answer = array();

        if ( $arIblock['is_offer'] ) $answer['is_offer'] = $arIblock['is_offer'];

        $arResProperties = $mysql -> query('
            SELECT 
                t1.`multiple`, t1.`name`, t1.`type`, t1.`code`, t1.`filter_type`,
                t2.`name` value_name, t2.`code` value_code
            FROM `iblock_properties` t1
            LEFT JOIN `iblock_values` t2 ON t2.`property_id` = t1.`id`
            WHERE t1.`iblock_id` = ?i AND t1.`show_in_filter` = 1
            ORDER BY t1.`sort`, t1.`name`, t2.`sort`, t2.`name`
        ',
            $arFilter['iblock_id']
        );

        $arProperties = array();

        if ( $arIblock['is_catalog'] AND ( !$arSelect || in_array( 'price', $arSelect ) ) ) {

            $arProperties['price'] = array(
                'multiple' => 0,
                'name' => 'Цена',
                'type' => 'number_dot',
                'filter_type' => 'range'
            );

        }

        while ( $row = mysqli_fetch_assoc( $arResProperties ) ) {

            if ( !$arSelect || in_array( $row['code'], $arSelect ) ) {

                $arProperties[$row['code']]['multiple'] = $row['multiple'];
                $arProperties[$row['code']]['name'] = $row['name'];
                $arProperties[$row['code']]['type'] = $row['type'];
                $arProperties[$row['code']]['filter_type'] = $row['filter_type'];

                if ( $row['value_code'] ) {
                    $arProperties[$row['code']]['values'][$row['value_code']]['name'] = $row['value_name'];
                }

            }

        }

        $arTypePrice = $this -> classShop -> GetTypePrice( array( 'id' ), array( 'main' => 'yes' ) );

        if ( $arParams['currency'] ) {
            $currency_id = $arParams['currency'];
        } else {
            $arCurrency = $this -> classShop -> GetCurrency( array( 'id' ), array( 'base' => 'yes' ) );
            $currency_id = $arCurrency['items'][0]['id'];
        }

        $arCurrencyRate = array();

        if ( $arParams['currency_rate'] && ( $arParams['currency_rate'] != $currency_id ) ) {

            $arResCurrencyRate = $this -> classShop -> GetCurrencyRate( array( 'currency', 'nominal', 'rate' ), array( 'id' => $arParams['currency_rate'] ) );
            $arCurrencyRate = $arResCurrencyRate['items'][0];

        }

        $arFields = array();

        foreach ( $arProperties as $key => $arProperty ) {

            $arHaveProps = array();

            if ( $arProperty['type'] == 'list' || $arProperty['type'] == 'choice' ) {

                if ( $arProperty['multiple'] ) {

                    $arResHaveProps = $mysql -> query('
                        SELECT
                            t2.`value`
                        FROM ?n t1
                        LEFT JOIN ?n t2 ON t2.`element_id` = t1.`id`
                        LEFT JOIN `iblock_properties` t3 ON t3.`id` = t2.`property_id`
                        ?p
                        WHERE t3.`code` = ?s ?p
                        GROUP BY t2.`value`
                    ',
                        $table_name,
                        $table_name . '_properties',
                        ( ( $sectionIds ) ? 'LEFT JOIN `sections_bind` s1 ON s1.`element_id` = t1.`id`' : '' ),
                        $key,
                        ( ( $sectionIds ) ? $mysql -> parse( ' AND s1.`section_id` IN (?a)', $sectionIds ) : '' )
                    );

                    while ( $row = mysqli_fetch_assoc( $arResHaveProps ) ) {

                        if ( $row['value'] ) $arHaveProps[] = $row['value'];

                    }

                } else {

                    $arResHaveProps = $mysql -> query('
                        SELECT
                            t1.?n
                        FROM ?n t1
                        ?p
                        WHERE t1.?n IS NOT NULL ?p
                        GROUP BY t1.?n
                    ',
                        $key,
                        $table_name,
                        ( ( $sectionIds ) ? 'LEFT JOIN `sections_bind` s1 ON s1.`element_id` = t1.`id`' : '' ),
                        $key,
                        ( ( $sectionIds ) ? $mysql -> parse( ' AND s1.`section_id` IN (?a)', $sectionIds ) : '' ),
                        $key
                    );

                    while ( $row = mysqli_fetch_assoc( $arResHaveProps ) ) {

                        if ( $row[$key] ) $arHaveProps[] = $row[$key];

                    }

                }

            } else if ( $arProperty['filter_type'] == 'range' ) {

                //$Functions -> Pre($table_name);

                $arResHaveProps = $mysql -> query('
                    SELECT
                        min( ?p.?n ) min, max( ?p.?n ) max
                    FROM ?n t1
                    ?p
                    ?p
                    ?p
                ',
                    ( ( $key == 'price' ) ? 'p1' : 't1' ),
                    'discount_' . $key,
                    ( ( $key == 'price' ) ? 'p1' : 't1' ),
                    'discount_' . $key,
                    $table_name,
                    ( ( $key == 'price' ) ? $mysql -> parse( 'LEFT JOIN ?n p1 ON p1.`product_id` = t1.`id` AND p1.`type_id` = ?i AND p1.`type_main` = 1 AND p1.`currency` = ?i', $table_name . '_prices', $arTypePrice['items'][0]['id'], $currency_id ) : '' ),
                    ( ( $sectionIds ) ? 'LEFT JOIN `sections_bind` s1 ON s1.`element_id` = t1.`id`' : '' ),
                    ( ( $sectionIds ) ? $mysql -> parse( 'WHERE s1.`section_id` IN (?a)', $sectionIds ) : '' )
                );

                while ( $row = mysqli_fetch_assoc( $arResHaveProps ) ) {

                    if ( $row['min'] != 0 OR $row['max'] != 0 )  $arHaveProps[] = $row;

                }

                if ( $key == 'price' ) {

                    $arHaveProps = array(
                        array(
                            'min' => ( ( $arCurrencyRate['rate'] ) ? $arHaveProps[0]['min'] * $arCurrencyRate['rate'] : $arHaveProps[0]['min'] ),
                            'max' => ( ( $arCurrencyRate['rate'] ) ? $arHaveProps[0]['max'] * $arCurrencyRate['rate'] : $arHaveProps[0]['max'] )
                        )
                    );

                }

            }

            $arFields[$key] = array(
                'name' => $arProperty['name'],
                'type' => $arProperty['type'],
                'code' => ( ( $arIblock['is_offer'] ) ? 'offer_' : '' ) . $key,
                'filter_type' => $arProperty['filter_type']
            );

            if ( $arHaveProps && $arProperty['values'] ) {

                foreach ( $arProperty['values'] as $value_key => $arValue ) {

                    if ( !in_array( $value_key, $arHaveProps ) ) continue;
                    $arFields[$key]['values'][$value_key]['name'] = $arValue['name'];
                    $arFields[$key]['values'][$value_key]['code'] = $value_key;

                }

            }

            if ( $arProperty['filter_type'] == 'range' && ( $arHaveProps[0]['min'] || $arHaveProps[0]['max'] ) ) $arFields[$key]['values'] = $arHaveProps[0];

        }

        return array(
            'offer_id' => $offer_id,
            'properties' => $arFields
        );

    }

    public function GetIblockIdByToken ( $token )
    {
        if ( !$token ) return 'Нет Токена';

        global $mysql;

        $arResult = $mysql -> queryList( 'SELECT `iblock_id`, `element_id` FROM `protected_form` WHERE `token` = ?s LIMIT 1', $token );

        return $arResult[0];
    }

    public function GetItemWithOffers ( $arSelect = array(), $arFilter = array(), $arOrder = array(), $arParams = array() ): array
    {
        global $mysql;
        global $Functions;

        $answer = array();

        $arResIblock = $this -> classIblock -> GetList(
            array(),
            array(
                'id' => $arFilter['iblock_id']
            ),
            array(),
            array()
        );

        $arIblock = $arResIblock['items'][0];
        $table_name = ( ( $arIblock['system'] ) ? '' : 'i_' ) . $arIblock['code'];

        $arProperties = array(
            'id' => array(
                'type' => 'number',
                'multiple' => 0
            ),
            'active' => array(
                'type' => 'number',
                'multiple' => 0
            ),
            'sort' => array(
                'type' => 'number',
                'multiple' => 0
            ),
            'date_create' => array(
                'type' => 'datetime',
                'multiple' => 0
            ),
            'date_update' => array(
                'type' => 'datetime',
                'multiple' => 0
            )
        );

        $arProperties = array_merge(
            $arProperties,
            $arIblock['properties']
        );

        //$Functions -> Pre( $arProperties );

        $arResIblockOffers = $this -> classIblock -> GetList(
            array(),
            array(
                'is_offer' => $arIblock['id']
            ),
            array(),
            array()
        );

        $arIblockOffers = $arResIblockOffers['items'][0];
        $table_name_offers = ( ( $arIblockOffers['system'] ) ? '' : 'i_' ) . $arIblockOffers['code'];

        $arPropertiesOffers = array(
            'id' => array(
                'type' => 'number',
                'multiple' => 0
            ),
            'active' => array(
                'type' => 'number',
                'multiple' => 0
            ),
            'sort' => array(
                'type' => 'number',
                'multiple' => 0
            ),
            'date_create' => array(
                'type' => 'datetime',
                'multiple' => 0
            ),
            'date_update' => array(
                'type' => 'datetime',
                'multiple' => 0
            )
        );

        if ( $arIblockOffers['is_offer'] ) {

            $arPropertiesOffers = array_merge(
                $arPropertiesOffers,
                array(
                    'product_id' => array(
                        'type' => 'number',
                        'multiple' => 0
                    )
                )
            );

        }

        $arPropertiesOffers = array_merge(
            $arPropertiesOffers,
            $arIblockOffers['properties']
        );

        //$Functions -> Pre( $arParams );

        if ( $arIblockOffers['is_catalog'] ) {

            $arPropertiesOffers = array_merge(
                $arPropertiesOffers,
                array(
                    'price' => array(
                        'type' => 'number_dot',
                        'multiple' => 0,
                        'is_catalog' => 1
                    ),
                    'discount' => array(
                        'type' => 'number_dot',
                        'multiple' => 0,
                        'is_catalog' => 1
                    ),
                    'discount_price' => array(
                        'type' => 'number_dot',
                        'multiple' => 0,
                        'is_catalog' => 1
                    ),
                    'discount_type' => array(
                        'type' => 'number',
                        'multiple' => 0,
                        'is_catalog' => 1
                    ),
                    'currency' => array(
                        'type' => 'number',
                        'multiple' => 0,
                        'is_catalog' => 1
                    )
                )
            );

            if ( $arParams['type_price'] ) {
                $type_price = $arParams['type_price'];
            } else {
                $arTypePrice = $this -> classShop -> GetTypePrice( array( 'id' ), array( 'main' => 'yes' ) );
                $type_price = $arTypePrice['items'][0]['id'];
            }

            if ( $arParams['currency'] ) {
                $currency_id = $arParams['currency'];
            } else {
                $arCurrency = $this -> classShop -> GetCurrency( array( 'id' ), array( 'base' => 'yes' ) );
                $currency_id = $arCurrency['items'][0]['id'];
            }

            if ( $arParams['currency_rate'] && $arParams['currency_rate'] != $currency_id ) {

                $arResCurrencyRate = $this -> classShop -> GetCurrencyRate( array( 'currency', 'nominal', 'rate' ), array( 'id' => $arParams['currency_rate'] ) );
                $arCurrencyRate = $arResCurrencyRate['items'][0];

            }

            $join_price = $mysql -> parse('
                LEFT JOIN ?n p1 ON p1.`product_id` = o1.`id` AND p1.`type_id` = ?i AND ?p AND p1.`currency` = ?i
            ',
                $table_name_offers . '_prices',
                $type_price,
                ( ( $arParams['quantity'] ) ? $mysql -> parse( 'IF ( p1.`quantity_from` IS NOT NULL, p1.`quantity_from` <= ?i, p1.`quantity_from` IS NULL ) AND IF ( p1.`quantity_to` IS NOT NULL ,p1.`quantity_to` >= ?i ,p1.`quantity_to` IS NULL )', $arParams['quantity'], $arParams['quantity'] ) : 'p1.`type_main` = 1' ),
                $currency_id
            );

        }

        $select = array();
        $select_props = array();
        $is_multiple = false;

        foreach ( $arProperties as $key => $arProperty ) {

            if ( $arProperty['multiple'] ) {

                if ( !$arSelect || in_array( $key, $arSelect ) ) {

                    $is_multiple = true;
                    $select_props[] = $key;

                }

            } else {

                if ( !$arSelect || in_array( $key, $arSelect ) || $key == 'id' ) $select[] = $mysql -> parse( 't1.?n', $key );

            }

        }

        //$Functions -> Pre( $select );
        //$Functions -> Pre( $select_props );
        //$Functions -> Pre( $is_multiple );

        $select_offers = array();
        $select_props_offers = array();
        $is_multiple_offers = false;
        $is_select_price = false;

        foreach ( $arPropertiesOffers as $key => $arProperty ) {

            if ( $arProperty['multiple'] ) {

                if ( !$arSelect || in_array( 'offer_' . $key, $arSelect ) ) {

                    $is_multiple_offers = true;
                    $select_props_offers[] = $key;

                }

            } else {

                if ( !$arSelect || in_array( 'offer_price', $arSelect ) || in_array( 'offer_discount', $arSelect ) || in_array( 'offer_discount_price', $arSelect ) ) $is_select_price = true;
                if ( !$arSelect || in_array( 'offer_' . $key, $arSelect ) || $key == 'id' || $key == 'product_id' ) $select_offers[] = $mysql -> parse( '?p.?n offer_?p', ( ( $arProperty['is_catalog'] ) ? 'p1' : 'o1' ), $key, $key );

            }

        }

        //$Functions -> Pre( $select_offers );
        //$Functions -> Pre( $select_props_offers );
        //$Functions -> Pre( $is_multiple_offers );
        //$Functions -> Pre( $is_select_price );

        //$Functions -> Pre( $arFilter );

        $where = array();

        $where_offers = array();
        $where_multi_offers = array();
        $is_where_offers = false;
        $is_where_multi_offers = false;

        if ( $arFilter ) {

            $count_offers = 0;

            foreach ( $arFilter as $key => $value ) {

                $condition = '';

                $arCondition = $this -> DefiningCondition( $key );

                if ( $arCondition['condition'] ) {

                    $condition = $arCondition['condition'];
                    $key = $arCondition['key'];

                }

                if ( $condition == '%' ) {

                    $condition = 'LIKE';
                    $value = '%' . $value . '%';

                }

                if (
                    $arProperties[$key]
                    && $value
                    && (
                        !$arProperties[$key]['multiple']
                        && (
                            $key == 'id'
                            || $arProperties[$key]['uniq']
                        )
                    )

                ) {

                    switch ( $arProperties[$key]['type'] ) {

                        case 'number':
                            $product_id = $value;
                            $where[] = $mysql -> parse( 't1.?n = ?i', $key, $value );
                            break;

                        case 'string':
                            $arResId = $mysql -> queryList( 'SELECT `id` FROM ?n WHERE ?n = ?s', $table_name, $key, $value );
                            $product_id = $arResId[0]['id'];
                            $where[] = $mysql -> parse( 't1.?n = ?s', $key, $value );
                            break;

                    }

                }

                if ( preg_match( '/offer_/', $key ) && $arPropertiesOffers[preg_replace( '/^offer_/', '', $key )] && $value ) {

                    if ( preg_match( '/offer_/', $key ) ) $key = preg_replace( '/^offer_/', '', $key );

                    if ( $arPropertiesOffers[$key]['multiple'] ) {

                        $is_where_multi_offers = true;

                        if ( is_array( $value ) ) {

                            $where_multi_offers[] = $mysql -> parse('
                                JOIN (
                                    SELECT o1.`element_id`
                                    FROM ?n o1
                                    WHERE o1.`property_id` = ?i AND o1.value IN ( ?a )
                                ) JO?i ON JO?i.`element_id` = o1.`id`
                            ',
                                $table_name_offers . '_properties',
                                $arPropertiesOffers[$key]['id'],
                                $arFilter['offer_' . $key],
                                $count_offers,
                                $count_offers
                            );

                            $count_offers++;

                        } else {

                            $where_multi_offers[] = $mysql -> parse('
                                JOIN (
                                    SELECT o1.`element_id`
                                    FROM ?n o1
                                    WHERE o1.`property_id` = ?i AND o1.value = ?s
                                ) JO?i ON JO?i.`element_id` = o1.`id`
                            ',
                                $table_name_offers . '_properties',
                                $arPropertiesOffers[$key]['id'],
                                $arFilter['offer_' . $key],
                                $count_offers,
                                $count_offers
                            );

                            $count_offers++;

                        }

                    } else {

                        $is_where_offers = true;

                        if ( is_array( $value ) ) {

                            $where_offers[] = $mysql -> parse( 'o1.?n IN (?a)', $key, $value );

                        } else {

                            if ( $arPropertiesOffers[$key]['is_catalog'] ) $is_where_price = true;

                            switch ( $arPropertiesOffers[$key]['type'] ) {

                                case 'bind':
                                case 'file':
                                    $where_offers[] = $mysql -> parse( 'o1.?n = ?i', $key, $value );
                                    break;

                                case 'choice':
                                case 'list':
                                    $where_offers[] = $mysql -> parse( 'o1.?n = ?s', $key, $value );
                                    break;

                                case 'number':
                                    $where_offers[] = $mysql -> parse( 'o1.?n ?p ?i', $key, ( ( $condition ) ? $condition : '=' ), $value );
                                    break;

                                case 'number_dot':
                                case 'string':
                                case 'text':
                                    $where_offers[] = $mysql -> parse( '?p.?n ?p ?s',( ( $arPropertiesOffers[$key]['is_catalog'] ) ? 'p1' : 'o1' ), $key, ( ( $condition ) ? $condition : '=' ), $value );
                                    break;

                            }

                        }

                    }

                }

            }

        }

        if ( !$where ) $answer['errors'][] = 'Нет условий';

        //$Functions -> Pre( $where );

        //$Functions -> Pre( $where_offers );
        //$Functions -> Pre( $is_where_offers );

        //$Functions -> Pre( $where_multi_offers );
        //$Functions -> Pre( $is_where_multi_offers );

        if ( !$answer['errors'] ) {

            $time_start = microtime( 1 );

            $arResProducts = $mysql -> query('
                    SELECT
                        ?p?p
                    FROM ?n t1
                    ?p
                    ?p
                ',
                implode( ', ', $select ),
                ( ( $is_multiple ) ? ', t2.`id` prop_id, t2.`value` prop_value, t3.`type` prop_type, t3.`code` prop_code' : '' ),
                $table_name,
                ( ( $is_multiple ) ? $mysql -> parse( 'LEFT JOIN ?n t2 ON t2.`element_id` = t1.`id` LEFT JOIN `iblock_properties` t3 ON t3.`id` = t2.`property_id` AND t3.`code` IN ( ?a )', $table_name . '_properties', $select_props ) : '' ),
                ( ( $where ) ? 'WHERE ' . implode( ' AND ', $where ) : '' )
            );

            if ( $arParams['show_query_time'] == 'Y' ) echo ( microtime(1) - $time_start ) . ' запрос с сортировкой и выборкой товаров<br />';

            $time_start = microtime( 1 );

            //$Functions -> Pre( $arResProducts );

            if ( !$order_offers ) {

                $is_order_price = true;
                $order_offers[] = 'o1.`sort` asc';
                $order_offers[] = 'p1.`discount_price` asc';

            }

            $arResOffers = $mysql -> query('
                    SELECT
                        ?p?p
                    FROM ?n o1
                    ?p
                    ?p
                    ?p
                    WHERE o1.`product_id` = ?i?p
                    ?p
                ',
                implode( ', ', $select_offers ),
                ( ( $is_multiple_offers ) ? ', o2.`id` prop_id, o2.`value` prop_value, o3.`type` prop_type, o3.`code` prop_code' : '' ),
                $table_name_offers,
                ( ( $is_multiple_offers ) ? $mysql -> parse( 'LEFT JOIN ?n o2 ON o2.`element_id` = o1.`id` LEFT JOIN `iblock_properties` o3 ON o3.`id` = o2.`property_id` AND o3.`code` IN ( ?a )', $table_name_offers . '_properties', $select_props_offers ) : '' ),
                ( ( $is_select_price || $is_order_price ) ? $join_price : '' ),
                ( ( $is_where_multi_offers ) ? implode( "\n", $where_multi_offers ) : '' ),
                $product_id,
                ( ( $is_where_offers ) ? ' AND ' . implode( ' AND ', $where_offers ) : '' ),
                ( ( $order_offers ) ? 'ORDER BY ' . implode( ', ', $order_offers ) : '' )
            );

            if ( $arParams['show_query_time'] == 'Y' ) echo ( microtime(1) - $time_start ) . ' запрос с сортировкой и выборкой торговых предложений<br />';

            $time_start = microtime( 1 );

            $items = array();
            $arFiles = array();
            $arFilesIds = array();

            while ( $row = mysqli_fetch_assoc( $arResProducts ) ) {

                for ( $i = 0; $i < count( $select ); $i++ ) {

                    $prop = explode( '`', $select[$i] );

                    $key = $prop[1];

                    if ( $arProperties[$key]['type'] == 'list' || $arProperties[$key]['type'] == 'choice' ) {

                        $items[$row['id']][$key]['name'] = $arProperties[$key]['values'][$row[$key]]['name'];
                        $items[$row['id']][$key]['code'] = $arProperties[$key]['values'][$row[$key]]['code'];

                        if ( $arProperties[$key]['values'][$row[$key]]['file'] ) $items[$row['id']][$key]['file'] = $arProperties[$key]['values'][$row[$key]]['file'];

                    } else if ( $arProperties[$key]['type'] == 'file' ) {

                        if ( $row[$key] ) {

                            $arFiles[$row[$key]] = array(
                                'item' => $row['id'],
                                'code' => $key,
                                'file_id' => $row[$key]
                            );

                            if ( !in_array( $row[$key], $arFilesIds ) ) $arFilesIds[] = $row[$key];

                        }

                        $items[$row['id']][$key] = $row[$key];

                    } else if ( $arProperties[$key]['type'] == 'number_dot' ) {

                        $items[$row['id']][$key] = ( $row[$key] ) ? round( $row[$key], 2 ) : '';

                    } else {

                        $items[$row['id']][$key] = $row[$key];
                    }

                }

                if ( $row['prop_id'] AND $row['prop_code'] ) {

                    if ( $row['prop_type'] == 'list' || $row['prop_type'] == 'choice' ) {

                        $items[$row['id']][$row['prop_code']][$row['prop_id']]['name'] = $arProperties[$row['prop_code']]['values'][$row['prop_value']]['name'];
                        $items[$row['id']][$row['prop_code']][$row['prop_id']]['code'] = $arProperties[$row['prop_code']]['values'][$row['prop_value']]['code'];

                        if ( $arProperties[$row['prop_code']]['values'][$row['prop_value']]['file'] ) {
                            $items[$row['id']][$row['prop_code']][$row['prop_id']]['file'] = $arProperties[$row['prop_code']]['values'][$row['prop_value']]['file'];
                        }

                    } else if ( $row['prop_type'] == 'file' ) {

                        $arFiles[$row['prop_value']] = array(
                            'item' => $row['id'],
                            'code' => $row['prop_code'],
                            'file_id' => $row['prop_value']
                        );

                        if ( !in_array( $row['prop_value'], $arFilesIds ) ) $arFilesIds[] = $row['prop_value'];

                        $items[$row['id']][$row['prop_code']][$row['prop_value']]['value'] = $row['prop_value'];

                    } else {

                        $items[$row['id']][$row['prop_code']][$row['prop_id']]['id'] = $row['prop_id'];
                        $items[$row['id']][$row['prop_code']][$row['prop_id']]['value'] = $row['prop_value'];

                    }

                }

            }

            while ( $row = mysqli_fetch_assoc( $arResOffers ) ) {

                for ( $i = 0; $i < count( $select_offers ); $i++ ) {

                    $prop = explode( '`', $select_offers[$i] );

                    $key = $prop[1];
                    $val = trim( $prop[2] );

                    if ( $arPropertiesOffers[$key]['type'] == 'list' || $arPropertiesOffers[$key]['type'] == 'choice' ) {

                        $items[$row['offer_product_id']]['offers'][$row['offer_id']][$key]['name'] = $arPropertiesOffers[$key]['values'][$row[$val]]['name'];
                        $items[$row['offer_product_id']]['offers'][$row['offer_id']][$key]['code'] = $arPropertiesOffers[$key]['values'][$row[$val]]['code'];

                        if ( $arPropertiesOffers[$key]['values'][$row[$key]]['file'] ) $items[$row['offer_product_id']]['offers'][$row['offer_id']][$key]['file'] = $arPropertiesOffers[$key]['values'][$row[$val]]['file'];

                    } else if ( $arPropertiesOffers[$key]['type'] == 'file' ) {

                        if ( $row[$val] ) {

                            $arFiles[$row[$val]] = array(
                                'item' => $row['offer_product_id'],
                                'offer' => $row['offer_id'],
                                'code' => $key,
                                'file_id' => $row[$val]
                            );

                            $arFilesIds[$row[$val]] = $row[$val];

                        }

                        $items[$row['offer_product_id']]['offers'][$row['offer_id']][$key] = $row[$val];

                    } else if ( $arPropertiesOffers[$key]['type'] == 'number_dot' ) {

                        if ( ( $key == 'price' || $key == 'discount' || $key == 'discount_price' ) && $arCurrencyRate ) {
                            $items[$row['offer_product_id']]['offers'][$row['offer_id']][$key] = ( $row[$val] ) ? round( $row[$val] * $arCurrencyRate['rate'] / $arCurrencyRate['nominal'], 2 ) : '';
                        } else {
                            $items[$row['offer_product_id']]['offers'][$row['offer_id']][$key] = ( $row[$val] ) ? round( $row[$val], 2 ) : '';
                        }

                    } else {
                        $items[$row['offer_product_id']]['offers'][$row['offer_id']][$key] = $row[$val];
                    }

                }

                if ( $row['prop_id'] AND $row['prop_code'] ) {

                    if ( $row['prop_type'] == 'list' || $row['prop_type'] == 'choice' ) {

                        $items[$row['offer_product_id']]['offers'][$row['offer_id']][$row['prop_code']][$row['prop_id']]['name'] = $arPropertiesOffers[$row['prop_code']]['values'][$row['prop_value']]['name'];
                        $items[$row['offer_product_id']]['offers'][$row['offer_id']][$row['prop_code']][$row['prop_id']]['code'] = $arPropertiesOffers[$row['prop_code']]['values'][$row['prop_value']]['code'];

                        if ( $arProperties[$row['prop_code']]['values'][$row['prop_value']]['file'] ) {
                            $items[$row['offer_product_id']]['offers'][$row['offer_id']][$row['prop_code']][$row['prop_id']]['file'] = $arPropertiesOffers[$row['prop_code']]['values'][$row['prop_value']]['file'];
                        }

                    } else if ( $row['prop_type'] == 'file' ) {

                        if ( $row['prop_value'] ) {

                            $arFiles[$row['prop_value']] = array(
                                'item' => $row['offer_product_id'],
                                'offer' => $row['offer_id'],
                                'code' => $row['prop_code'],
                                'file_id' => $row['prop_value']
                            );

                            $arFilesIds[$row['prop_value']] = $row['prop_value'];

                        }

                        $items[$row['offer_product_id']]['offers'][$row['offer_id']][$row['prop_code']][$row['prop_value']]['value'] = $row['prop_value'];

                    } else {

                        $items[$row['offer_product_id']]['offers'][$row['offer_id']][$row['prop_code']][$row['prop_id']]['id'] = $row['prop_id'];
                        $items[$row['offer_product_id']]['offers'][$row['offer_id']][$row['prop_code']][$row['prop_id']]['value'] = $row['prop_value'];

                    }

                }

            }

            if ( $arParams['show_query_time'] == 'Y' ) echo ( microtime(1) - $time_start ) . ' формирование основного массива<br />';

            //$Functions -> Pre( $arFiles );
            //$Functions -> Pre( $arFilesIds );

            if ( $arFilesIds ) {

                $time_start = microtime( 1 );

                $arFilesResult = $this -> classFiles -> GetFiles(
                    array(),
                    array(
                        'id' => $arFilesIds
                    ),
                    array(
                        'count_on_page' => 999
                    ),
                    array(),
                    array(
                        'reset_keys' => 'N'
                    )
                );

                //$Functions -> Pre( $arFilesResult );

                foreach ( $arFiles as $key => $file ) {

                    //$Functions -> Pre( $file );

                    if ( $file['offer'] ) {

                        if ( $items[$file['item']]['offers'][$file['offer']][$file['code']][$file['file_id']] ) {
                            $items[$file['item']]['offers'][$file['offer']][$file['code']][$file['file_id']] = $arFilesResult['items'][$file['file_id']];
                        } else {
                            $items[$file['item']]['offers'][$file['offer']][$file['code']] = $arFilesResult['items'][$file['file_id']];
                        }

                    } else {

                        if ( $items[$file['item']][$file['code']][$file['file_id']] ) {
                            $items[$file['item']][$file['code']][$file['file_id']] = $arFilesResult['items'][$file['file_id']];
                        } else {
                            $items[$file['item']][$file['code']] = $arFilesResult['items'][$file['file_id']];
                        }

                    }

                }

                if ( $arParams['show_query_time'] == 'Y' ) echo ( microtime(1) - $time_start ) . ' формирование фалового массива<br />';

            }

            // Формирование url детальной карточки, но пока только 2 уровня

            $arItem = $items[array_key_first( $items )];

            $element_id = $arItem['id'];

            $rsSections = $mysql -> query('
                SELECT
                    sb.*,
                    t1.`id`, t1.`code`, t1.`name`, t1.`parent_id`
                FROM `sections_bind` sb
                INNER JOIN `sections` t1 ON t1.`id` = sb.`section_id`
                WHERE sb.`iblock_id` = ?i AND sb.`element_id` = ?i
                ORDER BY t1.`parent_id`, t1.`sort`
            ',
                $arIblock['id'],
                $element_id
            );

            $arSections = array();

            while ( $row = mysqli_fetch_assoc( $rsSections ) ) {

                if ( empty( $row['parent_id'] ) ) {
                    $arSections[$row['id']] = $row;
                } else {
                    $arSections[$row['parent_id']]['children'][$row['id']] = $row;
                }

            }

            $items[$element_id]['sections'] = $arSections;

            $url = '';

            if ( !empty( $arSections ) ) {
                $arFirstLevel = $arSections[array_key_first( $arSections )];
                $arSecondLevel = !empty( $arFirstLevel['children'] ) ? $arFirstLevel['children'][array_key_first( $arFirstLevel['children'] )] : '';

                $url = '/' . $arFirstLevel['code'] . ( !empty( $arSecondLevel ) ? '/' . $arSecondLevel['code'] : '' ) . '/' . $arItem['code'] . '/';
            }

            $items[$element_id]['detail_page_url'] = $url;

        }

        return array(
            'items' => $Functions -> ResetKeys( $items )
        );
    }

    public function GetOffersProperties ( $arSelect = array(), $arFilter = array(), $arOrder = array() ): array
    {
        global $mysql;
        global $Functions;

        $answer = array();

        if ( !$arFilter['iblock_id'] ) $answer['errors'][] = 'Нет ID Информационного блока';

        if ( !$answer['errors'] ) {

            $arResIblock = $mysql -> queryList( 'SELECT `id`, `code` FROM `iblock` WHERE `is_offer` = ?i', $arFilter['iblock_id'] );

            $arProperties = $mysql -> query('
                SELECT
                   t1.`sort`, t1.`name`, t1.`code`, 
                   t2.`sort` property_sort, t2.`name` property_name, t2.`code` property_code
                FROM `iblock_properties` t1
                LEFT JOIN `iblock_values` t2 ON t2.`property_id` = t1.`id`
                WHERE t1.`iblock_id` = ?i AND t1.`is_choice_offer` = 1
                ORDER BY t2.`sort`
            ',
                $arResIblock[0]['id']
            );

            $items = array();
            $properties = array();

            while ( $row = mysqli_fetch_assoc( $arProperties ) ) {

                $properties[$row['code']]['sort'] = $row['sort'];
                $properties[$row['code']]['name'] = $row['name'];
                $properties[$row['code']]['code'] = $row['code'];

                $properties[$row['code']]['values'][$row['property_code']]['sort'] = $row['property_sort'];
                $properties[$row['code']]['values'][$row['property_code']]['name'] = $row['property_name'];
                $properties[$row['code']]['values'][$row['property_code']]['code'] = $row['property_code'];

            }

            if ( $arFilter['id'] ) {

                $arResProperties = $mysql -> query('
                    SELECT
                        t1.`multiple`, t1.`type`, t1.`code`,
                        t2.`name` value_name, t2.`code` value_code
                    FROM `iblock_properties` t1
                    LEFT JOIN `iblock_values` t2 ON t2.`property_id` = t1.`id`
                    WHERE t1.`iblock_id` = ?i AND t1.`is_choice_offer` = 1'
                    ,
                    $arResIblock[0]['id']
                );

                $arProperties = array(
                    'id' => array(
                        'type' => 'number',
                        'multiple' => 0
                    )
                );

                while ( $row = mysqli_fetch_assoc( $arResProperties ) ) {

                    $arProperties[$row['code']]['type'] = $row['type'];
                    $arProperties[$row['code']]['multiple'] = $row['multiple'];
                    $arProperties[$row['code']]['values'][$row['value_code']]['name'] = $row['value_name'];
                    $arProperties[$row['code']]['values'][$row['value_code']]['code'] = $row['value_code'];

                }

                //$Functions -> Pre( $arProperties );

                $select = array();
                $select_props = array();
                $is_multiple = false;

                foreach ( $arProperties as $key => $arProperty ) {

                    if ( $arProperty['multiple'] ) {

                        if ( !$arSelect || in_array( $key, $arSelect ) ) {

                            $is_multiple = true;
                            $select_props[] = $key;

                        }

                    } else {

                        if ( !$arSelect || in_array( $key, $arSelect ) || $key == 'id' ) $select[] = $mysql -> parse( 't1.?n', $key );

                    }

                }

                //$Functions -> Pre( $select );
                //$Functions -> Pre( $select_props );

                $arResult = $mysql -> query('
                    SELECT
                        ?p?p
                    FROM ?n t1
                    ?p
                    WHERE `product_id` = ?i AND t1.`active` = 1
                ',
                    implode( ', ', $select ),
                    ( ( $is_multiple ) ? ', t2.`id` prop_id, t2.`value` prop_value, t3.`type` prop_type, t3.`code` prop_code' : '' ),
                    'i_' . $arResIblock[0]['code'],
                    ( ( $is_multiple ) ? $mysql -> parse( 'LEFT JOIN ?n t2 ON t2.`element_id` = t1.`id` LEFT JOIN `iblock_properties` t3 ON t3.`id` = t2.`property_id` AND t3.`code` IN ( ?a )', 'i_' . $arResIblock[0]['code'] . '_properties', $select_props ) : '' ),
                    $arFilter['id']
                );

                $offers = array();

                while ( $row = mysqli_fetch_assoc( $arResult ) ) {

                    //$Functions -> Pre( $row );

                    for ( $i = 0; $i < count( $select ); $i++ ) {

                        $prop = explode( '`', $select[$i] );

                        $key = $prop[1];

                        if ( $arProperties[$key]['type'] == 'list' || $arProperties[$key]['type'] == 'choice' ) {

                            $offers[$row['id']][$key]['name'] = $arProperties[$key]['values'][$row[$key]]['name'];
                            $offers[$row['id']][$key]['code'] = $arProperties[$key]['values'][$row[$key]]['code'];

                        }

                    }

                    if ( $row['prop_id'] AND $row['prop_code'] ) {

                        if ( $row['prop_type'] == 'list' || $row['prop_type'] == 'choice' ) {

                            $offers[$row['id']][$row['prop_code']][$row['prop_value']]['name'] = $arProperties[$row['prop_code']]['values'][$row['prop_value']]['name'];
                            $offers[$row['id']][$row['prop_code']][$row['prop_value']]['code'] = $arProperties[$row['prop_code']]['values'][$row['prop_value']]['code'];

                        }

                    }

                }

                $arOffers = $Functions -> ResetKeys( $offers );

                for ( $i = 0; $i < count( $arOffers ); $i++ ) {

                    $arOffer = $arOffers[$i];

                    foreach ( $arOffer as $key => $prop ) {

                        if ( empty( $prop['code'] ) && !$arProperties[$key]['multiple'] ) continue;

                        $items[$key]['sort'] = $properties[$key]['sort'];
                        $items[$key]['name'] = $properties[$key]['name'];
                        $items[$key]['code'] = $properties[$key]['code'];

                        if ( $arProperties[$key]['multiple'] ) {

                            foreach ( $prop as $key_val => $val ) {

                                $items[$key]['values'][$key_val]['sort'] = $properties[$key]['values'][$key_val]['sort'];
                                $items[$key]['values'][$key_val]['name'] = $properties[$key]['values'][$key_val]['name'];
                                $items[$key]['values'][$key_val]['code'] = $properties[$key]['values'][$key_val]['code'];

                            }

                        } else {

                            $items[$key]['values'][$prop['code']]['sort'] = $properties[$key]['values'][$prop['code']]['sort'];
                            $items[$key]['values'][$prop['code']]['name'] = $properties[$key]['values'][$prop['code']]['name'];
                            $items[$key]['values'][$prop['code']]['code'] = $properties[$key]['values'][$prop['code']]['code'];

                        }

                    }

                }

                foreach ( $items as $key => $item ) {

                    uasort( $items[$key]['values'], function( $a, $b ) {

                        return ( $a['sort'] - $b['sort'] );

                    });

                }

                uasort( $items, function( $a, $b ) {

                    return ( $a['sort'] - $b['sort'] );

                });

            }

            $answer['items'] = ( ( empty( $items ) ) ? $properties : $items );

        }

        return $answer;
    }

    public function GetPrice ( $arSelect = array(), $arFilter = array(), $arOrder = array() )
    {
        global $mysql;
        global $Functions;

        $arResIblock = $this -> classIblock -> GetList(
            array(),
            array(
                'id' => $arFilter['iblock_id']
            ),
            array(),
            array()
        );

        $arIblock = $arResIblock['items'][0];

        $arResult = $mysql -> query('
            SELECT
                `id`, `active`, `sort`, `type_id`, `type_main`, `price`, `discount`, `discount_type`, `quantity_from`, `quantity_to`, `currency`
            FROM ?n
            WHERE `product_id` = ?i
            ORDER BY `sort`, `type_id`, `type_main` DESC
        ',
            'i_' . $arIblock['code'] . '_prices',
            $arFilter['product_id']
        );

        $items = array();

        while ( $row = mysqli_fetch_assoc( $arResult ) ) {

            $items[$row['id']]['id'] = $row['id'];
            $items[$row['id']]['active'] = $row['active'];
            $items[$row['id']]['sort'] = $row['sort'];
            $items[$row['id']]['type_id'] = $row['type_id'];
            $items[$row['id']]['type_main'] = $row['type_main'];
            $items[$row['id']]['price'] = $row['price'];
            $items[$row['id']]['discount'] = $row['discount'];
            $items[$row['id']]['discount_type'] = $row['discount_type'];
            $items[$row['id']]['quantity_from'] = $row['quantity_from'];
            $items[$row['id']]['quantity_to'] = $row['quantity_to'];
            $items[$row['id']]['currency'] = $row['currency'];

        }

        return array(
            'items' => $Functions -> ResetKeys( $items )
        );

    }

    public function GetProperties ( $arSelect, $arFilter, $arOrder )
    {
        global $mysql;
        global $Functions;

        $answer = array();
        $where = array();

        if ( $arFilter['iblock_id'] ) {
            $where[] = $mysql -> parse( 't1.`id` = ?i', $arFilter['iblock_id'] );
        } else {
            $answer['errors'][] = array( 'text' => 'Нет ID Информационного блока' );
        }

        if ( $arSelect ) $where[] = $mysql -> parse( 't2.`code` IN (?a)', $arSelect );

        //$Functions -> Pre( $where );

        if ( !$answer['errors'] ) {

            $arResult = $mysql -> query('
                SELECT
                    t1.`name`,
                    t2.`require_prop`, t2.`multiple`, t2.`name` prop_name, t2.`type`, t2.`code`,
                    t3.`id` val_id, t3.`name` val_name, t3.`code` val_code
                FROM `iblock` t1
                LEFT JOIN `iblock_properties` t2 ON t2.`iblock_id` = t1.`id`
                LEFT JOIN `iblock_values` t3 ON t3.`property_id` = t2.`id`
                ?p
                ORDER BY t2.`sort`, t3.`sort`
            ',
                ( ( $where ) ? 'WHERE ' . implode( ' AND ', $where ) : '' )
            );

            $iblock_name = '';
            $fields = array();

            while ( $row = mysqli_fetch_assoc( $arResult ) ) {

                $iblock_name = $row['name'];

                $fields[$row['code']]['require'] = $row['require_prop'];
                $fields[$row['code']]['multiple'] = $row['multiple'];
                $fields[$row['code']]['name'] = $row['prop_name'];
                $fields[$row['code']]['type'] = $row['type'];
                $fields[$row['code']]['code'] = $row['code'];

                if ( $row['val_code'] ) {

                    $fields[$row['code']]['values'][$row['val_code']]['id'] = $row['val_id'];
                    $fields[$row['code']]['values'][$row['val_code']]['name'] = $row['val_name'];

                }

            }

            $answer = array(
                'iblock_id' => $arFilter['iblock_id'],
                'iblock_name' => $iblock_name,
                'properties' => ( ( $fields ) ? $fields : '' )
            );

        }

        return $answer;
    }

    public function GetList ( $arSelect = array(), $arFilter = array(), $arNav = array(), $arOrder = array(), $arParams = array() ): array
    {
        global $mysql;
        global $Functions;

        if ( !$arFilter['iblock_id'] ) return false;

        $arResIblock = $this -> classIblock -> GetList(
            array(),
            array(
                'id' => $arFilter['iblock_id']
            ),
            array(),
            array()
        );

        $arIblock = $arResIblock['items'][0];
        $table_name = ( ( $arIblock['system'] ) ? '' : 'i_' ) . $arIblock['code'];

        $arProperties = array(
            'id' => array(
                'type' => 'number',
                'multiple' => 0
            ),
            'active' => array(
                'type' => 'number',
                'multiple' => 0
            ),
            'sort' => array(
                'type' => 'number',
                'multiple' => 0
            ),
            'date_create' => array(
                'type' => 'datetime',
                'multiple' => 0
            ),
            'date_update' => array(
                'type' => 'datetime',
                'multiple' => 0
            )
        );

        if ( $arIblock['is_offer'] ) {

            $arProperties = array_merge(
                $arProperties,
                array(
                    'product_id' => array(
                        'type' => 'number',
                        'multiple' => 0
                    )
                )
            );

        }

        $arProperties = array_merge(
            $arProperties,
            $arIblock['properties']
        );

        $join_price = '';
        $arCurrencyRate = array();

        if ( $arIblock['is_catalog'] ) {

            $arProperties = array_merge(
                $arProperties,
                array(
                    'price' => array(
                        'type' => 'number_dot',
                        'multiple' => 0,
                        'is_catalog' => 1
                    ),
                    'discount' => array(
                        'type' => 'number_dot',
                        'multiple' => 0,
                        'is_catalog' => 1
                    ),
                    'discount_price' => array(
                        'type' => 'number_dot',
                        'multiple' => 0,
                        'is_catalog' => 1
                    ),
                    'currency' => array(
                        'type' => 'number',
                        'multiple' => 0,
                        'is_catalog' => 1
                    )
                )
            );

            $arTypePrice = $this -> classShop -> GetTypePrice( array( 'id' ), array( 'main' => 'yes' ) );

            if ( $arParams['currency'] ) {
                $currency_id = $arParams['currency'];
            } else {
                $arCurrency = $this -> classShop -> GetCurrency( array( 'id' ), array( 'base' => 'yes' ) );
                $currency_id = $arCurrency['items'][0]['id'];
            }

            if ( $arParams['currency_rate'] && $arParams['currency_rate'] != $currency_id ) {

                $arResCurrencyRate = $this -> classShop -> GetCurrencyRate( array( 'currency', 'nominal', 'rate' ), array( 'id' => $arParams['currency_rate'] ) );
                $arCurrencyRate = $arResCurrencyRate['items'][0];

            }

            $join_price = $mysql -> parse( 'LEFT JOIN ?n p1 ON p1.`product_id` = t1.`id` AND p1.`type_id` = ?i AND p1.`type_main` = 1 AND p1.`currency` = ?i', $table_name . '_prices', $arTypePrice['items'][0]['id'], $currency_id );

        }

        //$Functions -> Pre( $join_price );
        //$Functions -> Pre( $arProperties );

        $select = array();
        $select_props = array();
        $where = array();
        $where_props = array();
        $where_join = array();
        $limit = ( ( $arNav['count_on_page'] ) ? $arNav['count_on_page'] : 50 );
        $offset = ( ( $arNav['page'] ) ? $arNav['page'] : 1 );
        $order = array();
        $is_multiple = false;

        foreach ( $arProperties as $key => $arProperty ) {

            if ( $arProperty['multiple'] ) {

                $is_multiple = true;
                if ( !$arSelect || in_array( $key, $arSelect ) ) $select_props[] = $key;

            } else {

                if ( !$arSelect || in_array( $key, $arSelect ) || $key == 'id' ) $select[] = $mysql -> parse( '?p.?n', ( ( $arProperty['is_catalog'] ) ? 'p1' : 't1' ), $key );

            }

        }

        //$Functions -> Pre( $select );
        //$Functions -> Pre( $arFilter );

        if ( $arFilter ) {

            $count = 0;

            foreach ( $arFilter as $key => $value ) {

                $condition = '';

                $arCondition = $this -> DefiningCondition( $key );

                if ( $arCondition['condition'] ) {

                    $condition = $arCondition['condition'];
                    $key = $arCondition['key'];

                }

                if ( $condition == '%' ) {

                    $condition = 'LIKE';
                    $value = '%' . $value . '%';

                }

                if ( $arProperties[$key] && ( !empty( $value ) || $value === 0 ) ) {

                    if ( $arProperties[$key]['multiple'] ) {

                        if ( is_array( $arFilter[$key] ) ) {

                            $where_props[] = $mysql -> parse('
                                JOIN (
                                    SELECT t1.`element_id`
                                    FROM ?n t1
                                    WHERE t1.`property_id` = ?i AND t1.value IN ( ?a )
                                ) JT?i ON JT?i.`element_id` = t1.`id`
                            ',
                                $table_name . '_properties',
                                $arProperties[$key]['id'],
                                $arFilter[$key],
                                $count,
                                $count
                            );

                            $count++;

                        } else {

                            switch ( $arProperties[$key]['type'] ) {

                                case 'bind':
                                case 'bind_sect':
                                case 'file':
                                    $where[] = $mysql -> parse( 't2.`property_id` = ?i AND t2.`value` = ?i', $arProperties[$key]['id'], $value );
                                    break;

                                case 'choice':
                                case 'list':
                                    $where[] = $mysql -> parse( 't2.`property_id` = ?i AND t2.`value` = ?s', $arProperties[$key]['id'], $value );
                                    break;

                                case 'datetime':
                                    $where[] = $mysql -> parse( 't1.?n ?p ?s', $key, ( ( $condition ) ? $condition : '=' ), $value );
                                    break;

                                case 'number':
                                case 'number_dot':
                                    $where[] = $mysql -> parse( 't2.`property_id` = ?i AND t2.`value` ?p ?i', $arProperties[$key]['id'], ( ( $condition ) ? $condition : '=' ), $value );
                                    break;

                                case 'string':
                                case 'text':
                                    $where[] = $mysql -> parse( 't2.`property_id` = ?i AND t2.`value` ?p ?s', $arProperties[$key]['id'], ( ( $condition ) ? $condition : '=' ), $value );
                                    break;

                            }

                        }

                    } else {

                        if ( is_array( $arFilter[$key] ) ) {

                            $where_props[] = $mysql -> parse('
                                JOIN (
                                    SELECT t1.`id`
                                    FROM ?n t1
                                    WHERE t1.?n IN (?a)
                                ) JT?i ON JT?i.`id` = t1.`id`
                            ',
                                $table_name,
                                $key,
                                $arFilter[$key],
                                $count,
                                $count
                            );

                            $count++;

                        } else {

                            //$Functions -> Pre( $arProperties[$key] );
                            //$Functions -> Pre( $value );

                            switch ( $arProperties[$key]['type'] ) {

                                case 'bind':
                                case 'bind_sect':
                                case 'file':
                                    $where[] = $mysql -> parse( 't1.?n = ?i', $key, $value );
                                    break;

                                case 'choice':
                                case 'list':
                                    $where[] = $mysql -> parse( 't1.?n = ?s', $key, $value );
                                    break;

                                case 'datetime':
                                    $where[] = $mysql -> parse( 't1.?n ?p ?s', $key, ( ( $condition ) ? $condition : '=' ), $value );
                                    break;

                                case 'number':
                                case 'number_dot':
                                    $where[] = $mysql -> parse( '?p.?n ?p ?i', ( ( $arProperties[$key]['is_catalog'] ) ? 'p1' : 't1' ), $key, ( ( $condition ) ? $condition : '=' ), $value );
                                    break;

                                case 'string':
                                case 'text':
                                    $where[] = $mysql -> parse( 't1.?n ?p ?s', $key, ( ( $condition ) ? $condition : '=' ), $value );
                                    break;

                            }

                        }

                    }

                } else {

                    if ( !$value ) continue;

                    switch ( $key ) {

                        case 'section_id':
                        case 'section_code':

                            $section_ids = array();

                            if ( $key == 'section_code' ) {

                                $arSectionId = $mysql -> queryList('
                                    SELECT `id`
                                    FROM `sections`
                                    WHERE `iblock_id` = ?i AND `code` = ?s
                                ',
                                    $arFilter['iblock_id'],
                                    $value
                                );
                                $arFilter['section_id'] = $arSectionId[0]['id'];

                            }

                            if ( $arParams['section_children'] == 'Y' ) $section_ids = array_merge( array( $arFilter['section_id'] ), $this -> GetSectionChildren( $arFilter['section_id'] ) );

                            if ( $section_ids ) {
                                $where[] = $mysql -> parse( 'S.`section_id` IN (?a)', $section_ids );
                            } else {
                                $where[] = $mysql -> parse( 'S.`section_id` = ?i', $arFilter['section_id'] );
                            }

                            $where_join[] = $mysql -> parse( 'LEFT JOIN `sections_bind` S ON S.`iblock_id` = ?i AND S.`element_id` = t1.`id`', $arFilter['iblock_id'] );

                            break;

                    }

                }

            }

        }

        //$Functions -> Pre( $where );
        //$Functions -> Pre( $where_props );

        if ( $arOrder ) {

            //$Functions -> Pre( $arOrder );

            foreach ( $arOrder as $key => $value ) {

                if ( $arProperties[$key] ) {

                    $order[] = $mysql -> parse( '?p.?n ?p', ( ( $arProperties[$key]['is_catalog'] ) ? 'p1' : 't1' ), $key, ( ( $value ) ? $value : 'asc' ) );

                }

            }

        }

        //$Functions -> Pre( $order );

        $time_start = microtime(1);

        if ( !$where && !$where_props ) {

            $arCount = $mysql -> query('
                SELECT
                    count( t1.`id` )
                FROM ?n t1
                ?p
                GROUP BY t1.`id`
            ',
                $table_name,
                ( ( $where_join ) ? implode( "\n", $where_join ) : '' )
            );

        } else {

            $arCount = $mysql -> query('
                SELECT
                    count( t1.`id` )
                FROM ?n t1
                ?p
                ?p
                ?p
                ?p
                ?p
                GROUP BY t1.`id`
            ',
                $table_name,
                ( ( $join_price ) ? $join_price : '' ),
                ( ( $is_multiple ) ? $mysql -> parse( 'LEFT JOIN ?n t2 ON t2.`element_id` = t1.`id` LEFT JOIN `iblock_properties` t3 ON t3.`id` = t2.`property_id`', $table_name . '_properties' ) : '' ),
                ( ( $where_join ) ? implode( "\n", $where_join ) : '' ),
                ( ( $where_props ) ? implode( "\n", $where_props ) : '' ),
                ( ( $where ) ? 'WHERE ' . implode( ' AND ', $where ) : '' )
            );

        }

        $count = $arCount -> num_rows;

        if ( $arParams['show_query_time'] == 'Y' ) echo ( microtime(1) - $time_start ) . ' запрос на количество всего: ' . $count . '<br />';

        //$Functions -> Pre( $select );
        //$Functions -> Pre( $select_props );

        $time_start = microtime(1);

        $arResult = $mysql -> query('
            SELECT
                ?p?p
            FROM ?n t1
            JOIN (
                SELECT
                    t1.`id`
                FROM ?n t1
                ?p
                ?p
                ?p
                ?p
                ?p
                GROUP BY t1.`id`
                ?p
                LIMIT ?i OFFSET ?i
            ) J ON J.`id` = t1.`id`
            ?p
            ?p
            ?p
        ',
            implode( ', ', $select ),
            ( ( $is_multiple ) ? ', t2.`id` prop_id, t2.`value` prop_value, t3.`type` prop_type, t3.`code` prop_code' : '' ),
            $table_name,

            $table_name,
            ( ( $join_price ) ? $join_price : '' ),
            ( ( $is_multiple ) ? $mysql -> parse( 'LEFT JOIN ?n t2 ON t2.`element_id` = t1.`id` LEFT JOIN `iblock_properties` t3 ON t3.`id` = t2.`property_id`', $table_name . '_properties' ) : '' ),
            ( ( $where_join ) ? implode( "\n", $where_join ) : '' ),
            ( ( $where_props ) ? implode( "\n", $where_props ) : '' ),
            ( ( $where ) ? 'WHERE ' . implode( ' AND ', $where ) : '' ),
            ( ( $order ) ? 'ORDER BY ' . implode( ', ', $order ) : '' ),
            $limit,
            ( $offset * $limit - $limit ),

            ( ( $join_price ) ? $join_price : '' ),
            ( ( $is_multiple ) ? $mysql -> parse( 'LEFT JOIN ?n t2 ON t2.`element_id` = t1.`id` LEFT JOIN `iblock_properties` t3 ON t3.`id` = t2.`property_id`?p', $table_name . '_properties', ( ( $select_props ) ? $mysql -> parse( ' AND t3.`code` IN (?a)', $select_props ) : ' AND t3.`code` = ""' ) ) : '' ),
            ( ( $order ) ? 'ORDER BY ' . implode( ', ', $order ) : '' )
        );

        if ( $arParams['show_query_time'] == 'Y' ) echo ( microtime(1) - $time_start ) . ' Основной запрос<br>';

        $items = array();
        $arFiles = array();
        $arFilesIds = array();
        $arElementIds = array();

        $time_start = microtime(1);

        while ( $row = mysqli_fetch_assoc( $arResult ) ) {

            if ( !in_array( $row['id'], $arElementIds ) ) $arElementIds[] = $row['id'];

            for ( $i = 0; $i < count( $select ); $i++ ) {

                $prop = explode( '`', $select[$i] );

                $key = $prop[1];

                if ( $arProperties[$key]['type'] == 'list' || $arProperties[$key]['type'] == 'choice' ) {

                    $items[$row['id']][$key]['name'] = $arProperties[$key]['values'][$row[$key]]['name'];
                    $items[$row['id']][$key]['code'] = $arProperties[$key]['values'][$row[$key]]['code'];

                    if ( $arProperties[$key]['values'][$row[$key]]['file'] ) $items[$row['id']][$key]['file'] = $arProperties[$key]['values'][$row[$key]]['file'];

                } else if ( $arProperties[$key]['type'] == 'file' ) {

                    $arFiles[$row[$key]] = array(
                        'item' => $row['id'],
                        'code' => $key,
                        'file_id' => $row[$key]
                    );

                    if ( !in_array( $row[$key], $arFilesIds ) ) $arFilesIds[] = $row[$key];

                    $items[$row['id']][$key] = $row[$key];

                } else if ( $arProperties[$key]['type'] == 'number_dot' ) {

                    if ( ( $key == 'price' || $key == 'discount' || $key == 'discount_price' ) && $arCurrencyRate ) {
                        $items[$row['id']][$key] = ( $row[$key] ) ? round( $row[$key] * $arCurrencyRate['rate'] / $arCurrencyRate['nominal'], 2 ) : '';
                    } else {
                        $items[$row['id']][$key] = ( $row[$key] ) ? round( $row[$key], 2 ) : '';
                    }

                } else {
                    $items[$row['id']][$key] = $row[$key];
                }

            }

            if ( $row['prop_id'] AND $row['prop_code'] ) {

                if ( $row['prop_type'] == 'list' || $row['prop_type'] == 'choice' ) {

                    $items[$row['id']][$row['prop_code']][$row['prop_id']]['name'] = $arProperties[$row['prop_code']]['values'][$row['prop_value']]['name'];
                    $items[$row['id']][$row['prop_code']][$row['prop_id']]['code'] = $arProperties[$row['prop_code']]['values'][$row['prop_value']]['code'];

                    if ( $arProperties[$row['prop_code']]['values'][$row['prop_value']]['file'] ) {
                        $items[$row['id']][$row['prop_code']][$row['prop_id']]['file'] = $arProperties[$row['prop_code']]['values'][$row['prop_value']]['file'];
                    }

                } else if ( $row['prop_type'] == 'file' ) {

                    $arFiles[$row['prop_value']] = array(
                        'item' => $row['id'],
                        'code' => $row['prop_code'],
                        'file_id' => $row['prop_value']
                    );

                    if ( !in_array( $row['prop_value'], $arFilesIds ) ) $arFilesIds[] = $row['prop_value'];

                    $items[$row['id']][$row['prop_code']][$row['prop_value']]['value'] = $row['prop_value'];

                } else {

                    $items[$row['id']][$row['prop_code']][$row['prop_id']]['id'] = $row['prop_id'];
                    $items[$row['id']][$row['prop_code']][$row['prop_id']]['value'] = $row['prop_value'];

                }

            }

        }

        //$Functions -> Pre( $arFilesIds );
        //$Functions -> Pre( $arFiles );
        //$Functions -> Pre( $items );

        if ( $arFilesIds ) {

            $arFilesResult = $this -> classFiles -> GetFiles(
                array(),
                array(
                    'id' => $arFilesIds
                ),
                array(
                    'count_on_page' => 999
                ),
                array(),
                array(
                    'reset_keys' => 'N'
                )
            );

            //$Functions -> Pre( $arFilesResult );

            foreach ( $arFiles as $key => $file ) {

                //$Functions -> Pre( $items[$file['item']] );

                if ( $items[$file['item']][$file['code']][$file['file_id']] ) {
                    $items[$file['item']][$file['code']][$file['file_id']] = $arFilesResult['items'][$file['file_id']];
                } else {
                    $items[$file['item']][$file['code']] = $arFilesResult['items'][$file['file_id']];
                }

            }

        }

        $arResSections = $mysql -> query('
            SELECT
                t1.`section_id`, t1.`element_id`,
                t2.`name`, t2.`code`, t2.`parent_id`
            FROM `sections_bind` t1
            INNER JOIN `sections` t2 ON t2.`id` = t1.`section_id`
            WHERE t1.`iblock_id` = ?i AND t1.`element_id` IN ( ?a )
        ',
            $arIblock['id'],
            $arElementIds
        );

        while ( $row = mysqli_fetch_assoc( $arResSections ) ) {

            $items[$row['element_id']]['sections'][$row['section_id']] = array(
                'id' => $row['section_id'],
                'name' => $row['name'],
                'code' => $row['code'],
                'parent_id' => $row['parent_id']
            );

        }

        if ( $arParams['show_query_time'] == 'Y' ) echo ( microtime(1) - $time_start ) . ' Формирование массива<br />';

        return array(
            'iblock_name' => $arIblock['name'],
            'items_count' => $count,
            'page_current' => $offset,
            'pages_count' => intval( ( $count - 1 ) / $limit ) + 1,
            'items' => $Functions -> ResetKeys( $items )
        );

    }

    public function GetListWithOffers ( $arSelect = array(), $arFilter = array(), $arNav = array(), $arOrder = array(), $arParams = array() ): array
    {
        global $mysql;
        global $Functions;

        if ( !$arFilter['iblock_id'] ) return false;

        $arResIblock = $this -> classIblock -> GetList(
            array(),
            array(
                'id' => $arFilter['iblock_id']
            ),
            array(),
            array()
        );

        $arIblock = $arResIblock['items'][0];
        $table_name = ( ( $arIblock['system'] ) ? '' : 'i_' ) . $arIblock['code'];

        $arProperties = array(
            'id' => array(
                'type' => 'number',
                'multiple' => 0
            ),
            'active' => array(
                'type' => 'number',
                'multiple' => 0
            ),
            'sort' => array(
                'type' => 'number',
                'multiple' => 0
            ),
            'date_create' => array(
                'type' => 'datetime',
                'multiple' => 0
            ),
            'date_update' => array(
                'type' => 'datetime',
                'multiple' => 0
            )
        );

        $arProperties = array_merge(
            $arProperties,
            $arIblock['properties']
        );

        $arResIblockOffers = $this -> classIblock -> GetList(
            array(),
            array(
                'is_offer' => $arIblock['id']
            ),
            array(),
            array()
        );

        $arIblockOffers = $arResIblockOffers['items'][0];
        $table_name_offers = ( ( $arIblockOffers['system'] ) ? '' : 'i_' ) . $arIblockOffers['code'];

        $arPropertiesOffers = array(
            'id' => array(
                'type' => 'number',
                'multiple' => 0
            ),
            'active' => array(
                'type' => 'number',
                'multiple' => 0
            ),
            'sort' => array(
                'type' => 'number',
                'multiple' => 0
            ),
            'date_create' => array(
                'type' => 'datetime',
                'multiple' => 0
            ),
            'date_update' => array(
                'type' => 'datetime',
                'multiple' => 0
            )
        );

        if ( $arIblockOffers['is_offer'] ) {

            $arPropertiesOffers = array_merge(
                $arPropertiesOffers,
                array(
                    'product_id' => array(
                        'type' => 'number',
                        'multiple' => 0
                    )
                )
            );

        }

        $arPropertiesOffers = array_merge(
            $arPropertiesOffers,
            $arIblockOffers['properties']
        );

        $join_price = '';
        $arCurrencyRate = array();

        if ( $arIblockOffers['is_catalog'] ) {

            $arPropertiesOffers = array_merge(
                $arPropertiesOffers,
                array(
                    'price' => array(
                        'type' => 'number_dot',
                        'multiple' => 0,
                        'is_catalog' => 1
                    ),
                    'discount' => array(
                        'type' => 'number_dot',
                        'multiple' => 0,
                        'is_catalog' => 1
                    ),
                    'discount_price' => array(
                        'type' => 'number_dot',
                        'multiple' => 0,
                        'is_catalog' => 1
                    ),
                    'discount_type' => array(
                        'type' => 'number',
                        'multiple' => 0,
                        'is_catalog' => 1
                    ),
                    'currency' => array(
                        'type' => 'number',
                        'multiple' => 0,
                        'is_catalog' => 1
                    )
                )
            );

            $arTypePrice = $this -> classShop -> GetTypePrice( array( 'id' ), array( 'main' => 'yes' ) );

            if ( $arParams['currency'] ) {
                $currency_id = $arParams['currency'];
            } else {
                $arCurrency = $this -> classShop -> GetCurrency( array( 'id' ), array( 'base' => 'yes' ) );
                $currency_id = $arCurrency['items'][0]['id'];
            }

            if ( $arParams['currency_rate'] && $arParams['currency_rate'] != $currency_id ) {

                $arResCurrencyRate = $this -> classShop -> GetCurrencyRate( array( 'currency', 'nominal', 'rate' ), array( 'id' => $arParams['currency_rate'] ) );
                $arCurrencyRate = $arResCurrencyRate['items'][0];

            }

            $join_price = $mysql -> parse( 'LEFT JOIN ?n p1 ON p1.`product_id` = o1.`id` AND p1.`type_id` = ?i AND p1.`type_main` = 1 AND p1.`currency` = ?i', $table_name_offers . '_prices', $arTypePrice['items'][0]['id'], $currency_id );

        }

        //$Functions -> Pre( $arProperties );
        //$Functions -> Pre( $arPropertiesOffers );

        $select = array();
        $select_props = array();
        $is_multiple = false;

        foreach ( $arProperties as $key => $arProperty ) {

            if ( $arProperty['multiple'] ) {

                if ( !$arSelect || in_array( $key, $arSelect ) ) {

                    $is_multiple = true;
                    $select_props[] = $key;

                }

            } else {

                if ( !$arSelect || in_array( $key, $arSelect ) || $key == 'id' ) $select[] = $mysql -> parse( 't1.?n', $key );

            }

        }

        //$Functions -> Pre( $select );
        //$Functions -> Pre( $select_props );

        $select_offers = array();
        $select_props_offers = array();
        $is_multiple_offers = false;
        $is_select_price = false;

        foreach ( $arPropertiesOffers as $key => $arProperty ) {

            if ( $arProperty['multiple'] ) {

                if ( !$arSelect || in_array( 'offer_' . $key, $arSelect ) ) {

                    $is_multiple_offers = true;
                    $select_props_offers[] = $key;

                }

            } else {

                if ( !$arSelect || in_array( 'offer_price', $arSelect ) || in_array( 'offer_discount', $arSelect ) || in_array( 'offer_discount_price', $arSelect ) ) $is_select_price = true;
                if ( !$arSelect || in_array( 'offer_' . $key, $arSelect ) || $key == 'id' || $key == 'product_id' ) $select_offers[] = $mysql -> parse( '?p.?n offer_?p', ( ( $arProperty['is_catalog'] ) ? 'p1' : 'o1' ), $key, $key );

            }

        }

        //$Functions -> Pre( $is_select_price );
        //$Functions -> Pre( $select_offers );
        //$Functions -> Pre( $select_props_offers );

        //$Functions -> Pre( $arFilter );

        $where = array();
        $where_multi = array();
        $is_where = false;
        $is_where_multi = false;

        $where_offers = array();
        $where_multi_offers = array();
        $is_where_offers = false;
        $is_where_multi_offers = false;

        $is_where_price = false;

        $is_where_section = false;

        $join_section = $mysql -> parse( 'LEFT JOIN `sections_bind` S ON S.`iblock_id` = ?i AND S.`element_id` = t1.`id`', $arFilter['iblock_id'] );

        if ( $arFilter ) {

            $count = 0;
            $count_offers = 0;

            foreach ( $arFilter as $key => $value ) {

                $condition = '';

                $arCondition = $this -> DefiningCondition( $key );

                if ( $arCondition['condition'] ) {

                    $condition = $arCondition['condition'];
                    $key = $arCondition['key'];

                }

                if ( $condition == '%' ) {

                    $condition = 'LIKE';
                    $value = '%' . $value . '%';

                }

                if ( $arProperties[$key] && $value ) {

                    if ( $arProperties[$key]['multiple'] ) {

                        $is_where_multi = true;

                        if ( is_array( $value ) ) {

                            $where_multi[] = $mysql -> parse('
                                JOIN (
                                    SELECT
                                        t1.`element_id`
                                    FROM ?n t1
                                    WHERE t1.`property_id` = ?i AND t1.value IN ( ?a )
                                ) JT?i ON JT?i.`element_id` = t1.`id`
                            ',
                                $table_name . '_properties',
                                $arProperties[$key]['id'],
                                $arFilter[$key],
                                $count,
                                $count
                            );

                            $count++;

                        } else {

                            $where_multi[] = $mysql -> parse('
                                JOIN (
                                    SELECT
                                        t1.`element_id`
                                    FROM ?n t1
                                    WHERE t1.`property_id` = ?i AND t1.value = ?s
                                ) JT?i ON JT?i.`element_id` = t1.`id`
                            ',
                                $table_name . '_properties',
                                $arProperties[$key]['id'],
                                $arFilter[$key],
                                $count,
                                $count
                            );

                            $count++;

                        }

                    } else {

                        $is_where = true;

                        if ( is_array( $value ) ) {

                            $where[] = $mysql -> parse( 't1.?n IN (?a)', $key, $value );

                        } else {

                            switch ( $arProperties[$key]['type'] ) {

                                case 'bind':
                                case 'file':
                                    $where[] = $mysql -> parse( 't1.?n = ?i', $key, $value );
                                    break;

                                case 'choice':
                                case 'list':
                                    $where[] = $mysql -> parse( 't1.?n = ?s', $key, $value );
                                    break;

                                case 'number':
                                    $where[] = $mysql -> parse( 't1.?n ?p ?i', $key, ( ( $condition ) ? $condition : '=' ), $value );
                                    break;

                                case 'number_dot':
                                case 'string':
                                case 'text':
                                    $where[] = $mysql -> parse( 't1.?n ?p ?s', $key, ( ( $condition ) ? $condition : '=' ), $value );
                                    break;

                            }

                        }

                    }

                } else {

                    switch ( $key ) {

                        case 'section_id':
                        case 'section_code':

                            $is_where_section = true;
                            $section_ids = array();

                            if ( $key == 'section_code' ) {

                                $arSectionId = $mysql -> queryList( 'SELECT `id` FROM `sections` WHERE `code` = ?s AND `iblock_id` = ?i', $value, $arFilter['iblock_id'] );
                                $arFilter['section_id'] = $arSectionId[0]['id'];

                            }

                            if ( $arParams['section_children'] == 'Y' ) $section_ids = array_merge( array( $arFilter['section_id'] ), $this -> GetSectionChildren( $arFilter['section_id'] ) );

                            if ( $section_ids ) {
                                $where[] = $mysql -> parse( 'S.`section_id` IN (?a)', $section_ids );
                            } else {
                                $where[] = $mysql -> parse( 'S.`section_id` = ?i', $arFilter['section_id'] );
                            }

                            break;

                    }

                }

                if ( preg_match( '/offer_/', $key ) && $arPropertiesOffers[preg_replace( '/^offer_/', '', $key )] && $value ) {

                    if ( preg_match( '/offer_/', $key ) ) $key = preg_replace( '/^offer_/', '', $key );

                    if ( $arPropertiesOffers[$key]['multiple'] ) {

                        $is_where_multi_offers = true;

                        if ( is_array( $value ) ) {

                            $where_multi_offers[] = $mysql -> parse('
                                JOIN (
                                    SELECT o1.`element_id`
                                    FROM ?n o1
                                    WHERE o1.`property_id` = ?i AND o1.value IN ( ?a )
                                ) JO?i ON JO?i.`element_id` = o1.`id`
                            ',
                                $table_name_offers . '_properties',
                                $arPropertiesOffers[$key]['id'],
                                $arFilter['offer_' . $key],
                                $count_offers,
                                $count_offers
                            );

                            $count_offers++;

                        } else {

                            $where_multi_offers[] = $mysql -> parse('
                                JOIN (
                                    SELECT o1.`element_id`
                                    FROM ?n o1
                                    WHERE o1.`property_id` = ?i AND o1.value = ?s
                                ) JO?i ON JO?i.`element_id` = o1.`id`
                            ',
                                $table_name_offers . '_properties',
                                $arPropertiesOffers[$key]['id'],
                                $arFilter['offer_' . $key],
                                $count_offers,
                                $count_offers
                            );

                            $count_offers++;

                        }

                    } else {

                        $is_where_offers = true;

                        if ( is_array( $value ) ) {

                            $where_offers[] = $mysql -> parse( 'o1.?n IN (?a)', $key, $value );

                        } else {

                            if ( $arPropertiesOffers[$key]['is_catalog'] ) $is_where_price = true;

                            switch ( $arPropertiesOffers[$key]['type'] ) {

                                case 'bind':
                                case 'file':
                                    $where_offers[] = $mysql -> parse( 'o1.?n = ?i', $key, $value );
                                    break;

                                case 'choice':
                                case 'list':
                                    $where_offers[] = $mysql -> parse( 'o1.?n = ?s', $key, $value );
                                    break;

                                case 'number':
                                    $where_offers[] = $mysql -> parse( 'o1.?n ?p ?i', $key, ( ( $condition ) ? $condition : '=' ), $value );
                                    break;

                                case 'number_dot':
                                case 'string':
                                case 'text':
                                    $where_offers[] = $mysql -> parse( '?p.?n ?p ?s',( ( $arPropertiesOffers[$key]['is_catalog'] ) ? 'p1' : 'o1' ), $key, ( ( $condition ) ? $condition : '=' ), $value );
                                    break;

                            }

                        }

                    }

                }

            }

        }

        //$Functions -> Pre( $where );
        //$Functions -> Pre( $is_where );
        //$Functions -> Pre( $is_where_offers );
        //$Functions -> Pre( $is_where_multi_offers );

        $limit = ( ( $arNav['count_on_page'] ) ? $arNav['count_on_page'] : 50 );
        $offset = ( ( $arNav['page'] ) ? $arNav['page'] : 1 ) * $limit - $limit;

        $order = array();
        $order_offers = array();
        $order_price = array();
        $is_order = false;
        $is_order_price = false;

        //$Functions -> Pre( $arOrder );

        if ( $arOrder ) {

            foreach ( $arOrder as $key => $value ) {

                if ( $arProperties[$key] ) {

                    $order[] = $mysql -> parse( 't1.?n ?p', $key, ( ( $value ) ? $value : 'asc' ) );
                    $is_order = true;

                }

                if ( preg_match( '/offer_/', $key ) && $arPropertiesOffers[str_replace( 'offer_', '', $key )] && $value ) {

                    if ( preg_match( '/offer_/', $key ) ) $key = preg_replace( '/offer_/', '', $key );

                    if ( $key == 'price' || $key == 'discount' || $key == 'discount_price' ) {

                        $order[] = ( ( $value == 'desc' ) ? $mysql -> parse( 'max( p1.?n ) desc', $key ) : $mysql -> parse( 'min( p1.?n ) asc', $key ) );
                        $is_order_price = true;

                    }

                    $order_offers[] = $mysql -> parse( '?p.?n ?p', ( ( $arPropertiesOffers[$key]['is_catalog'] ) ? 'p1' : 'o1' ), $key, ( ( $value == 'desc' ) ? 'desc' : 'asc' ) );

                }

            }

        }

        //$Functions -> Pre( $order );
        //$Functions -> Pre( $order_offers );
        //$Functions -> Pre( $order_price );
        //$Functions -> Pre( $where_multi_offers );

        $time_start = microtime( 1 );

        $arResCount = $mysql -> query('
            SELECT
                COUNT( o1.`product_id` )
            FROM ?n o1
            ?p
            ?p
            ?p
            ?p
            GROUP BY o1.`product_id`
        ',
            $table_name_offers,
            ( ( $is_where || $is_where_multi || $is_where_section ) ?
                $mysql -> parse('
                    JOIN (
                        SELECT
                            t1.`id`
                        FROM ?n t1
                        ?p
                        ?p
                        ?p
                    ) J ON J.`id` = o1.`product_id`
                ',
                    $table_name,
                    ( ( $is_where_section ) ? $join_section : '' ),
                    ( ( $is_where_multi ) ? implode( "\n", $where_multi ) : '' ),
                    ( ( $is_where || $is_where_section ) ? 'WHERE ' . implode( ' AND ', $where ) : '' )
                )
                : '' ),
            ( ( $is_where_price ) ? $join_price : '' ),
            ( ( $is_where_multi_offers ) ? implode( "\n", $where_multi_offers ) : '' ),
            ( ( $is_where_offers ) ? 'WHERE ' . implode( ' AND ', $where_offers ) : '' )
        );

        $count = $arResCount -> num_rows;

        if ( $arParams['show_query_time'] == 'Y' ) echo ( microtime(1) - $time_start ) . ' запрос на количество всего: ' . $count . '<br />';

        $time_start = microtime( 1 );

        $arResProductIds = $mysql -> query('
            SELECT
                o1.`product_id`
            FROM ?n o1
            ?p
            ?p
            ?p
            ?p
            ?p
            GROUP BY o1.`product_id`
            ?p
            LIMIT ?i OFFSET ?i
        ',
            $table_name_offers,
            ( ( $is_where || $is_where_multi || $is_where_section ) ?
                $mysql -> parse('
                    JOIN (
                        SELECT
                            t1.`id`
                        FROM ?n t1
                        ?p
                        ?p
                        ?p
                    ) J ON J.`id` = o1.`product_id`
                ',
                    $table_name,
                    ( ( $is_where_section ) ? $join_section : '' ),
                    ( ( $is_where_multi ) ? implode( "\n", $where_multi ) : '' ),
                    ( ( $is_where || $is_where_section ) ? 'WHERE ' . implode( ' AND ', $where ) : '' )
                )
                : ''
            ),
            ( ( $is_where_multi_offers ) ? implode( "\n", $where_multi_offers ) : '' ),
            ( ( $is_order_price || $is_where_price ) ? $join_price : '' ),
            ( ( $is_order ) ? $mysql -> parse( 'LEFT JOIN ?n t1 ON t1.`id` = o1.`product_id`', $table_name ) : '' ),
            ( ( $is_where_offers ) ? 'WHERE ' . implode( ' AND ', $where_offers ) : '' ),
            ( ( $order ) ? 'ORDER BY ' . implode( ', ', $order ) : '' ),
            $limit,
            $offset
        );

        if ( $arParams['show_query_time'] == 'Y' ) echo ( microtime(1) - $time_start ) . ' запрос с сортировкой и выборкой ID товаров<br />';

        $arProductIds = array();

        while ( $row = mysqli_fetch_assoc( $arResProductIds ) ) {

            $arProductIds[] = $row['product_id'];

        }

        //$Functions -> Pre( $arProductIds );

        if ( $arProductIds ) {

            $time_start = microtime( 1 );

            $arResOffersIds = $mysql -> query('
                SELECT
                    o1.`id`
                FROM ?n o1
                ?p
                ?p
                WHERE o1.`product_id` IN ( ?a )?p
            ',
                $table_name_offers,
                ( ( $is_where_price ) ? $join_price : '' ),
                ( ( $is_where_multi_offers ) ? implode( "\n", $where_multi_offers ) : '' ),
                $arProductIds,
                ( ( $is_where_offers ) ? ' AND ' . implode( ' AND ', $where_offers ) : '' )
            );

            if ( $arParams['show_query_time'] == 'Y' ) echo ( microtime(1) - $time_start ) . ' запрос с сортировкой и выборкой ID торговых предложений<br />';

            $arOffersIds = array();

            while ( $row = mysqli_fetch_assoc( $arResOffersIds ) ) {

                $arOffersIds[] = $row['id'];

            }

            //$Functions -> Pre( $arOffersIds );

            if ( $arOffersIds ) {

                $time_start = microtime( 1 );

                $arResProducts = $mysql -> query('
                    SELECT
                        ?p?p
                    FROM ?n t1
                    ?p
                    WHERE t1.`id` IN ( ?a )
                    ORDER BY FIELD( t1.`id`, ?a )
                ',
                    implode( ', ', $select ),
                    ( ( $is_multiple ) ? ', t2.`id` prop_id, t2.`value` prop_value, t3.`type` prop_type, t3.`code` prop_code' : '' ),
                    $table_name,
                    ( ( $is_multiple ) ? $mysql -> parse( 'LEFT JOIN ?n t2 ON t2.`element_id` = t1.`id` LEFT JOIN `iblock_properties` t3 ON t3.`id` = t2.`property_id` AND t3.`code` IN ( ?a )', $table_name . '_properties', $select_props ) : '' ),
                    $arProductIds,
                    $arProductIds
                );

                if ( $arParams['show_query_time'] == 'Y' ) echo ( microtime(1) - $time_start ) . ' запрос с сортировкой и выборкой товаров<br />';

                $time_start = microtime( 1 );

                //$Functions -> Pre( $order_offers );

                if ( !$order_offers ) {

                    $is_order_price = true;
                    $order_offers[] = 'p1.`discount_price` asc';

                }

                $arResOffers = $mysql -> query('
                    SELECT
                        ?p?p
                    FROM ?n o1
                    ?p
                    ?p
                    WHERE o1.`id` IN ( ?a )
                    ?p
                ',
                    implode( ', ', $select_offers ),
                    ( ( $is_multiple_offers ) ? ', o2.`id` prop_id, o2.`value` prop_value, o3.`type` prop_type, o3.`code` prop_code' : '' ),
                    $table_name_offers,
                    ( ( $is_multiple_offers ) ? $mysql -> parse( 'LEFT JOIN ?n o2 ON o2.`element_id` = o1.`id` LEFT JOIN `iblock_properties` o3 ON o3.`id` = o2.`property_id` AND o3.`code` IN ( ?a )', $table_name_offers . '_properties', $select_props_offers ) : '' ),
                    ( ( $is_select_price || $is_order_price ) ? $join_price : '' ),
                    $arOffersIds,
                    ( ( $order_offers ) ? 'ORDER BY ' . implode( ', ', $order_offers ) : '' )
                );

                if ( $arParams['show_query_time'] == 'Y' ) echo ( microtime(1) - $time_start ) . ' запрос с сортировкой и выборкой торговых предложений<br />';

                $time_start = microtime( 1 );

                $items = array();
                $arFiles = array();
                $arFilesIds = array();
                $arElementIds = array();

                while ( $row = mysqli_fetch_assoc( $arResProducts ) ) {

                    if ( !in_array( $row['id'], $arElementIds ) ) $arElementIds[] = $row['id'];

                    for ( $i = 0; $i < count( $select ); $i++ ) {

                        $prop = explode( '`', $select[$i] );

                        $key = $prop[1];

                        if ( $arProperties[$key]['type'] == 'list' || $arProperties[$key]['type'] == 'choice' ) {

                            $items[$row['id']][$key]['name'] = $arProperties[$key]['values'][$row[$key]]['name'];
                            $items[$row['id']][$key]['code'] = $arProperties[$key]['values'][$row[$key]]['code'];

                            if ( $arProperties[$key]['values'][$row[$key]]['file'] ) $items[$row['id']][$key]['file'] = $arProperties[$key]['values'][$row[$key]]['file'];

                        } else if ( $arProperties[$key]['type'] == 'file' ) {

                            if ( $row[$key] ) {

                                $arFiles[$row[$key]] = array(
                                    'item' => $row['id'],
                                    'code' => $key,
                                    'file_id' => $row[$key]
                                );

                                if ( !in_array( $row[$key], $arFilesIds ) ) $arFilesIds[] = $row[$key];

                            }

                            $items[$row['id']][$key] = $row[$key];

                        } else if ( $arProperties[$key]['type'] == 'number_dot' ) {

                            $items[$row['id']][$key] = ( $row[$key] ) ? round( $row[$key], 2 ) : '';

                        } else {

                            $items[$row['id']][$key] = $row[$key];
                        }

                    }

                    if ( $row['prop_id'] AND $row['prop_code'] ) {

                        if ( $row['prop_type'] == 'list' || $row['prop_type'] == 'choice' ) {

                            $items[$row['id']][$row['prop_code']][$row['prop_id']]['name'] = $arProperties[$row['prop_code']]['values'][$row['prop_value']]['name'];
                            $items[$row['id']][$row['prop_code']][$row['prop_id']]['code'] = $arProperties[$row['prop_code']]['values'][$row['prop_value']]['code'];

                            if ( $arProperties[$row['prop_code']]['values'][$row['prop_value']]['file'] ) {
                                $items[$row['id']][$row['prop_code']][$row['prop_id']]['file'] = $arProperties[$row['prop_code']]['values'][$row['prop_value']]['file'];
                            }

                        } else if ( $row['prop_type'] == 'file' ) {

                            $arFiles[$row['prop_value']] = array(
                                'item' => $row['id'],
                                'code' => $row['prop_code'],
                                'file_id' => $row['prop_value']
                            );

                            if ( !in_array( $row['prop_value'], $arFilesIds ) ) $arFilesIds[] = $row['prop_value'];

                            $items[$row['id']][$row['prop_code']][$row['prop_value']]['value'] = $row['prop_value'];

                        } else {

                            $items[$row['id']][$row['prop_code']][$row['prop_id']]['id'] = $row['prop_id'];
                            $items[$row['id']][$row['prop_code']][$row['prop_id']]['value'] = $row['prop_value'];

                        }

                    }

                }

                while ( $row = mysqli_fetch_assoc( $arResOffers ) ) {

                    for ( $i = 0; $i < count( $select_offers ); $i++ ) {

                        $prop = explode( '`', $select_offers[$i] );

                        $key = $prop[1];
                        $val = trim( $prop[2] );

                        if ( $arPropertiesOffers[$key]['type'] == 'list' || $arPropertiesOffers[$key]['type'] == 'choice' ) {

                            $items[$row['offer_product_id']]['offers'][$row['offer_id']][$key]['name'] = $arPropertiesOffers[$key]['values'][$row[$val]]['name'];
                            $items[$row['offer_product_id']]['offers'][$row['offer_id']][$key]['code'] = $arPropertiesOffers[$key]['values'][$row[$val]]['code'];

                            if ( $arPropertiesOffers[$key]['values'][$row[$key]]['file'] ) $items[$row['offer_product_id']]['offers'][$row['offer_id']][$key]['file'] = $arPropertiesOffers[$key]['values'][$row[$val]]['file'];

                        } else if ( $arPropertiesOffers[$key]['type'] == 'file' ) {

                            if ( $row[$val] ) {

                                $arFiles[$row[$val]] = array(
                                    'item' => $row['offer_product_id'],
                                    'offer' => $row['offer_id'],
                                    'code' => $key,
                                    'file_id' => $row[$val]
                                );

                                $arFilesIds[$row[$val]] = $row[$val];

                            }

                            $items[$row['offer_product_id']]['offers'][$row['offer_id']][$key] = $row[$val];

                        } else if ( $arPropertiesOffers[$key]['type'] == 'number_dot' ) {

                            if ( ( $key == 'price' || $key == 'discount' || $key == 'discount_price' ) && $arCurrencyRate ) {
                                $items[$row['offer_product_id']]['offers'][$row['offer_id']][$key] = ( $row[$val] ) ? round( $row[$val] * $arCurrencyRate['rate'] / $arCurrencyRate['nominal'], 2 ) : '';
                            } else {
                                $items[$row['offer_product_id']]['offers'][$row['offer_id']][$key] = ( $row[$val] ) ? round( $row[$val], 2 ) : '';
                            }

                        } else {
                            $items[$row['offer_product_id']]['offers'][$row['offer_id']][$key] = $row[$val];
                        }

                    }

                    if ( $row['prop_id'] AND $row['prop_code'] ) {

                        if ( $row['prop_type'] == 'list' || $row['prop_type'] == 'choice' ) {

                            $items[$row['offer_product_id']]['offers'][$row['offer_id']][$row['prop_code']][$row['prop_id']]['name'] = $arPropertiesOffers[$row['prop_code']]['values'][$row['prop_value']]['name'];
                            $items[$row['offer_product_id']]['offers'][$row['offer_id']][$row['prop_code']][$row['prop_id']]['code'] = $arPropertiesOffers[$row['prop_code']]['values'][$row['prop_value']]['code'];

                            if ( $arProperties[$row['prop_code']]['values'][$row['prop_value']]['file'] ) {
                                $items[$row['offer_product_id']]['offers'][$row['offer_id']][$row['prop_code']][$row['prop_id']]['file'] = $arPropertiesOffers[$row['prop_code']]['values'][$row['prop_value']]['file'];
                            }

                        } else if ( $row['prop_type'] == 'file' ) {

                            if ( $row['prop_value'] ) {

                                $arFiles[$row['prop_value']] = array(
                                    'item' => $row['offer_product_id'],
                                    'offer' => $row['offer_id'],
                                    'code' => $row['prop_code'],
                                    'file_id' => $row['prop_value']
                                );

                                $arFilesIds[$row['prop_value']] = $row['prop_value'];

                            }

                            $items[$row['offer_product_id']]['offers'][$row['offer_id']][$row['prop_code']][$row['prop_value']]['value'] = $row['prop_value'];

                        } else {

                            $items[$row['offer_product_id']]['offers'][$row['offer_id']][$row['prop_code']][$row['prop_id']]['id'] = $row['prop_id'];
                            $items[$row['offer_product_id']]['offers'][$row['offer_id']][$row['prop_code']][$row['prop_id']]['value'] = $row['prop_value'];

                        }

                    }

                }

                if ( $arParams['show_query_time'] == 'Y' ) echo ( microtime(1) - $time_start ) . ' формирование основного массива<br />';

                //$Functions -> Pre( $arFiles );
                //$Functions -> Pre( $arFilesIds );

                if ( $arFilesIds ) {

                    $time_start = microtime( 1 );

                    $arFilesResult = $this -> classFiles -> GetFiles(
                        array(),
                        array(
                            'id' => $arFilesIds
                        ),
                        array(
                            'count_on_page' => 999
                        ),
                        array(),
                        array(
                            'reset_keys' => 'N'
                        )
                    );

                    //$Functions -> Pre( $arFilesResult );

                    foreach ( $arFiles as $key => $file ) {

                        //$Functions -> Pre( $file );

                        if ( $file['offer'] ) {

                            if ( $items[$file['item']]['offers'][$file['offer']][$file['code']][$file['file_id']] ) {
                                $items[$file['item']]['offers'][$file['offer']][$file['code']][$file['file_id']] = $arFilesResult['items'][$file['file_id']];
                            } else {
                                $items[$file['item']]['offers'][$file['offer']][$file['code']] = $arFilesResult['items'][$file['file_id']];
                            }

                        } else {

                            if ( $items[$file['item']][$file['code']][$file['file_id']] ) {
                                $items[$file['item']][$file['code']][$file['file_id']] = $arFilesResult['items'][$file['file_id']];
                            } else {
                                $items[$file['item']][$file['code']] = $arFilesResult['items'][$file['file_id']];
                            }

                        }

                    }

                    if ( $arParams['show_query_time'] == 'Y' ) echo ( microtime(1) - $time_start ) . ' формирование фалового массива<br />';

                }

                $arResSections = $mysql -> query('
                    SELECT
                        t1.`section_id`, t1.`element_id`,
                        t2.`name`, t2.`code`, t2.`parent_id`
                    FROM `sections_bind` t1
                    INNER JOIN `sections` t2 ON t2.`id` = t1.`section_id`
                    WHERE t1.`iblock_id` = ?i AND t1.`element_id` IN ( ?a )
                ',
                    $arIblock['id'],
                    $arElementIds
                );

                while ( $row = mysqli_fetch_assoc( $arResSections ) ) {

                    $items[$row['element_id']]['sections'][$row['section_id']] = array(
                        'id' => $row['section_id'],
                        'name' => $row['name'],
                        'code' => $row['code'],
                        'parent_id' => $row['parent_id']
                    );

                }

                //$Functions -> Pre( $items );

            }

        }

        return array(
            'items' => ( ( isset( $items ) ) ? $Functions -> ResetKeys( $items ) : '' ),
            'items_count' => $count
        );
    }

    public function GetSectionChildren ( $id )
    {
        global $mysql;
        $array = array();

        $arResSectionId = $mysql -> query( 'SELECT `id` FROM `sections` WHERE `parent_id` = ?i', $id );

        if ( $arResSectionId -> num_rows ) {

            while ( $row = mysqli_fetch_assoc( $arResSectionId ) ) {

                $array[] = $row['id'];
                $answer = $this -> GetSectionChildren( $row['id'] );
                if ( $answer ) $array = array_merge( $array, $answer );

            }

        }

        return $array;

    }

    public function GetSectionList ( $arSelect = array(), $arFilter = array(), $arNav = array(), $arOrder = array(), $arParams = array() )
    {
        global $mysql;
        global $Functions;

        $limit = 50;
        $offset = 0;

        $arProperties = array(
            'id' => array(
                'type' => 'number'
            ),
            'iblock_id' => array(
                'type' => 'number'
            ),
            'active' => array(
                'type' => 'number'
            ),
            'sort' => array(
                'type' => 'number'
            ),
            'date_create' => array(
                'type' => 'datetime'
            ),
            'date_update' => array(
                'type' => 'datetime'
            ),
            'name' => array(
                'type' => 'string'
            ),
            'code' => array(
                'type' => 'string'
            ),
            'parent_id' => array(
                'type' => 'number'
            ),
            'description' => array(
                'type' => 'text'
            ),
            'file_id' => array(
                'type' => 'number'
            )
        );

        $select = array();

        foreach ( $arProperties as $key => $arProp ) {

            if ( !$arSelect || in_array( $key, $arSelect ) || $key == 'id' ) $select[] = $mysql -> parse( 't1.?n', $key );

        }

        if ( $arParams ) {

            if ( $arParams['get_child'] == 'Y' ) {

                $arSectionParentId = array();

                if ( $arFilter['code'] ) {

                    $arSectionId = $mysql -> queryList( 'SELECT `id` FROM `sections` WHERE `iblock_id` = ?i AND `code` = ?s', $arFilter['iblock_id'], $arFilter['code'] );
                    unset( $arFilter['code'] );
                    $arFilter['parent_id'] = $arSectionId[0]['id'];

                } else if ( $arFilter['id'] ) {

                    $arFilter['parent_id'] = $arFilter['id'];
                    unset( $arFilter['id'] );

                }

            }

        }

        //$Functions -> Pre( $arFilter );

        $where = array();

        if ( $arFilter ) {

            foreach ( $arFilter as $key => $value ) {

                $condition = '';

                $arCondition = $this -> DefiningCondition( $key );

                if ( $arCondition['condition'] ) {

                    $condition = $arCondition['condition'];
                    $key = $arCondition['key'];

                }

                if ( $arProperties[$key] || $key == 'iblock_id' ) {

                    if ( $condition == '%' ) {

                        $condition = 'LIKE';
                        $value = '%' . $value . '%';

                    }

                    switch ( $arProperties[$key]['type'] ) {

                        case 'datetime':
                        case 'string':
                        case 'text':
                            $where[] = $mysql -> parse( 't1.?n ?p ?s', $key, ( ( $condition ) ? $condition : '=' ), $value );
                            break;

                        case 'number':
                            $where[] = $mysql -> parse( 't1.?n ?p ?i', $key, ( ( $condition ) ? $condition : '=' ), $value );
                            break;

                    }

                }

            }

        }

        //$Functions -> Pre( $where );

        $limit = ( ( $arNav['limit'] ) ? $arNav['limit'] : 50 );
        $offset = ( ( $arNav['page'] ) ? $arNav['page'] : 1 );

        $order = array();

        if ( $arOrder ) {

            foreach ( $arOrder as $key => $value ) {

                if ( !$arProperties[$key] ) continue;

                $order[] = $mysql -> parse( 't1.?n ?p', $key, $value );

            }

        }

        $arCount = $mysql -> query('
            SELECT
                COUNT( t1.`id` )
            FROM `sections` t1
            ?p
            GROUP BY t1.`id`
        ',
            ( ( $where ) ? 'WHERE ' . implode( ' AND ', $where ) : '' )
        );

        $count = $arCount -> num_rows;

        $arResult = $mysql -> query('
            SELECT
                ?p,
                t2.`id` file_id, t2.`active` file_active, t2.`sort` file_sort, t2.`date_create` file_date_create, t2.`date_update` file_date_update, t2.`name` file_name, t2.`path`, t2.`description` file_description, t2.`type`, t2.`size`, t2.`width`, t2.`height`, t2.`user_id`
            FROM `sections` t1
            LEFT JOIN `files` t2 ON t2.`id` = t1.`file_id`
            ?p
            ?p
            LIMIT ?i OFFSET ?i
        ',
            implode( ', ', $select ),
            ( ( $where ) ? 'WHERE ' . implode( ' AND ', $where ) : '' ),
            ( ( $order ) ? 'ORDER BY ' . implode( ', ', $order ) : '' ),
            $limit,
            ( $offset * $limit - $limit )
        );

        $items = array();

        while ( $row = mysqli_fetch_assoc( $arResult ) ) {

            for ( $i = 0; $i < count( $select ); $i++ ) {

                $prop = explode( '`', $select[$i] );
                $key = $prop[1];

                $items[$row['id']][$key] = $row[$key];

            }

            if ( $row['file_id'] ) {

                $items[$row['id']]['file']['id'] = $row['file_id'];
                $items[$row['id']]['file']['active'] = $row['file_active'];
                $items[$row['id']]['file']['sort'] = $row['file_sort'];
                $items[$row['id']]['file']['date_create'] = $row['file_date_create'];
                $items[$row['id']]['file']['date_update'] = $row['file_date_update'];
                $items[$row['id']]['file']['name'] = $row['file_name'];
                $items[$row['id']]['file']['path'] = $row['path'];
                $items[$row['id']]['file']['description'] = $row['file_description'];
                $items[$row['id']]['file']['type'] = $row['type'];
                $items[$row['id']]['file']['size'] = $row['size'];
                $items[$row['id']]['file']['width'] = $row['width'];
                $items[$row['id']]['file']['height'] = $row['height'];
                $items[$row['id']]['file']['user_id'] = $row['user_id'];

            }

        }

        return array(
            'count' => $count,
            'items' => ( ( $items ) ? ( ( $arParams['reset_keys'] != 'N' ) ? $Functions -> ResetKeys( $items ) : $Functions -> BuildTree( $items ) ) : '' )
        );
    }

    public function SetToken ( $array )
    {
        if ( !$array['iblock_id'] ) return 'Нет ID информационного блока';

        global $mysql;
        global $Functions;

        $token = md5( microtime() . $array['iblock_id'] );

        $set = array(
            'token' => $token,
            'iblock_id' => $array['iblock_id'],
            'element_id' => ( ( $array['element_id'] ) ? $array['element_id'] : NULL )
        );

        $mysql -> query( 'INSERT INTO `protected_form` SET ?u', $set );

        return $token;
    }
}