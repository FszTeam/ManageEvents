//$(document).ready(function () {
    function setToolTips() {
        var targets = $( '[rel~=pe_tooltip]' ).parent(),
            target  = false,
            tooltip = false,
            title   = false;
     
        targets.bind( 'mouseenter', function()
        {
            target  = $( this );
            tip     = target.find('.pe_tooltip_body').html();
            tooltip = $( '<div id="pe_tooltip"></div>' );
     
            if( !tip || tip == '' )
                return false;
     
            target.removeAttr( 'title' );
            tooltip.css( 'opacity', 0 )
                   .html( tip )
                   .appendTo( 'body' );
     
            var init_tooltip = function()
            {
                if( $( window ).width() < tooltip.outerWidth() * 1.5 )
                    tooltip.css( 'max-width', $( window ).width() / 2 );
                else
                    tooltip.css( 'max-width', 340 );
     
                var pos_left = target.offset().left + ( target.outerWidth() / 2 ) - ( tooltip.outerWidth() / 2 ),
                    pos_top  = target.offset().top - tooltip.outerHeight() - 20;
     
                if( pos_left < 0 )
                {
                    pos_left = target.offset().left + target.outerWidth() / 2 - 20;
                    tooltip.addClass( 'left' );
                }
                else
                    tooltip.removeClass( 'left' );
     
                if( pos_left + tooltip.outerWidth() > $( window ).width() )
                {
                    pos_left = target.offset().left - tooltip.outerWidth() + target.outerWidth() / 2 + 20;
                    tooltip.addClass( 'right' );
                }
                else
                    tooltip.removeClass( 'right' );
     
                if( pos_top < 0 )
                {
                    var pos_top  = target.offset().top + target.outerHeight();
                    tooltip.addClass( 'top' );
                }
                else
                    tooltip.removeClass( 'top' );
     
                tooltip.css( { left: pos_left, top: pos_top } )
                       .animate( { top: '+=10', opacity: 1 }, 50 );
            };
     
            init_tooltip();
            $( window ).resize( init_tooltip );
     
            var remove_tooltip = function()
            {
                tooltip.animate( { top: '-=10', opacity: 0 }, 50, function()
                {
                    $( this ).remove();
                });
     
                target.attr( 'title', tip );
            };
     
            target.bind( 'mouseleave', remove_tooltip );
            tooltip.bind( 'click', remove_tooltip );
        });
    }

    /**
     * Equal Heights Plugin
     * Equalize the heights of elements. Great for columns or any elements
     * that need to be the same size (floats, etc).
     * 
     * Version 1.0
     * Updated 12/10/2008
     *
     * Copyright (c) 2008 Rob Glazebrook (cssnewbie.com) 
     *
     * Usage: $(object).equalHeights([minHeight], [maxHeight]);
     * 
     * Example 1: $(".cols").equalHeights(); Sets all columns to the same height.
     * Example 2: $(".cols").equalHeights(400); Sets all cols to at least 400px tall.
     * Example 3: $(".cols").equalHeights(100,300); Cols are at least 100 but no more
     * than 300 pixels tall. Elements with too much content will gain a scrollbar.
     * 
     */
    

    function equalHeights(minHeight, maxHeight) {
        tallest = (minHeight) ? minHeight : 0;
        $(".calendar-day").each(function() {
            if($(this).height() > tallest) {
                tallest = ($(this).height())*1 + 10;
            }
        });
        if((maxHeight) && tallest > maxHeight) tallest = maxHeight;
        return $(".calendar-day").each(function() {
            $(this).height(tallest).css("overflow","auto");
        });
    }
//});