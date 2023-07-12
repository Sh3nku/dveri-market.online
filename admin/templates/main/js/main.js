function getPage ( url, hApi, target ) {

    if ( target === '_blank' ) {

        window.open( url );
        return false

    }

    let anchor;

    if ( url.match( /#/ ) ) anchor = url.split('#').pop();

    $.ajax({
        url: url,
        type: 'GET',
        cache: false,
        dataType: 'html',

        beforeSend: function () {

            startProgressBar()

        },

        error: function ( jqXHR, exception ) {

            let msg = '';

            if ( jqXHR.status === 0 ) {
                msg = 'Not connect.\n Verify Network.';
            } else if ( jqXHR.status === 404 ) {
                location.href = '/404.php';
                msg = 'Requested page not found. [404]';
            } else if ( jqXHR.status === 500 ) {
                msg = 'Internal Server Error [500].';
            } else if ( exception === 'parsererror' ) {
                msg = 'Requested JSON parse failed.';
            } else if ( exception === 'timeout' ) {
                msg = 'Time out error.';
            } else if ( exception === 'abort' ) {
                msg = 'Ajax request aborted.';
            } else {
                msg = 'Uncaught Error.\n' + jqXHR.responseText;
            }

        },

        success: function ( data ) {

            if( hApi !== false ) window.history.pushState( null, '', url );

            $( 'main' ).html( $( data ).find( 'main' ).html() );

            endProgressBar();

            let b = $( 'body' );

            if ( b.hasClass( 'responsive' ) ) {

                b.removeClass( 'responsive bodylock' )

            }

            if ( anchor ) {

                $( 'html, body' ).animate({
                    scrollTop: $( '#' + anchor ).offset().top
                }, 300 );

            } else {

                $( 'body, html' ).animate({
                    scrollTop: 0
                }, 0 );

            }

            $( 'title' ).html( $( data ).filter( 'title' ).text() );
            $( 'meta[name="keywords"]' ).attr( 'content', $( data ).filter( 'meta[name="keywords"]' ).attr( 'content' ) );
            $( 'meta[name="description"]' ).attr( 'content', $( data ).filter( 'meta[name="description"]' ).attr( 'content' ) );
            $( 'link[rel="canonical"]' ).attr( 'href', $( data ).filter( 'link[rel="canonical"]' ).attr( 'href' ) );

        }
    });

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

    $( document ).on( 'click', 'a', function () {

        let href = $( this ).attr( 'href' ),
            target = $( this ).attr( 'target' );

        if ( !href.match( 'tel:' ) && !href.match( 'mailto:' ) && !$( this ).parent().hasClass( 'adm__panel-action' ) ) {

            getPage( href, true, target );
            return false

        }

    });

    $( '.show-aside' ).on( 'click', function () {

        let b = $( 'body' );

        if ( b.hasClass( 'responsive' ) ) {

            b.removeClass( 'responsive bodylock' )

        } else {

            b.addClass( 'responsive bodylock' );

        }

    });

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

                console.log( data );

                $( '.header-basket-count' ).text( data );
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

    })

})

$( document ).on( 'focus', '.errors-item', function () {

    $( this ).removeClass( 'errors-item' ).next( 'span' ).remove();

});

$( window ).one( 'load', function() {

    $( 'body' ).removeClass( 'js-body-no-transition' );

});

$( window ).on( 'resize scroll load', function () {

    let d = $( this ).scrollTop(),
        w = $( 'header' ),
        h = w.offset().top;

    if ( ( d > h ) && !w.hasClass( 'fixed' ) ) {

        w.addClass( 'fixed' )

    } else if ( ( d <= h ) && w.hasClass( 'fixed' ) ) {

        w.removeClass( 'fixed' )

    }

});