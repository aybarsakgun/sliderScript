if (typeof jQuery === "undefined") {
    throw new Error("Bu dosyayı işleme almak için önce jQuery tanımlamalısınız.");
}

$.SliderScript = {};
$.SliderScript.options = {
    colors: {
        red: '#F44336',
        pink: '#E91E63',
        purple: '#9C27B0',
        deepPurple: '#673AB7',
        indigo: '#3F51B5',
        blue: '#2196F3',
        lightBlue: '#03A9F4',
        cyan: '#00BCD4',
        teal: '#009688',
        green: '#4CAF50',
        lightGreen: '#8BC34A',
        lime: '#CDDC39',
        yellow: '#ffe821',
        amber: '#FFC107',
        orange: '#FF9800',
        deepOrange: '#FF5722',
        brown: '#795548',
        grey: '#9E9E9E',
        blueGrey: '#607D8B',
        black: '#000000',
        white: '#ffffff'
    },
    leftSideBar: {
        scrollColor: 'rgba(0,0,0,0.5)',
        scrollWidth: '4px',
        scrollAlwaysVisible: false,
        scrollBorderRadius: '0',
        scrollRailBorderRadius: '0',
        scrollActiveItemWhenPageLoad: true,
        breakpointWidth: 1170
    },
    dropdownMenu: {
        effectIn: 'fadeIn',
        effectOut: 'fadeOut'
    }
};

$.SliderScript.leftSideBar = {
    activate: function () {
        var _this = this;
        var $body = $('body');
        var $overlay = $('.overlay');

        $(window).click(function (e) {
            var $target = $(e.target);
            if (e.target.nodeName.toLowerCase() === 'i') { $target = $(e.target).parent(); }

            if (!$target.hasClass('bars') && _this.isOpen() && $target.parents('#leftsidebar').length === 0) {
                $body.removeClass('overlay-open');
            }
        });

        $.each($('.menu-toggle.toggled'), function (i, val) {
            $(val).next().slideToggle(0);
        });

        $.each($('.menu .list li.active'), function (i, val) {
            var $activeAnchors = $(val).find('a:eq(0)');

            $activeAnchors.addClass('toggled');
            $activeAnchors.next().show();
        });

        $('.menu-toggle').on('click', function (e) {
            var $this = $(this);
            var $content = $this.next();

            if ($($this.parents('ul')[0]).hasClass('list')) {
                var $not = $(e.target).hasClass('menu-toggle') ? e.target : $(e.target).parents('.menu-toggle');

                $.each($('.menu-toggle.toggled').not($not).next(), function (i, val) {
                    if ($(val).is(':visible')) {
                        $(val).prev().toggleClass('toggled');
                        $(val).slideUp();
                    }
                });
            }

            $this.toggleClass('toggled');
            $content.slideToggle(320);
        });

        _this.setMenuHeight(true);
        _this.checkStatusForResize(true);
        $(window).resize(function () {
            _this.setMenuHeight(false);
            _this.checkStatusForResize(false);
        });

        Waves.attach('.menu .list a', ['waves-block']);
        Waves.init();
    },
    setMenuHeight: function (isFirstTime) {
        if (typeof $.fn.slimScroll != 'undefined') {
            var configs = $.SliderScript.options.leftSideBar;
            var height = ($(window).height() - ($('.legal').outerHeight() + $('.user-info').outerHeight() + $('.navbar').innerHeight()));
            var $el = $('.list');

            if (!isFirstTime) {
                $el.slimscroll({
                    destroy: true
                });
            }

            $el.slimscroll({
                height: height + "px",
                color: configs.scrollColor,
                size: configs.scrollWidth,
                alwaysVisible: configs.scrollAlwaysVisible,
                borderRadius: configs.scrollBorderRadius,
                railBorderRadius: configs.scrollRailBorderRadius
            });

            if ($.SliderScript.options.leftSideBar.scrollActiveItemWhenPageLoad) {
                var item = $('.menu .list li.active')[0];
                if (item) {
                    var activeItemOffsetTop = item.offsetTop;
                    if (activeItemOffsetTop > 150) $el.slimscroll({ scrollTo: activeItemOffsetTop + 'px' });
                }
            }
        }
    },
    checkStatusForResize: function (firstTime) {
        var $body = $('body');
        var $openCloseBar = $('.navbar .navbar-header .bars');
        var width = $body.width();

        if (firstTime) {
            $body.find('.content, .sidebar').addClass('no-animate').delay(1000).queue(function () {
                $(this).removeClass('no-animate').dequeue();
            });
        }

        if (width < $.SliderScript.options.leftSideBar.breakpointWidth) {
            $body.addClass('ls-closed');
            $openCloseBar.fadeIn();
        }
        else {
            $body.removeClass('ls-closed');
            $openCloseBar.fadeOut();
        }
    },
    isOpen: function () {
        return $('body').hasClass('overlay-open');
    }
};

$.SliderScript.navbar = {
    activate: function () {
        var $body = $('body');
        var $overlay = $('.overlay');

        $('.bars').on('click', function () {
            $body.toggleClass('overlay-open');
            if ($body.hasClass('overlay-open')) { $overlay.fadeIn(); } else { $overlay.fadeOut(); }
        });

        $('.nav [data-close="true"]').on('click', function () {
            var isVisible = $('.navbar-toggle').is(':visible');
            var $navbarCollapse = $('.navbar-collapse');

            if (isVisible) {
                $navbarCollapse.slideUp(function () {
                    $navbarCollapse.removeClass('in').removeAttr('style');
                });
            }
        });
    }
};

$.SliderScript.input = {
    activate: function ($parentSelector) {
        $parentSelector = $parentSelector || $('body');

        $parentSelector.find('.form-control').focus(function () {
            $(this).closest('.form-line').addClass('focused');
        });

        $parentSelector.find('.form-control').focusout(function () {
            var $this = $(this);
            if ($this.parents('.form-group').hasClass('form-float')) {
                if ($this.val() == '') { $this.parents('.form-line').removeClass('focused'); }
            }
            else {
                $this.parents('.form-line').removeClass('focused');
            }
        });

        $parentSelector.on('click', '.form-float .form-line .form-label', function () {
            $(this).parent().find('input').focus();
        });
    }
};

$.SliderScript.select = {
    activate: function () {
        if ($.fn.selectpicker) { $('select:not(.ms)').selectpicker(); }
    }
};

$.SliderScript.dropdownMenu = {
    activate: function () {
        var _this = this;

        $('.dropdown, .dropup, .btn-group').on({
            "show.bs.dropdown": function () {
                var dropdown = _this.dropdownEffect(this);
                _this.dropdownEffectStart(dropdown, dropdown.effectIn);
            },
            "shown.bs.dropdown": function () {
                var dropdown = _this.dropdownEffect(this);
                if (dropdown.effectIn && dropdown.effectOut) {
                    _this.dropdownEffectEnd(dropdown, function () { });
                }
            },
            "hide.bs.dropdown": function (e) {
                var dropdown = _this.dropdownEffect(this);
                if (dropdown.effectOut) {
                    e.preventDefault();
                    _this.dropdownEffectStart(dropdown, dropdown.effectOut);
                    _this.dropdownEffectEnd(dropdown, function () {
                        dropdown.dropdown.removeClass('open');
                    });
                }
            }
        });

        Waves.attach('.dropdown-menu li a', ['waves-block']);
        Waves.init();
    },
    dropdownEffect: function (target) {
        var effectIn = $.SliderScript.options.dropdownMenu.effectIn, effectOut = $.SliderScript.options.dropdownMenu.effectOut;
        var dropdown = $(target), dropdownMenu = $('.dropdown-menu', target);

        if (dropdown.length > 0) {
            var udEffectIn = dropdown.data('effect-in');
            var udEffectOut = dropdown.data('effect-out');
            if (udEffectIn !== undefined) { effectIn = udEffectIn; }
            if (udEffectOut !== undefined) { effectOut = udEffectOut; }
        }

        return {
            target: target,
            dropdown: dropdown,
            dropdownMenu: dropdownMenu,
            effectIn: effectIn,
            effectOut: effectOut
        };
    },
    dropdownEffectStart: function (data, effectToStart) {
        if (effectToStart) {
            data.dropdown.addClass('dropdown-animating');
            data.dropdownMenu.addClass('animated dropdown-animated');
            data.dropdownMenu.addClass(effectToStart);
        }
    },
    dropdownEffectEnd: function (data, callback) {
        var animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
        data.dropdown.one(animationEnd, function () {
            data.dropdown.removeClass('dropdown-animating');
            data.dropdownMenu.removeClass('animated dropdown-animated');
            data.dropdownMenu.removeClass(data.effectIn);
            data.dropdownMenu.removeClass(data.effectOut);

            if (typeof callback == 'function') {
                callback();
            }
        });
    }
};

var edge = 'Microsoft Edge';
var ie10 = 'Internet Explorer 10';
var ie11 = 'Internet Explorer 11';
var opera = 'Opera';
var firefox = 'Mozilla Firefox';
var chrome = 'Google Chrome';
var safari = 'Safari';

$.SliderScript.browser = {
    activate: function () {
        var _this = this;
        var className = _this.getClassName();

        if (className !== '') $('html').addClass(_this.getClassName());
    },
    getBrowser: function () {
        var userAgent = navigator.userAgent.toLowerCase();

        if (/edge/i.test(userAgent)) {
            return edge;
        } else if (/rv:11/i.test(userAgent)) {
            return ie11;
        } else if (/msie 10/i.test(userAgent)) {
            return ie10;
        } else if (/opr/i.test(userAgent)) {
            return opera;
        } else if (/chrome/i.test(userAgent)) {
            return chrome;
        } else if (/firefox/i.test(userAgent)) {
            return firefox;
        } else if (!!navigator.userAgent.match(/Version\/[\d\.]+.*Safari/)) {
            return safari;
        }

        return undefined;
    },
    getClassName: function () {
        var browser = this.getBrowser();

        if (browser === edge) {
            return 'edge';
        } else if (browser === ie11) {
            return 'ie11';
        } else if (browser === ie10) {
            return 'ie10';
        } else if (browser === opera) {
            return 'opera';
        } else if (browser === chrome) {
            return 'chrome';
        } else if (browser === firefox) {
            return 'firefox';
        } else if (browser === safari) {
            return 'safari';
        } else {
            return '';
        }
    }
};

function isYoutubeUrl(url) {
    var p = /^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
    return (url.match(p)) ? RegExp.$1 : false;
}

$(function () {
    $.SliderScript.browser.activate();
    $.SliderScript.leftSideBar.activate();
    $.SliderScript.navbar.activate();
    $.SliderScript.dropdownMenu.activate();
    $.SliderScript.input.activate();
    $.SliderScript.select.activate();

    setTimeout(function () { $('.page-loader-wrapper').fadeOut(); }, 50);

    var previewMediaContent = $('span#previewMediaContent');
    var previewTextContent = $('#previewTextContent');
    var previewTitle = $('#previewTitle');
    var previewSubTitle = $('#previewSubTitle');

    var file = $('#file');
    var youtubeUrl = $('#youtube_url');

    var title = $('#title');
    var titleColor = $('#title_color');
    var titleFont = $('#title_font');
    var titleFontSize = $('#title_font_size');
    var titleAnimation = $('#title_animation');

    var subTitle = $('#sub_title');
    var subTitleColor = $('#sub_title_color');
    var subTitleFont = $('#sub_title_font');
    var subTitleFontSize = $('#sub_title_font_size');
    var subTitleAnimation = $('#sub_title_animation');

    var textDirection = $('#text_direction');

    function fileRead(input) {
        if (input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                if (input.files[0].type === 'image/png' || input.files[0].type === 'image/jpeg') {
                    previewMediaContent.html('<img src="' + e.target.result + '">');
                } else if (input.files[0].type === 'video/mp4') {
                    previewMediaContent.html('<video autoplay loop playsinline muted><source src="' + e.target.result + '" type="video/mp4"/></video>');
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    file.change(function() {
        if ($(this).val()) {
            $('#clearFileInput').show();
        } else {
            $('#clearFileInput').hide();
        }
        fileRead(this);
    });

    $('#clearFileInput').click(function(e) {
        e.preventDefault();
        file.val('');
        $('#clearFileInput').hide();
        var youtubeVideoId = isYoutubeUrl(youtubeUrl.val());
        if (youtubeVideoId) {
            previewMediaContent.html('<img src="https://i.ytimg.com/vi/' + youtubeVideoId + '/hqdefault.jpg">');
        } else {
            previewMediaContent.html('<img class="bg-black">');
        }
    });

    youtubeUrl.keyup(function() {
        var youtubeVideoId = isYoutubeUrl(youtubeUrl.val());
        if (youtubeVideoId) {
            previewMediaContent.html('<img src="https://i.ytimg.com/vi/' + youtubeVideoId + '/hqdefault.jpg">');
        } else {
            if (file.val()) {

                fileRead(document.getElementById('file'));
            } else {
                previewMediaContent.html('<img class="bg-black">');
            }
        }
    });

    title.keyup(function() {
        previewTitle.text(this.value);
    });

    titleFont.change(function() {
       previewTitle.css({'font-family': "'" + this.value + "', " + $('option:selected', this).attr('data-font-family')});
    });

    titleFontSize.change(function() {
        previewTitle.css({'font-size': this.value + 'px'});
    });

    subTitle.keyup(function() {
        previewSubTitle.text(this.value);
    });

    subTitleFont.change(function() {
        previewSubTitle.css({'font-family': "'" + this.value + "', " + $('option:selected', this).attr('data-font-family')});
    });

    subTitleFontSize.change(function() {
        previewSubTitle.css({'font-size': this.value + 'px'});
    });

    $(".logoutButton").on('click',(function(e) {
        $.ajax({
            url: "logout",
            type: "POST",
            contentType: false,
            cache: false,
            processData:false,
            headers : {
                'csrftoken': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data)
            {
                if(data == "1")
                {
                    window.location.href = 'home';
                }
            }
        });
    }));

    // $("#title_animation, #sub_title_animation").on('change',(function() {
    //     $('label[for="' + $(this).attr('id') + '"]').removeClass().addClass('animated ' + $(this).val());
    // }));
});
