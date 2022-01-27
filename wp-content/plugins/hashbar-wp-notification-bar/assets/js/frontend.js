;(function ($) {
    "use strict";

    $.fn.get_transparent_selector = function get_transparent_selector(){
        let transparent_header_selector = $(this).closest('.hthb-notification.hthb-pos--top[data-transparent_header_selector]').last().data('transparent_header_selector');
        if($(this).closest('.hthb-notification.hthb-pos--top').hasClass('hthb-transparent')){
            return transparent_header_selector;
        }

        return false;
    }

    $.fn.add_transparent_header_spacing = function add_transparent_header_spacing(top_notification_height){
        if( $('body').find('#wpadminbar').length ){
            top_notification_height = Number.parseInt(top_notification_height + $('body').find('#wpadminbar').height());
        }
    }

    function calculate_top_zero(){
        if( $('body').find('#wpadminbar').length ){
            return '32px';
        } else{
            return '0px';
        }
    }

    var timeout = 400;
    $(window).on('load',function(){
        var top_notification_height         = Number.parseInt($('.hthb-notification.hthb-pos--top').last().height()),
            bottom_notification_height      = $('.hthb-notification.hthb-pos--bottom').last().height(),
            left_wall_notification_width    = $('.hthb-notification.hthb-pos--left-wall').last().width(),
            right_wall_notification_width   = $('.hthb-notification.hthb-pos--right-wall').last().width();

        var position;

        // if load as minimized disabled
        $('.hthb-notification.hthb-state--open').each(function(){
            position = $(this).getPostion();

            if( position == 'top' ){
                $('body').addClass('hthb hthb-pt--' + top_notification_height );
                if( $(this).get_transparent_selector() ){

                    $($(this).get_transparent_selector()).addClass('hthb-top-unset');
                    $($(this).get_transparent_selector()).css( {'top':'unset'} );

                }
            } else if( position == 'bottom'){
                $('body').css('padding-bottom', bottom_notification_height + 'px');
            }

            // Implement how many time to show
            var time_to_show           = $(this).data('time_to_show'),
                id                     = $(this).data('id') ? '_' + $(this).data('id') : '', 
                coockie_count          = Cookies.get('hashbarpro_cookiecount' + id),
                hashbarpro_oldcookie   = Cookies.get('hashbarpro_oldcookie' + id);

            if( time_to_show && time_to_show > 0 ){

                if(document.cookie.indexOf("hashbarpro_oldcookie" + id) >= 0){
                    if(time_to_show == hashbarpro_oldcookie){
                        coockie_count++;
                        Cookies.set('hashbarpro_cookiecount' + id, coockie_count, { expires: 7 });
                    } else {
                        Cookies.set('hashbarpro_oldcookie' + id, time_to_show, { expires: 7 });
                        Cookies.set('hashbarpro_cookiecount' + id, 1, { expires: 7 });
                    }
                } else {
                    Cookies.set('hashbarpro_oldcookie' + id, time_to_show, { expires: 7 });
                    Cookies.set('hashbarpro_cookiecount' + id, 1, { expires: 7 });
                }
            }

            if( coockie_count > time_to_show ){
               $(this).css({'display': 'none'});
               $(this).removeClass('hthb-state--open').addClass('hthb-state--minimized');

               if( position == 'top' ){
                    $('body').removeClass('hthb');
               }
            }
            
        });

        // Load as mimimized if option is set to minimized
        $('.hthb-notification.hthb-state--minimized').each(function(){
             var position = $(this).getPostion();
             if( position == 'top' || position == 'top-promo' || position == 'bottom' || position == 'bottom-promo' ){
                $(this).find('.hthb-row').css('display', 'none');
             }
            
        });

        // Left/right wall
        $('.hthb-notification.hthb-state--minimized').each(function(){
            var position = $(this).getPostion();
            if( position == 'left-wall' ){
                  $(this).css('left', '-' + left_wall_notification_width + 'px' );
            } else if( position == 'right-wall' ){
                 $(this).css('right', '-' + right_wall_notification_width + 'px' );
            }
           
        });

        setTimeout(function(){
            $('.hthb-notification').addClass('hthb-loaded');
        }, timeout );

        // When click close button
        $('.hthb-close-toggle').on('click', function(){
            $(this).minimizeNotification( top_notification_height, bottom_notification_height, left_wall_notification_width, right_wall_notification_width);

            // Keep closed
             if( hashbar_localize.bar_keep_closed == '1' ){
                 Cookies.set('keep_closed_bar', '1', { expires: 7, path: '/' });
             }

            // Don't show forever
            if( hashbar_localize.dont_show_bar_after_close == '1' ){
                Cookies.set( 'dont_show_bar', '1', { expires: 7, path: '/' } );
            }
        });

        // When clicked open button
        $('.hthb-open-toggle').on('click', function(){
            $(this).showNotification( top_notification_height, bottom_notification_height, left_wall_notification_width, right_wall_notification_width );
        });

        // When scroll position matched with a notification
        // Show the notifications
        var window_inner_height             = $(window).height(),
            page_height                     = $('body').height();

        let current_scroll_position         = window.pageYOffset || document.documentElement.scrollTop,
            current_scroll_position_percent = current_scroll_position / scroll_pos_max * 100;
            current_scroll_position_percent = Number.parseInt(current_scroll_position_percent);

        var scroll_pos_max = $(document).height() - $(window).height();

        $(window).on('scroll', function(e){
            current_scroll_position         = $(window).scrollTop(),
            current_scroll_position_percent = current_scroll_position / scroll_pos_max * 100;
            current_scroll_position_percent = Number.parseInt(current_scroll_position_percent);

            $(`.hthb-scroll`).each(function(){
                let scroll_to_show = $(this).data('scroll_to_show'),
                    scroll_to_hide = $(this).data('scroll_to_hide');

                $(this).trigger_notification_on_scroll(scroll_to_show, scroll_to_hide, current_scroll_position, scroll_pos_max);
            });
        });
    });
    
    /**
     * Calculate % of a given value.
     * For example, If the give value is 1000 & we want to know the 75% of it.
     * It will return 750 as the result.
     *
     * @param number percent_amount, % amount.
     * @param number percent_of, Total amount from where the % should be calculated.
     * @return number
     */
    function percent_of(percent_amount, percent_of){
        percent_of = Number.parseInt(percent_of);
        percent_amount = Number.parseInt(percent_amount);
        
        return percent_of * percent_amount / 100;
    }

    $.fn.getPostion = function(){
        if(this.closest('.hthb-notification').hasClass('hthb-pos--top')){
            return 'top';
        }

        if(this.closest('.hthb-notification').hasClass('hthb-pos--bottom')){
            return 'bottom';
        }

        if(this.closest('.hthb-notification').hasClass('hthb-pos--left-wall')){
            return 'left-wall';
        }

        if(this.closest('.hthb-notification').hasClass('hthb-pos--right-wall')){
            return 'right-wall';
        }

        if(this.closest('.hthb-notification').hasClass('hthb-pos--bottom-promo')){
            return 'bottom-promo';
        }

        if(this.closest('.hthb-notification').hasClass('hthb-pos--top-promo')){
            return 'top-promo';
        }
    }

    $.fn.minimizeNotification = function(top_notification_height, bottom_notification_height, left_wall_notification_width, right_wall_notification_width){
        var postion = this.getPostion(),
            left_wall_notification_width = this.closest('.hthb-notification').width(),
            right_wall_notification_width = this.closest('.hthb-notification').width();

        this.closest('.hthb-notification').removeClass('hthb-state--open');
        this.closest('.hthb-notification').addClass('hthb-state--minimized');

        if(postion != 'left-wall' && postion != 'right-wall'){
            this.closest('.hthb-notification').find('.hthb-row').slideToggle();
        }
        
        if( postion == 'top' ){
            $('body').removeClass('hthb');

            // for sticky
            if( this.get_transparent_selector() ){
                $(this.get_transparent_selector()).addClass('hthb-top-unset');
                $(this.get_transparent_selector()).css({'top': calculate_top_zero()});
            }
        } else if( postion == 'bottom' ){
            $('body').css('padding-bottom', '');
        } else if( postion == 'left-wall' ){
            this.closest('.hthb-notification').css('left', '-' + left_wall_notification_width + 'px' );
        } else if( postion == 'right-wall' ){
            this.closest('.hthb-notification').css('right', '-' + right_wall_notification_width + 'px' );
        }
    }

    $.fn.showNotification = function(top_notification_height, bottom_notification_height, left_wall_notification_width, right_wall_notification_width){
        var postion = this.getPostion(),
        left_wall_notification_width = this.closest('.hthb-notification').width(),
        right_wall_notification_width = this.closest('.hthb-notification').width();

        this.closest('.hthb-notification').removeClass('hthb-state--minimized');
        this.closest('.hthb-notification').addClass('hthb-state--open');

        if(postion != 'left-wall' && postion != 'right-wall'){
            this.closest('.hthb-notification').find('.hthb-row').slideToggle();
        }
        
        if( postion == 'top' ){
            $('body').addClass('hthb hthb-pt--'+ top_notification_height );

            // for sticky
            if( this.get_transparent_selector() ){
                $(this.get_transparent_selector()).addClass('hthb-top-unset');
                $(this.get_transparent_selector()).css({'top': 'unset'});
            }
        } else if( postion == 'bottom' ){
            $('body').css('padding-bottom', bottom_notification_height + 'px');
        } else if( postion == 'left-wall' ){
            this.closest('.hthb-notification').css('left', '');
        } else if( postion == 'right-wall' ){
            this.closest('.hthb-notification').css('right', '');
        }
    }

    $.fn.trigger_click_on_open_button = function(){
        $(this).addClass('hthb-trigger-open-clicked').removeClass('hthb-trigger-close-clicked');
        $(this).find('.hthb-open-toggle').trigger('click');
    }

    $.fn.trigger_click_on_close_button = function(){
        $(this).removeClass('hthb-trigger-open-clicked').addClass('hthb-trigger-close-clicked');
        $(this).find('.hthb-close-toggle').trigger('click');
    }

    $.fn.trigger_notification_on_scroll = function( scroll_to_show, scroll_to_hide, current_scroll_position, scroll_pos_max ){
        if( (scroll_to_show && typeof scroll_to_show == 'string' && scroll_to_show.indexOf('%')) > 0 && (scroll_to_hide && typeof scroll_to_hide == 'string' && scroll_to_hide.indexOf('%')) > 1 ){
            scroll_to_show = Number.parseInt(scroll_to_show);
            scroll_to_hide = Number.parseInt(scroll_to_hide);
            // 20% ,  80%
            // console.log(1,scroll_to_show,scroll_to_hide,current_scroll_position, percent_of(scroll_to_hide, scroll_pos_max));

            if(current_scroll_position > percent_of(scroll_to_show, scroll_pos_max) &&  current_scroll_position < percent_of(scroll_to_hide, scroll_pos_max) ){
                if( !$(this).is('.hthb-state--open') && !$(this).is('.hthb-trigger-open-clicked') ){
                    $(this).trigger_click_on_open_button(); // show
                }
            } else{
                if( !$(this).is('.hthb-state--minimized') ){
                    $(this).trigger_click_on_close_button(); // hide
                }
            }
        } else if( (scroll_to_show && typeof scroll_to_show == 'string' && scroll_to_show.indexOf('%')) &&  (scroll_to_hide === '' || scroll_to_hide == undefined) ){
            scroll_to_show = Number.parseInt(scroll_to_show);
            // 20% , ''/undefined
            // console.log(2,scroll_to_show,scroll_to_hide,current_scroll_position, percent_of(scroll_to_hide, scroll_pos_max));

            if( current_scroll_position > percent_of(scroll_to_show, scroll_pos_max) ){
                if( !$(this).is('.hthb-state--open') && !$(this).is('.hthb-trigger-open-clicked') ){
                    $(this).trigger_click_on_open_button(); // show
                }
            } else {
                if( !$(this).is('.hthb-state--minimized') ){
                    $(this).trigger_click_on_close_button(); // hide
                }
            }
        } else if( (scroll_to_show && typeof scroll_to_show == 'number') && (scroll_to_hide && typeof scroll_to_hide == 'string')){
            // 300 , 80%
            // console.log(3,scroll_to_show,scroll_to_hide,current_scroll_position, percent_of(scroll_to_hide, scroll_pos_max));

            if( current_scroll_position > scroll_to_show &&  current_scroll_position < percent_of(scroll_to_hide, scroll_pos_max) ){
                if( !$(this).is('.hthb-state--open') && !$(this).is('.hthb-trigger-open-clicked') ){
                    $(this).trigger_click_on_open_button(); // show
                }
            } else{
                if( !$(this).is('.hthb-state--minimized') ){
                    $(this).trigger_click_on_close_button(); // hide
                }
            }

        } else if( (scroll_to_show === '' || scroll_to_show == undefined) && (scroll_to_hide && typeof scroll_to_hide == 'string' && scroll_to_hide.indexOf('%')) ){
            scroll_to_hide = Number.parseInt(scroll_to_hide);
            // empty / undefined , 90%
            // console.log(4,scroll_to_show,scroll_to_hide,current_scroll_position, percent_of(scroll_to_hide, scroll_pos_max));

            if( current_scroll_position > percent_of(scroll_to_hide, scroll_pos_max) ){
                if( !$(this).is('.hthb-state--minimized') ){
                    $(this).trigger_click_on_close_button(); // hide
                }
            } else{
                if( !$(this).is('.hthb-state--open') && !$(this).is('.hthb-trigger-open-clicked') ){
                    $(this).trigger_click_on_open_button(); // show 
                }
            }

        } else if( (scroll_to_show && typeof scroll_to_show == 'number') && (scroll_to_hide === '' || scroll_to_hide == undefined) ){
            // 300 , empty/undefined 
            // console.log(5,scroll_to_show,scroll_to_hide,current_scroll_position, percent_of(scroll_to_hide, scroll_pos_max));
            if( current_scroll_position < scroll_to_show ){
                if( !$(this).is('.hthb-state--minimized') ){
                    $(this).trigger_click_on_close_button(); // hide
                }

            } else{
                if( !$(this).is('.hthb-state--open') && !$(this).is('.hthb-trigger-open-clicked') ){
                    $(this).trigger_click_on_open_button(); // show
                }
            }
            
        } else {
            console.log(`Invalid formate  Scroll to show=${typeof scroll_to_show} ${scroll_to_show } scroll_to_hide=${typeof scroll_to_hide} ${scroll_to_hide}`);
        }
    }

    $(document).ready(function(){
        $(".hthb-countdown").each(function(){
            var countdown_id = "#"+$(this).attr('id')+" .hthb-countdown-section .hthb-countdown-wrap",
                finalDate    = $(countdown_id).data('countdown'),
                customLabel  = $(countdown_id).data('custom_label');
            $(countdown_id).countdown(finalDate, function (event) {
                $(countdown_id+' .countdown-day').html(event.strftime('%D'));
                $(countdown_id+' .countdown-day-text').html(customLabel.day);
                $(countdown_id+' .countdown-hour').html(event.strftime('%H'));
                $(countdown_id+' .countdown-hour-text').html(customLabel.hour);
                $(countdown_id+' .countdown-minute').html(event.strftime('%M'));
                $(countdown_id+' .countdown-minite-text').html(customLabel.min);
                $(countdown_id+' .countdown-second').html(event.strftime('%S'));
                $(countdown_id+' .countdown-second-text').html(customLabel.sec);
            });
        });

    });
})(jQuery);