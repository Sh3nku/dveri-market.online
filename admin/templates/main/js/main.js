function ShowErrorBlock ( obj, errors ) {

    let block = obj.prev( '.form-errors' );

    if ( block.length ) {
        block.html( '' );
    } else {

        obj.before( '<div class="form-errors"></div>' );
        block = obj.prev( '.form-errors' );

    }

    $.each( errors, function( k, v ) {

        if ( v.block ) obj.find( v.block ).addClass( 'error' );
        if ( v.input ) obj.find( '[name="' + v.input + '"]' ).addClass( 'error' );
        block.append( '<p>' + v.text + '</p>' );

    });

}

function ShowErrorItems ( errors ) {

    console.log( errors );

    $( '[data-error]' ).removeClass( 'errors-item' ).next( '.form-errors-item' ).remove();

    let upper = 100000,
        scroll_top = $( window ).scrollTop();

    $.each( errors, function( key, item ) {

        let target = '';

        if ( item.input ) {
            target = $( '[name="' + item.input + '"]' );
            target.parent( '.form-input' ).addClass( 'errors-item' ).append( '<span class="form-errors-item">' + item['text'] + '</span>' );
        } else {
            target = $( '[data-error=' + item['block'] + ']' );
            target.append( '<span class="form-errors-item">' + item['text'] + '</span>' );
        }

        let offset_top = target.offset().top;

        if ( offset_top < upper ) upper = offset_top;

    });

    if ( scroll_top > upper ) {

        $( 'html, body' ).animate({
            scrollTop: ( upper - 70 ) + 'px'
        }, 300 )

    }

}

function HideErrorBlock () {

    $( '.form-errors' ).remove()

}

function ShowLoaderButton ( button ) {

    let w = button.outerWidth();
    button.css( 'width', w ).prop( 'disabled', true ).addClass( 'loader' )

}

function HideLoaderButton ( button ) {

    button.removeAttr( 'style' ).prop( 'disabled', false ).removeClass( 'loader' )

}

function ClosePopupSearch () {
    $( '.js-search-button' ).trigger( 'click' );
    $( '.js-search-popup-input' ).val( '' );
    $( '.js-search-popup-content' ).html( '' );
    $( '.search-popup__content-wrapper' ).removeClass( '_search-content-open' );
}

function IsMobile() {
    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|BB|PlayBook|IEMobile|Windows Phone|Kindle|Silk|Opera Mini/i.test(navigator.userAgent)) {
        return true;
    } else {
        return false;
    }
}

function startProgressBar () {

    $( 'body' ).append( $( '<div></div>' ).attr( 'id', 'progress' ) );
    $( '#progress' ).width( ( 50 + Math.random() * 30 ) + '%' );

}

function endProgressBar () {

    $( '#progress' ).width( '101%' ).delay( 300 ).fadeOut( 400, function () {
        $( this ).remove();
    });

}

$( function () {

    let scrollWidth = window.innerWidth - document.documentElement.clientWidth;

    $( '.js-open-basket' ).on( 'click', function () {

        $.ajax({
            url: '/admin/templates/main/helper.php',
            type: 'POST',
            data: { OpenBasket: 1 },

            success: function( data ) {

                open_modal(
                    data,
                    {
                        width: 1200
                    }
                );

            }
        })

    });

    $( document ).on( 'click', '.js-in-basket', function () {

        let array = [],
            button = $( this );

        if ( $( '#choiceOffer' ).length && !$( this ).hasClass( 'accessories-in-basket' ) ) {
            array = $( '#choiceOffer' ).serializeArray();
        } else {
            $.each( $( this ).data(), function ( k, v ) {

                array.push({
                    'name': k,
                    'value': v
                })

            })
        }

        ShowLoaderButton( button );

        $.ajax({
            url: '/admin/templates/main/helper.php',
            type: 'POST',
            data: { InBasket: array },

            success: function( data ) {

                HideLoaderButton( button );

                $( '.js-basket-count' ).text( data );
                $( '.js-open-basket' ).trigger( 'click' )

            }
        })

        return false;

    });

    $( '.js-get-call-form' ).on( 'click', function () {

        $.ajax({
            url: '/admin/templates/main/helper.php',
            type: 'POST',
            data: { gelCallForm: 1 },

            success: function( data ) {

                open_modal(
                    data,
                    {
                        width: 400
                    }
                );

            }
        })

    });

    $( document ).on( 'click', '.js-auth', function () {

        $.ajax({
            url: '/admin/templates/main/helper.php',
            type: 'POST',
            data: { getAuthForm: 1 },

            success: function( data ) {

                open_modal(
                    data,
                    {
                        width: 400
                    }
                );

            }
        })

    });

    $( '.js-exit' ).on( 'click', function () {

        $.ajax({
            url: '/admin/templates/main/helper.php',
            type: 'POST',
            data: { logout: 1 },

            success: function() {

                location.href = '';

            }
        });

    });

    $( document ).on( 'click', '.js-registration-form', function ( e ) {

        e.preventDefault()

        $.ajax({
            url: '/admin/templates/main/helper.php',
            type: 'POST',
            data: { getRegistrationForm: 1 },

            success: function( data ) {

                open_modal(
                    data,
                    {
                        width: 400
                    }
                );

            }
        })

    });

    $( document ).on( 'click', '.js-recover-form', function ( e ) {

        $.ajax({
            url: '/admin/templates/main/helper.php',
            type: 'POST',
            data: { gelRecoverForm: 1 },

            success: function( data ) {

                open_modal(
                    data,
                    {
                        width: 400
                    }
                );

            }
        })

    });

    $( '.js-search-button' ).on( 'click', function () {

        let body = $( 'body' ),
            header = $( 'header' ),
            search_popup = $( '.search-popup' );

        if ( body.hasClass( '_search-open' ) ) {
            body.removeClass( '_search-open _overflow-hidden' ).removeAttr( 'style' );
            header.removeAttr( 'style' );
            search_popup.removeAttr( 'style' );
        } else {
            body.addClass( '_search-open _overflow-hidden' ).css( 'padding-right', scrollWidth + 'px' );
            header.css( 'padding-right', scrollWidth + 'px' );
            search_popup.css( 'padding-right', scrollWidth + 'px' );
            $( '.search-popup__input' ).trigger( 'focus' )
        }

    });

    $( '.js-search-popup-close' ).on( 'click', function () {
        ClosePopupSearch();
    });

    $( '.js-catalog-button' ).on( 'click', function () {

        let body = $( 'body' ),
            header = $( 'header' );

        if ( body.hasClass( '_catalog-open' ) ) {
            body.removeClass( '_catalog-open _overflow-hidden' ).removeAttr( 'style' );
            header.removeAttr( 'style' );
        } else {
            body.addClass( '_catalog-open _overflow-hidden' ).css( 'padding-right', scrollWidth + 'px' );
            header.css( 'padding-right', scrollWidth + 'px' );
        }

    });

    let search_timeout;

    $( '.js-search-popup-input' ).on( 'keyup', function () {

        clearTimeout( search_timeout );

        let value = $( this ).val();

        if ( value.length > 1 ) {

            search_timeout = setTimeout( function () {

                $.ajax({
                    url: '/admin/templates/main/helper.php',
                    type: 'POST',
                    data: { search_string: value },

                    success: function( data ) {

                        $( '.search-popup__content-wrapper' ).addClass( '_search-content-open' );
                        $( '.js-search-popup-content' ).html( data );

                    }
                })

            }, 300 );

        } else {
            $( '.search-popup__content-wrapper' ).removeClass( '_search-content-open' );
            $( '.js-search-popup-content' ).html( '' );
        }

    });

    $( document ).on( 'mouseup', function ( e ) {

        let body = $( 'body' );

        if (
            body.hasClass( '_catalog-open' )
            && !$( e.target ).closest( '.menu' ).length
            && !$( e.target ).is( '.menu' )
            && !$( e.target ).closest( '.js-catalog-button' ).length
            && !$( e.target ).is( '.js-catalog-button' )
        ) {
            $( '.js-catalog-button' ).trigger( 'click' )
        }

        if (
            body.hasClass( '_search-open' )
            && !$( e.target ).closest( '.search-popup' ).length
            && !$( e.target ).is( '.search-popup' )
            && !$( e.target ).closest( '.search-popup__content' ).length
            && !$( e.target ).is( '.search-popup__content' )
        ) {
            ClosePopupSearch();
        }

    });

    let lastscrolltop = 0;

    $( window ).on( 'scroll', function () {

        if ( $( 'body' ).hasClass( '_catalog-open' ) ) return false;

        if ( $( window ).scrollTop() > lastscrolltop && $( window ).scrollTop() > 0 ) {
            $( 'header' ).addClass( '_hidden' );
        } else {
            $( 'header' ).removeClass( '_hidden' );
        }

        lastscrolltop = $( window ).scrollTop();

    });

    $( '.menu__first-level__a.js-menu-choice' ).on( 'mouseenter', function () {
        if ( !IsMobile() ) {
            let id = $( this ).data( 'menu_id' ),
                target = $( '.menu__second-level[data-menu_target=' + id + ']' );

            $( '.menu__second-level' ).removeClass( '_active' );
            target.addClass( '_active' );
        }
    });

    $( '.js-menu-choice' ).on( 'click', function ( e ) {

        if ( IsMobile() && !$( this ).hasClass( 'js-no' ) ) {
            e.preventDefault();

            let id = $( this ).data( 'menu_id' ),
                target = $( '[data-menu_target=' + id + ']' );

            $( '.menu__second' ).addClass( '_active' );
            target.addClass( '_open' );
        }

    });

    $( '.js-menu-back' ).on( 'click', function () {

        let isTwoLevel = false,
            target = $( this ).parent( 'div' ).parent( 'div' );

        if ( target.hasClass( 'menu__second-level' ) ) {
            isTwoLevel = true;
        }

        if ( isTwoLevel ) {
            $( '.menu__second' ).removeClass( '_active' );
        }

        target.removeClass( '_open' );

        console.log( isTwoLevel );

    });

    $( '.js-menu-more' ).on( 'click', function () {

        $( this ).closest( 'ul' ).addClass( '_show-all' );
        $( this ).remove();

    });

    /* ----- Каталог ----- */

    function catalogTagMoreToggle() {

        if ( $( '.catalog-tag__list > .buttons' ).outerHeight() < 80 ) {
            $( '.catalog-tag__more' ).hide();
            //$( '.catalog-tag__list' ).removeClass( '_short' );
        } else {
            $( '.catalog-tag__more' ).show();
            //$( '.catalog-tag__list' ).addClass( '_short' );
        }
    }

    catalogTagMoreToggle();

    $( window ).on( 'resize', function () {
        catalogTagMoreToggle();
    });

    $( document ).on( 'click', '.js-short-view', function () {

        let button = $( this ).children( 'span' ),
            block = $( this ).closest( '.catalog-tag__list' ),
            height = block.children( '.buttons' ).outerHeight();

        if ( block.hasClass( '_open' ) ) {
            block.removeAttr( 'style' ).removeClass( '_open' );
            button.text( 'Показать всё' );
        } else {
            block.height( height ).addClass( '_open' );
            button.text( 'Свернуть' );
        }

    });
});

$( document ).on( 'focus', '.errors-item', function () {
    $( this )
        .removeClass( 'errors-item' )
        .children( 'span' )
        .fadeOut( 200 );
});

$( document ).on( 'change', '[type=radio]', function () {
    let name = $( this ).attr( 'name' );

    $( '[data-error="' + name + '"]' )
        .children( 'span' )
        .fadeOut( 200 );
});

$( window ).one( 'load', function() {
    $( 'body' ).removeClass( 'js-body-no-transition' );
});