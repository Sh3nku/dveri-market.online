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

    //console.log( errors );

    $( '[data-error]' ).removeClass( 'errors-item' ).next( '.form-errors-item' ).remove();

    let upper = 100000,
        scroll_top = $( window ).scrollTop();

    $.each( errors, function( key, item ) {

        let target = $( '[data-error=' + item['block'] + ']' ),
            offset_top = target.offset().top;

        if ( offset_top < upper ) upper = offset_top;

        target.addClass( 'errors-item' ).after( '<span class="form-errors-item">' + item['text'] + '</span>' )

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

    button.css( 'width', 'auto' ).prop( 'disabled', false ).removeClass( 'loader' )

}

window.onpopstate = function() {
    getPage( window.location.pathname + window.location.search, false );
};

$( function () {

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

        let array = [];

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

        $.ajax({
            url: '/admin/templates/main/helper.php',
            type: 'POST',
            data: { InBasket: array },

            success: function( data ) {

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
    
    $( document ).on( 'click', '.js-short-view', function () {

        let block = $( this ).closest( '.short-view' );
        let content = block.children( '*:first-child' ),
            open = false;

        if ( block.hasClass( '_show' ) ) {
            open = true;
            block.removeClass( '_show' ).removeAttr( 'style' );
        } else {
            block.addClass( '_show' ).height( content.outerHeight() );
        }

        $( this ).text( ( ( open ) ? 'Показать' : 'Скрыть' ) );

    });

    $( '.js-search-button' ).on( 'click', function () {

        let body = $( 'body' );

        if ( body.hasClass( '_search-open' ) ) {
            body.removeClass( '_search-open _overflow-hidden' );
        } else {
            body.addClass( '_search-open _overflow-hidden' );
            $( '.search-popu__input' ).trigger( 'focus' )
        }

    });

    $( '.js-search-popup-close' ).on( 'click', function () {
        $( '.js-search-button' ).trigger( 'click' )
    });

    $( '.js-catalog-button' ).on( 'click', function () {

        let body = $( 'body' );

        if ( body.hasClass( '_catalog-open' ) ) {
            body.removeClass( '_catalog-open _overflow-hidden' );
        } else {
            body.addClass( '_catalog-open _overflow-hidden' );
        }

    });

    $( document ).on( 'mouseup', function ( e ) {

        if (
            $( 'body' ).hasClass( '_catalog-open' )
            && !$( e.target ).closest( '.menu' ).length
            && !$( e.target ).is( '.menu' )
            && !$( e.target ).closest( '.js-catalog-button' ).length
            && !$( e.target ).is( '.js-catalog-button' )
        ) {
            $( '.js-catalog-button' ).trigger( 'click' )
        }

        if (
            $( 'body' ).hasClass( '_search-open' )
            && !$( e.target ).closest( '.search-popup' ).length
            && !$( e.target ).is( '.search-popup' )
        ) {
            $( '.js-search-button' ).trigger( 'click' )
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

})

$( document ).on( 'focus', '.errors-item', function () {
    $( this ).removeClass( 'errors-item' ).next( 'span' ).remove();
});

$( window ).one( 'load', function() {
    $( 'body' ).removeClass( 'js-body-no-transition' );
});