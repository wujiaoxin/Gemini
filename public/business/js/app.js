/**
Core script to handle the entire layout and base functions
**/
var g_orderType = {'1':'二手车按揭贷款','2':'二手车按揭垫资','3':'新车按揭贷款','4':'新车按揭垫资'};
var g_orderStatus = {'-2':'编辑中','-1':'已撤回','0':'待提交','1':'审核通过','2':'审核拒绝','3':'资料审核','4':'额度审核','5':'补充资料','10':'初级审核','11':'信用审核','12':'额度审核','13':'财务审核'};
var g_financeStatus ={'1':'待支付','2':'已支付','3':'已放款','4':'已提现'};
var g_repayStatus = {'-2':'审核中','-1':'未还','1':'已还','2':'逾期'};
var g_transactionType = {'5':'支付款项','1':'垫资到账','2':'垫资还款','3':'充值','4':'提现'};
var g_dealObj = {'0':'系统','1':'商户'};
var bankChoice = {
        '1':'中国银行',
        '2':'工商银行',
        '3':'农业银行',
        '4':'交通银行',
        '5':'广发银行',
        '6':'深发银行',
        '7':'建设银行',
        '8':'上海浦发银行',
        '9':'浙江泰隆商业银行',
        '10':'招商银行',
        '11':'邮政储蓄银行',
        '12':'民生银行',
        '13':'兴业银行',
        '14':'广东发展银行',
        '15':'东莞银行',
        '16':'深圳发展银行',
        '17':'中信银行',
        '18':'华夏银行',
        '19':'中国光大银行',
        '20':'北京银行',
        '21':'上海银行',
        '22':'天津银行',
        '23':'大连银行',
        '24':'杭州银行',
        '25':'宁波银行',
        '26':'厦门银行',
        '27':'广州银行',
        '28':'平安银行',
        '29':'浙商银行',
        '30':'上海农村商业银行',
        '31':'重庆银行',
        '32':'江苏银行',
        '33':'农村信用合作社',
        '34':'广州农村商业银行',
        '35':'四川成都龙泉驿稠州村镇银行',
        '36':'内蒙古银行',
        '37':'深圳农村商业银行',
        '38':'贵阳银行',
        '39':'贵州银行',
        '40':'贵阳农村商业银行',
        '41':'南充市商业银行',
        '42':'东莞农商银行',
        '43':'徽商银行',
        '44':'河北银行',
        '45':'重庆农村商业银行',
        '46':'呼和浩特金谷农村商业银行',
        '47':'吴江农村商业银行',
        '48':'宁夏银行',
        '49':'石嘴山银行',
        '50':'黄河农村商业银行',
        '51':'德阳银行',
        '52':'昆明富滇银行',
        '53':'云南省农村信用社',
        '54':'郑州银行',
        '55':'潍坊银行',
        '56':'渤海银行',
        '57':'安徽凤阳农村商业银行',
        '58':'富滇银行',
        '59':'玉溪市商业银行',
        '60':'曲靖市商业银行',
        '61':'泰安市商业银行',
        '62':'汇丰银行',
        '63':'河北邯郸农村信用社',
        '64':'江苏江南农村商业银行',
        '65':'湖北省农村信用社',
        '66':'湖北银行',
        '67':'汉口银行',
        '68':'襄阳市农村商业银行',
        '69':'南京银行',
        '70':'贵州花溪农村商业银行',
        '71':'包商银行',
        '72':'柳州银行',
        '73':'广西农村信用社 ',
        '74':'桂林银行',
        '75':'广西北部湾银行',
        '76':'贵州贞丰农村商业银行股份有限公司',
        '77':'四川农村信用社',
        '78':'长春农商银行',
        '79':'吉林省农业信用社',
        '80':'吉林银行',
        '81':'浙江农信银行',
        '82':'苏州银行',
        '83':'江苏长江商业银行',
        '84':'北京农村商业银行',
        '85':'合肥科技农村商业银行',
        '86':'湖北嘉鱼农村商业银行',
        '87':'广东顺德农村商业银行',
        '88':'恒丰银行',
        '89':'九江银行'
    };
var apiUrl = "";
var App = function () {

    // IE mode
    var isRTL = false;
    var isIE8 = false;
    var isIE9 = false;
    var isIE10 = false;

    var sidebarWidth = 225;
    var sidebarCollapsedWidth = 35;

    var responsiveHandlers = [];

    // theme layout color set
    var layoutColorCodes = {
        'blue': '#4b8df8',
        'red': '#e02222',
        'green': '#35aa47',
        'purple': '#852b99',
        'grey': '#555555',
        'light-grey': '#fafafa',
        'yellow': '#ffb848'
    };

    var handleInit = function() {

        if ($('body').css('direction') === 'rtl') {
            isRTL = true;
        }

        isIE8 = !! navigator.userAgent.match(/MSIE 8.0/);
        isIE9 = !! navigator.userAgent.match(/MSIE 9.0/);
        isIE10 = !! navigator.userAgent.match(/MSIE 10/);

        if (isIE10) {
            jQuery('html').addClass('ie10'); // detect IE10 version
        }
    }

    var handleDesktopTabletContents = function () {
        // loops all page elements with "responsive" class and applies classes for tablet mode
        // For metornic  1280px or less set as tablet mode to display the content properly
        if ($(window).width() <= 1280 || $('body').hasClass('page-boxed')) {
            $(".responsive").each(function () {
                var forTablet = $(this).attr('data-tablet');
                var forDesktop = $(this).attr('data-desktop');
                if (forTablet) {
                    $(this).removeClass(forDesktop);
                    $(this).addClass(forTablet);
                }
            });
        }

        // loops all page elements with "responsive" class and applied classes for desktop mode
        // For metornic  higher 1280px set as desktop mode to display the content properly
        if ($(window).width() > 1280 && $('body').hasClass('page-boxed') === false) {
            $(".responsive").each(function () {
                var forTablet = $(this).attr('data-tablet');
                var forDesktop = $(this).attr('data-desktop');
                if (forTablet) {
                    $(this).removeClass(forTablet);
                    $(this).addClass(forDesktop);
                }
            });
        }
    }

    var handleSidebarState = function () {
        // remove sidebar toggler if window width smaller than 900(for table and phone mode)
        if ($(window).width() < 980) {
            $('body').removeClass("page-sidebar-closed");
        }
    }

    var runResponsiveHandlers = function () {
        // reinitialize other subscribed elements
        for (var i in responsiveHandlers) {
            var each = responsiveHandlers[i];
            each.call();
        }
    }

    var handleResponsive = function () {
        handleTooltips();
        handleSidebarState();
        handleDesktopTabletContents();
        handleSidebarAndContentHeight();
        handleChoosenSelect();
        handleFixedSidebar();
        runResponsiveHandlers();
    }

    var handleResponsiveOnInit = function () {
        handleSidebarState();
        handleDesktopTabletContents();
        handleSidebarAndContentHeight();
    }

    var handleResponsiveOnResize = function () {
        var resize;
        if (isIE8) {
            var currheight;
            $(window).resize(function() {
                if(currheight == document.documentElement.clientHeight) {
                    return; //quite event since only body resized not window.
                }
                if (resize) {
                    clearTimeout(resize);
                }
                resize = setTimeout(function() {
                    handleResponsive();
                }, 50); // wait 50ms until window resize finishes.
                currheight = document.documentElement.clientHeight; // store last body client height
            });
        } else {
            $(window).resize(function() {
                if (resize) {
                    clearTimeout(resize);
                }
                resize = setTimeout(function() {
                    // console.log('resize');
                    handleResponsive();
                }, 50); // wait 50ms until window resize finishes.
            });
        }
    }

    //* BEGIN:CORE HANDLERS *//
    // this function handles responsive layout on screen size resize or mobile device rotate.

    var handleSidebarAndContentHeight = function () {
        var content = $('.page-content');
        var sidebar = $('.page-sidebar');
        var body = $('body');
        var height;

        if (body.hasClass("page-footer-fixed") === true && body.hasClass("page-sidebar-fixed") === false) {
            var available_height = $(window).height() - $('.footer').height();
            if (content.height() <  available_height) {
                content.attr('style', 'min-height:' + available_height + 'px !important');
            }
        } else {
            if (body.hasClass('page-sidebar-fixed')) {
                height = _calculateFixedSidebarViewportHeight();
            } else {
                height = sidebar.height() + 20;
            }
            if (height >= content.height()) {
                content.attr('style', 'min-height:' + height + 'px !important');
            }
        }
    }

    var handleSidebarMenu = function () {
        jQuery('.page-sidebar').on('click', 'li > a', function (e) {
                var thisUrl = window.location.pathname;
                var navLiUrl = $(e.currentTarget).attr('href');
                if(thisUrl.indexOf(navLiUrl) != -1){
                    e.preventDefault();
                }
                if ($(this).next().hasClass('sub-menu') == false) {
                    if ($('.btn-navbar').hasClass('collapsed') == false) {
                        $('.btn-navbar').click();
                    }
                    return;
                }

                var parent = $(this).parent().parent();

                parent.children('li.open').children('a').children('.arrow').removeClass('open');
                parent.children('li.open').children('.sub-menu').slideUp(200);
                parent.children('li.open').removeClass('open');

                var sub = jQuery(this).next();
                if (sub.is(":visible")) {
                    jQuery('.arrow', jQuery(this)).removeClass("open");
                    jQuery(this).parent().removeClass("open");
                    sub.slideUp(200, function () {
                            handleSidebarAndContentHeight();
                        });
                } else {
                    jQuery('.arrow', jQuery(this)).addClass("open");
                    jQuery(this).parent().addClass("open");
                    sub.slideDown(200, function () {
                            handleSidebarAndContentHeight();
                        });
                }

                // e.preventDefault();
            });

        // handle ajax links
        jQuery('.page-sidebar').on('click', ' li > a.ajaxify', function (e) {
                e.preventDefault();
                App.scrollTop();

                var url = $(this).attr("href");
                var menuContainer = jQuery('.page-sidebar ul');
                var pageContent = $('.page-content');
                var pageContentBody = $('.page-content .page-content-body');

                menuContainer.children('li.active').removeClass('active');
                menuContainer.children('arrow.open').removeClass('open');

                $(this).parents('li').each(function () {
                        $(this).addClass('active');
                        $(this).children('a > span.arrow').addClass('open');
                    });
                $(this).parents('li').addClass('active');

                App.blockUI(pageContent, false);

                $.post(url, {}, function (res) {
                        App.unblockUI(pageContent);
                        pageContentBody.html(res);
                        App.fixContentHeight(); // fix content height
                        App.initUniform(); // initialize uniform elements
                    });
            });
    }

    var _calculateFixedSidebarViewportHeight = function () {
        var sidebarHeight = $(window).height() - $('.header').height() + 1;
        if ($('body').hasClass("page-footer-fixed")) {
            sidebarHeight = sidebarHeight - $('.footer').height();
        }

        return sidebarHeight;
    }

    var handleFixedSidebar = function () {
        var menu = $('.page-sidebar-menu');

        if (menu.parent('.slimScrollDiv').size() === 1) { // destroy existing instance before updating the height
            menu.slimScroll({
                destroy: true
            });
            menu.removeAttr('style');
            $('.page-sidebar').removeAttr('style');
        }

        if ($('.page-sidebar-fixed').size() === 0) {
            handleSidebarAndContentHeight();
            return;
        }

        if ($(window).width() >= 980) {
            var sidebarHeight = _calculateFixedSidebarViewportHeight();

            menu.slimScroll({
                size: '7px',
                color: '#a1b2bd',
                opacity: .3,
                position: isRTL ? 'left' : ($('.page-sidebar-on-right').size() === 1 ? 'left' : 'right'),
                height: sidebarHeight,
                allowPageScroll: false,
                disableFadeOut: false
            });
            handleSidebarAndContentHeight();
        }
    }

    var handleFixedSidebarHoverable = function () {
        if ($('body').hasClass('page-sidebar-fixed') === false) {
            return;
        }

        $('.page-sidebar').off('mouseenter').on('mouseenter', function () {
            var body = $('body');

            if ((body.hasClass('page-sidebar-closed') === false || body.hasClass('page-sidebar-fixed') === false) || $(this).hasClass('page-sidebar-hovering')) {
                return;
            }

            body.removeClass('page-sidebar-closed').addClass('page-sidebar-hover-on');
            $(this).addClass('page-sidebar-hovering');
            $(this).animate({
                width: sidebarWidth
            }, 400, '', function () {
                $(this).removeClass('page-sidebar-hovering');
            });
        });

        $('.page-sidebar').off('mouseleave').on('mouseleave', function () {
            var body = $('body');

            if ((body.hasClass('page-sidebar-hover-on') === false || body.hasClass('page-sidebar-fixed') === false) || $(this).hasClass('page-sidebar-hovering')) {
                return;
            }

            $(this).addClass('page-sidebar-hovering');
            $(this).animate({
                width: sidebarCollapsedWidth
            }, 400, '', function () {
                $('body').addClass('page-sidebar-closed').removeClass('page-sidebar-hover-on');
                $(this).removeClass('page-sidebar-hovering');
            });
        });
    }

    var handleSidebarToggler = function () {
        // handle sidebar show/hide
        $('.page-sidebar').on('click', '.sidebar-toggler', function (e) {
            var body = $('body');
            var sidebar = $('.page-sidebar');

            if ((body.hasClass("page-sidebar-hover-on") && body.hasClass('page-sidebar-fixed')) || sidebar.hasClass('page-sidebar-hovering')) {
                body.removeClass('page-sidebar-hover-on');
                sidebar.css('width', '').hide().show();
                e.stopPropagation();
                runResponsiveHandlers();
                return;
            }

            $(".sidebar-search", sidebar).removeClass("open");

            if (body.hasClass("page-sidebar-closed")) {
                body.removeClass("page-sidebar-closed");
                if (body.hasClass('page-sidebar-fixed')) {
                    sidebar.css('width', '');
                }
            } else {
                body.addClass("page-sidebar-closed");
            }
            runResponsiveHandlers();
        });

        // handle the search bar close
        $('.page-sidebar').on('click', '.sidebar-search .remove', function (e) {
            e.preventDefault();
            $('.sidebar-search').removeClass("open");
        });

        // handle the search query submit on enter press
        $('.page-sidebar').on('keypress', '.sidebar-search input', function (e) {
            if (e.which == 13) {
                window.location.href = "extra_search.html";
                return false; //<---- Add this line
            }
        });

        // handle the search submit
        $('.sidebar-search .submit').on('click', function (e) {
            e.preventDefault();

                if ($('body').hasClass("page-sidebar-closed")) {
                    if ($('.sidebar-search').hasClass('open') == false) {
                        if ($('.page-sidebar-fixed').size() === 1) {
                            $('.page-sidebar .sidebar-toggler').click(); //trigger sidebar toggle button
                        }
                        $('.sidebar-search').addClass("open");
                    } else {
                        window.location.href = "extra_search.html";
                    }
                } else {
                    window.location.href = "extra_search.html";
                }
        });
    }

    var handleHorizontalMenu = function () {
        //handle hor menu search form toggler click
        $('.header').on('click', '.hor-menu .hor-menu-search-form-toggler', function (e) {
                if ($(this).hasClass('hide')) {
                    $(this).removeClass('hide');
                    $('.header .hor-menu .search-form').hide();
                } else {
                    $(this).addClass('hide');
                    $('.header .hor-menu .search-form').show();
                }
                e.preventDefault();
            });

        //handle hor menu search button click
        $('.header').on('click', '.hor-menu .search-form .btn', function (e) {
                window.location.href = "extra_search.html";
                e.preventDefault();
            });

        //handle hor menu search form on enter press
        $('.header').on('keypress', '.hor-menu .search-form input', function (e) {
                if (e.which == 13) {
                    window.location.href = "extra_search.html";
                    return false;
                }
            });
    }

    var handleGoTop = function () {
        /* set variables locally for increased performance */
        jQuery('.footer').on('click', '.go-top', function (e) {
                App.scrollTo();
                e.preventDefault();
            });
    }

    var handlePortletTools = function () {
        jQuery('body').on('click', '.portlet .tools a.remove', function (e) {
            e.preventDefault();
                var removable = jQuery(this).parents(".portlet");
                if (removable.next().hasClass('portlet') || removable.prev().hasClass('portlet')) {
                    jQuery(this).parents(".portlet").remove();
                } else {
                    jQuery(this).parents(".portlet").parent().remove();
                }
        });

        jQuery('body').on('click', '.portlet .tools a.reload', function (e) {
            e.preventDefault();
                var el = jQuery(this).parents(".portlet");
                App.blockUI(el);
                window.setTimeout(function () {
                        App.unblockUI(el);
                    }, 1000);
        });

        jQuery('body').on('click', '.portlet .tools .collapse, .portlet .tools .expand', function (e) {
            e.preventDefault();
                var el = jQuery(this).closest(".portlet").children(".portlet-body");
                if (jQuery(this).hasClass("collapse")) {
                    jQuery(this).removeClass("collapse").addClass("expand");
                    el.slideUp(200);
                } else {
                    jQuery(this).removeClass("expand").addClass("collapse");
                    el.slideDown(200);
                }
        });
    }

    var handleUniform = function () {
        if (!jQuery().uniform) {
            return;
        }
        var test = $("input[type=checkbox]:not(.toggle), input[type=radio]:not(.toggle, .star)");
        if (test.size() > 0) {
            test.each(function () {
                    if ($(this).parents(".checker").size() == 0) {
                        $(this).show();
                        $(this).uniform();
                    }
                });
        }
    }

    var handleAccordions = function () {
        $(".accordion").collapse().height('auto');

        var lastClicked;

        //add scrollable class name if you need scrollable panes
        jQuery('body').on('click', '.accordion.scrollable .accordion-toggle', function () {
            lastClicked = jQuery(this);
        }); //move to faq section

        jQuery('body').on('shown', '.accordion.scrollable', function () {
            jQuery('html,body').animate({
                scrollTop: lastClicked.offset().top - 150
            }, 'slow');
        });
    }

    var handleTabs = function () {

        // function to fix left/right tab contents
        var fixTabHeight = function(tab) {
            $(tab).each(function() {
                var content = $($($(this).attr("href")));
                var tab = $(this).parent().parent();
                if (tab.height() > content.height()) {
                    content.css('min-height', tab.height());
                }
            });
        }

        // fix tab content on tab shown
        $('body').on('shown', '.nav.nav-tabs.tabs-left a[data-toggle="tab"], .nav.nav-tabs.tabs-right a[data-toggle="tab"]', function(){
            fixTabHeight($(this));
        });

        $('body').on('shown', '.nav.nav-tabs', function(){
            handleSidebarAndContentHeight();
        });

        //fix tab contents for left/right tabs
        fixTabHeight('.nav.nav-tabs.tabs-left > li.active > a[data-toggle="tab"], .nav.nav-tabs.tabs-right > li.active > a[data-toggle="tab"]');

        //activate tab if tab id provided in the URL
        if(location.hash) {
            var tabid = location.hash.substr(1);
            $('a[href="#'+tabid+'"]').click();
        }
    }

    var handleScrollers = function () {
        $('.scroller').each(function () {
                $(this).slimScroll({
                        size: '7px',
                        color: '#a1b2bd',
                        position: isRTL ? 'left' : 'right',
                        height: $(this).attr("data-height"),
                        alwaysVisible: ($(this).attr("data-always-visible") == "1" ? true : false),
                        railVisible: ($(this).attr("data-rail-visible") == "1" ? true : false),
                        disableFadeOut: true
                    });
            });
    }

    var handleTooltips = function () {
        if (App.isTouchDevice()) { // if touch device, some tooltips can be skipped in order to not conflict with click events
            jQuery('.tooltips:not(.no-tooltip-on-touch-device)').tooltip();
        } else {
            jQuery('.tooltips').tooltip();
        }
    }

    var handleDropdowns = function () {
        $('body').on('click', '.dropdown-menu.hold-on-click', function(e){
            e.stopPropagation();
        })
    }

    var handlePopovers = function () {
        jQuery('.popovers').popover();
    }

    var handleChoosenSelect = function () {
        if (!jQuery().chosen) {
            return;
        }

        $(".chosen").each(function () {
            $(this).chosen({
                allow_single_deselect: $(this).attr("data-with-diselect") === "1" ? true : false
            });
        });
    }

    var handleFancybox = function () {
        if (!jQuery.fancybox) {
            return;
        }

        if (jQuery(".fancybox-button").size() > 0) {
            jQuery(".fancybox-button").fancybox({
                groupAttr: 'data-rel',
                prevEffect: 'none',
                nextEffect: 'none',
                closeBtn: true,
                helpers: {
                    title: {
                        type: 'inside'
                    }
                }
            });
        }
    }

    var handleTheme = function () {

        var panel = $('.color-panel');

        if ($('body').hasClass('page-boxed') == false) {
            $('.layout-option', panel).val("fluid");
        }

        $('.sidebar-option', panel).val("default");
        $('.header-option', panel).val("fixed");
        $('.footer-option', panel).val("default");

        //handle theme layout
        var resetLayout = function () {
            $("body").
                removeClass("page-boxed").
                removeClass("page-footer-fixed").
                removeClass("page-sidebar-fixed").
                removeClass("page-header-fixed");

            $('.header > .navbar-inner > .container').removeClass("container").addClass("container-fluid");

            if ($('.page-container').parent(".container").size() === 1) {
                $('.page-container').insertAfter('.header');
            }

            if ($('.footer > .container').size() === 1) {
                $('.footer').html($('.footer > .container').html());
            } else if ($('.footer').parent(".container").size() === 1) {
                $('.footer').insertAfter('.page-container');
            }

            $('body > .container').remove();
        }

        var lastSelectedLayout = '';

        var setLayout = function () {

            var layoutOption = $('.layout-option', panel).val();
            var sidebarOption = $('.sidebar-option', panel).val();
            var headerOption = $('.header-option', panel).val();
            var footerOption = $('.footer-option', panel).val();

            if (sidebarOption == "fixed" && headerOption == "default") {
                alert('Default Header with Fixed Sidebar option is not supported. Proceed with Default Header with Default Sidebar.');
                $('.sidebar-option', panel).val("default");
                sidebarOption = 'default';
            }

            resetLayout(); // reset layout to default state

            if (layoutOption === "boxed") {
                $("body").addClass("page-boxed");

                // set header
                $('.header > .navbar-inner > .container-fluid').removeClass("container-fluid").addClass("container");
                var cont = $('.header').after('<div class="container"></div>');

                // set content
                $('.page-container').appendTo('body > .container');

                // set footer
                if (footerOption === 'fixed' || sidebarOption === 'default') {
                    $('.footer').html('<div class="container">'+$('.footer').html()+'</div>');
                } else {
                    $('.footer').appendTo('body > .container');
                }
            }

            if (lastSelectedLayout != layoutOption) {
                //layout changed, run responsive handler:
                runResponsiveHandlers();
            }
            lastSelectedLayout = layoutOption;

            //header
            if (headerOption === 'fixed') {
                $("body").addClass("page-header-fixed");
                $(".header").removeClass("navbar-static-top").addClass("navbar-fixed-top");
            } else {
                $("body").removeClass("page-header-fixed");
                $(".header").removeClass("navbar-fixed-top").addClass("navbar-static-top");
            }

            //sidebar
            if (sidebarOption === 'fixed') {
                $("body").addClass("page-sidebar-fixed");
            } else {
                $("body").removeClass("page-sidebar-fixed");
            }

            //footer
            if (footerOption === 'fixed') {
                $("body").addClass("page-footer-fixed");
            } else {
                $("body").removeClass("page-footer-fixed");
            }

            handleSidebarAndContentHeight(); // fix content height
            handleFixedSidebar(); // reinitialize fixed sidebar
            handleFixedSidebarHoverable(); // reinitialize fixed sidebar hover effect
        }

        // handle theme colors
        var setColor = function (color) {
            $('#style_color').attr("href", "assets/css/themes/" + color + ".css");
            $.cookie('style_color', color);
        }

        $('.icon-color', panel).click(function () {
            $('.color-mode').show();
            $('.icon-color-close').show();
        });

        $('.icon-color-close', panel).click(function () {
            $('.color-mode').hide();
            $('.icon-color-close').hide();
        });

        $('li', panel).click(function () {
            var color = $(this).attr("data-style");
            setColor(color);
            $('.inline li', panel).removeClass("current");
            $(this).addClass("current");
        });

        $('.layout-option, .header-option, .sidebar-option, .footer-option', panel).change(setLayout);
    }

    var handleFixInputPlaceholderForIE = function () {
        //fix html5 placeholder attribute for ie7 & ie8
        if (isIE8 || isIE9) { // ie7&ie8
            // this is html5 placeholder fix for inputs, inputs with placeholder-no-fix class will be skipped(e.g: we need this for password fields)
            jQuery('input[placeholder]:not(.placeholder-no-fix), textarea[placeholder]:not(.placeholder-no-fix)').each(function () {

                var input = jQuery(this);

                if(input.val()=='' && input.attr("placeholder") != '') {
                    input.addClass("placeholder").val(input.attr('placeholder'));
                }

                input.focus(function () {
                    if (input.val() == input.attr('placeholder')) {
                        input.val('');
                    }
                });

                input.blur(function () {
                    if (input.val() == '' || input.val() == input.attr('placeholder')) {
                        input.val(input.attr('placeholder'));
                    }
                });
            });
        }
    }

    //* END:CORE HANDLERS *//

    return {

        //main function to initiate template pages
        init: function () {

            //IMPORTANT!!!: Do not modify the core handlers call order.

            //core handlers
            handleInit();
            handleResponsiveOnResize(); // set and handle responsive
            handleUniform();
            handleScrollers(); // handles slim scrolling contents
            handleResponsiveOnInit(); // handler responsive elements on page load

            //layout handlers
            handleFixedSidebar(); // handles fixed sidebar menu
            handleFixedSidebarHoverable(); // handles fixed sidebar on hover effect
            handleSidebarMenu(); // handles main menu
            handleHorizontalMenu(); // handles horizontal menu
            handleSidebarToggler(); // handles sidebar hide/show
            handleFixInputPlaceholderForIE(); // fixes/enables html5 placeholder attribute for IE9, IE8
            handleGoTop(); //handles scroll to top functionality in the footer
            handleTheme(); // handles style customer tool

            //ui component handlers
            handlePortletTools(); // handles portlet action bar functionality(refresh, configure, toggle, remove)
            handleDropdowns(); // handle dropdowns
            handleTabs(); // handle tabs
            handleTooltips(); // handle bootstrap tooltips
            handlePopovers(); // handles bootstrap popovers
            handleAccordions(); //handles accordions
            handleChoosenSelect(); // handles bootstrap chosen dropdowns

            App.addResponsiveHandler(handleChoosenSelect); // reinitiate chosen dropdown on main content resize. disable this line if you don't really use chosen dropdowns.
        },

        fixContentHeight: function () {
            handleSidebarAndContentHeight();
        },

        addResponsiveHandler: function (func) {
            responsiveHandlers.push(func);
        },

        // useful function to make equal height for contacts stand side by side
        setEqualHeight: function (els) {
            var tallestEl = 0;
            els = jQuery(els);
            els.each(function () {
                    var currentHeight = $(this).height();
                    if (currentHeight > tallestEl) {
                        tallestColumn = currentHeight;
                    }
                });
            els.height(tallestEl);
        },

        // wrapper function to scroll to an element
        scrollTo: function (el, offeset) {
            pos = el ? el.offset().top : 0;
            jQuery('html,body').animate({
                    scrollTop: pos + (offeset ? offeset : 0)
                }, 'slow');
        },

        scrollTop: function () {
            App.scrollTo();
        },

        // wrapper function to  block element(indicate loading)
        blockUI: function (el, centerY) {
            var el = jQuery(el);
            el.block({
                    message: '<img src="./assets/img/ajax-loading.gif" align="">',
                    centerY: centerY != undefined ? centerY : true,
                    css: {
                        top: '10%',
                        border: 'none',
                        padding: '2px',
                        backgroundColor: 'none'
                    },
                    overlayCSS: {
                        backgroundColor: '#000',
                        opacity: 0.05,
                        cursor: 'wait'
                    }
                });
        },

        // wrapper function to  un-block element(finish loading)
        unblockUI: function (el) {
            jQuery(el).unblock({
                    onUnblock: function () {
                        jQuery(el).removeAttr("style");
                    }
                });
        },

        // initializes uniform elements
        initUniform: function (els) {

            if (els) {
                jQuery(els).each(function () {
                        if ($(this).parents(".checker").size() == 0) {
                            $(this).show();
                            $(this).uniform();
                        }
                    });
            } else {
                handleUniform();
            }

        },

        // initializes choosen dropdowns
        initChosenSelect: function (els) {
            $(els).chosen({
                    allow_single_deselect: true
                });
        },

        initFancybox: function () {
            handleFancybox();
        },

        getActualVal: function (el) {
            var el = jQuery(el);
            if (el.val() === el.attr("placeholder")) {
                return "";
            }

            return el.val();
        },

        getURLParameter: function (paramName) {
            var searchString = window.location.search.substring(1),
                i, val, params = searchString.split("&");

            for (i = 0; i < params.length; i++) {
                val = params[i].split("=");
                if (val[0] == paramName) {
                    return unescape(val[1]);
                }
            }
            return null;
        },

        // check for device touch support
        isTouchDevice: function () {
            try {
                document.createEvent("TouchEvent");
                return true;
            } catch (e) {
                return false;
            }
        },

        isIE8: function () {
            return isIE8;
        },

        isRTL: function () {
            return isRTL;
        },

        getLayoutColorCode: function (name) {
            if (layoutColorCodes[name]) {
                return layoutColorCodes[name];
            } else {
                return '';
            }
        }

    };

}();

//定义ui_alert
function ui_alert(msg,type,position){
   $.messager.show(msg, {placement: position,type:type});
};

// function ui_alert(classname,message){
//         jQuery('.alert').removeClass('alert-error alert-success').children('span').text(message).end()
//                 .addClass(classname).show();
//         setTimeout("$('.alert').slideUp('slow')",3000);
//     };
// jQuery('.alert > .close').click(function(){
//     jQuery('.alert').removeClass('alert-error alert-success').hide();
// });

function ajax_jquery(options) {
    if (options == undefined) {
        options = new Object();
    } else if (typeof options != 'object') {
        return;
    } else {
    }
    var sid =localStorage.getItem('sid');
    var options_default = {
        url: '',
        type: 'POST',
        dataType: 'json',
        data: {
            sid: sid
        },
        success: _ajax_success,
        error: _ajax_error,
        timeout: 60000,
        async: true,
        cache:false
    };
    var options_merge = new Object();
    $.extend(true,options_merge, options_default, options);

    $.ajax(options_merge);
}

function _ajax_success(data, textStatus) {
    // data 可能是 xmlDoc, jsonObj, html, text, 等等...
    this; // 调用本次AJAX请求时传递的options参数
}

function _ajax_error(XMLHttpRequest, textStatus, errorThrown) {
    // 通常 textStatus 和 errorThrown 之中只有一个会包含信息
//    this; // 调用本次AJAX请求时传递的options参数
    var session_status = XMLHttpRequest.getResponseHeader("Session-Status"); //通过XMLHttpRequest取得响应头，Session-Status，
    if (session_status == 'TimeOut') {
        ui_alert('登录超时，请重新登录');
        // window.location.href = "/mobile/user/login"; //如果超时就处理 ，指定要跳转的页面
    } else if (session_status == 'Empty') {
        ui_alert('权限限制，请联系管理员');
    } else if (textStatus == 'timeout') {
        ui_alert('加载超时，请重试');
    } else {
        console.log("XHR="+XMLHttpRequest+"\ntextStatus="+textStatus+"\nerrorThrown=" + errorThrown);
    }
}

function setNavActive (navId) {
  var thisUrl = window.location.pathname;
  var navItemList = $('#' + navId + " li");
  $('#' + navId + " li").each(function(){
    var navLiUrl = $(this).find('a').attr('href');
    if(thisUrl.indexOf(navLiUrl) != -1){
        $(this).addClass("active")
          .parents("li").addClass("active");
        var pageTitle = $(this).children('a').text();
        $(".page-title").text(pageTitle);
        $("#page-sidebar-menu > li.active").find('a > span.title').after('<span class="selected"></span>').end()
                                            .find('a > span.arrow').addClass('open');
    }
  });
}
setNavActive("page-sidebar-menu");

function logout() {
    ajax_jquery({
        url: apiUrl +'/business/login/logout',
        success:function(resp){
            if (resp.code == "1" ) {
                    localStorage.clear();
                    window.location.href = "/business/login/login";
            } else {
                if (typeof(resp.msg) == 'string') {
                    ui_alert(resp.msg);
                }
            }
        }
    });
}

$("#editPasswordBtn").click(function(event) {
    var oldPassword = $("#oldPassword").val();
    var newPassword = $("#newPassword").val();
    var confirmPassword = $("#confirmPassword").val();
    var token = localStorage.getItem('token');
    if(oldPassword == ""){
        ui_alert("请输入原密码");
        return;
    }else if(!validatePassword(oldPassword)){
        ui_alert("原密码输入有误");
        return;
    }else if(newPassword == ""){
        ui_alert("请输入新密码");
        return;
    }else if(!validatePassword(newPassword)){
        ui_alert("请输入8-16位英文数字组合");
        return;
    }else if(confirmPassword == ""){
        ui_alert("请确认新密码");
        return;
    }else if(newPassword != confirmPassword){
        ui_alert("两次输入密码不一致");
        return;
    }

    ajax_jquery({
        url: apiUrl +'/business/user/editPassword?t='+Math.random(),
        data:{
            'oldPassword': oldPassword,
            'newPassword': newPassword
        },
        success:function(resp){
            if (resp.code == "1" ) {
                    ui_alert("密码重置成功","success");
                    setTimeout('window.location.reload();',1000);
            } else {
                if (typeof(resp.msg) == 'string') {
                    ui_alert(resp.msg);
                    return false;
                }
            }
        }
    });
});

// 格式化数字20,000,00.00
function formatAmount(n) {
    if(!n){
        return '0.00';
    }
    n = parseFloat(n).toFixed(2);
    n = n.toString().replace(/\B(?=(?:\d{3})+\b)/g, ',');
    return n;
}

// 格式化日期时间
function formatDatetime(timeStr){
    var timeStr = timeStr*1000;
    var now =new Date(timeStr);
    var year=now.getFullYear();
    var month=now.getMonth()+1;
    var date=now.getDate();
    var hour=fillZero(now.getHours());
    var minute=fillZero(now.getMinutes());
    var second=fillZero(now.getSeconds());
    return   year+"-"+month+"-"+date+"   "+hour+":"+minute+":"+second;
}

// 格式化日期
function formatDate(timeStr){
    var timeStr = timeStr*1000;
    var now = new Date(timeStr);
    var year = now.getFullYear();
    var month = fillZero(now.getMonth()+1);
    var date = fillZero(now.getDate());
    return   year+"-"+month+"-"+date;
}

function fillZero(i){
    if (i<10){
        i="0" + i;
    }
    return i
}

//发送短信验证码
function sendSms(id) {
    var formName = $(id).attr('data-form');
    var mobile = $('#'+formName+'-username').val();
    var imgverify = $('#'+formName+'-rvalicode').val();
    if (mobile == "") {
        ui_alert("请输入手机号!");
        return false;
    }else if (!validatePhoneNumber(mobile)) {
        ui_alert("请输入正确的手机号!");
        return false;
    }
    ajax_jquery({
        url: apiUrl + '/business/user/sendSmsVerify',
        data: {
            "mobile": mobile,
         },
        success: function (resp) {
            if (resp.code == "1") {
                ui_alert("验证码发送成功,请注意查收","success");
                settime();
            } else {
                if (typeof(resp.msg) == 'string' && resp.msg != '') {
                    ui_alert(resp.msg);
                } else {
                    ui_alert("验证码发送失败");
                }
                return false;
            }
        }
    });
}

var countdown = 30;
    var isCounting = false;
    function settime() {
        if(!isCounting){
            countTime();
        }
    }
    function countTime(){
        isCounting = true;
        if (countdown == 0) {
            $(".getcode").removeAttr("disabled");
            $(".getcode").val("发送验证码");
            $(".getcode").html("发送验证码");
            countdown = 30;
            isCounting = false;
            return;
        } else {
            $(".getcode").attr("disabled", true);
            $(".getcode").val("重新发送(" + countdown + ")");
            $(".getcode").html("重新发送(" + countdown + ")");
            countdown--;
        }
            setTimeout(function () {
                countTime();
            }, 1000);
    }






