<?php if ($pageRequest && $pageRequest == 'home') { ?>
<script type="text/javascript">
    $(document).ready(function() {
        $.ajax({
            url: "access-logs",
            type: "POST",
            contentType: false,
            cache: false,
            processData:false,
            headers : {
                'csrftoken': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data)
            {
                $(".accessLogs").html(data);
            }
        });
        $(document).on("click", '.paginateButton', function(event) {
            event.preventDefault();
            var page = this.id;
            var sort = $("select#sort").val();
            var regexpNumeric = /[^0-9]/g;
            var regexpAlphaNumeric = /[^a-z0-9]/g;
            var filter = $("input[name='filter']:checked").attr("id");
            $.ajax({
                url: "access-logs?page="+page.replace(regexpNumeric,'')+"&sort="+sort.replace(regexpNumeric,'')+"&filter="+filter.replace(regexpAlphaNumeric,''),
                type: "POST",
                contentType: false,
                cache: false,
                processData:false,
                headers : {
                    'csrftoken': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function()
                {
                    $('.page-loader-wrapper').fadeIn(100);
                },
                success: function(data)
                {
                    $(".accessLogs").html(data);
                    $('.page-loader-wrapper').fadeOut();
                    $("html, body").animate({ scrollTop: 0 }, "slow");
                }
            });
        });
        $(document).on("change", 'select#sort', function(event) {
            event.preventDefault();
            var sort = $("select#sort").val();
            var regexpNumeric = /[^0-9]/g;
            var regexpAlphaNumeric = /[^a-z0-9]/g;
            var filter = $("input[name='filter']:checked").attr("id");
            $.ajax({
                url: "access-logs?sort="+sort.replace(regexpNumeric,'')+"&filter="+filter.replace(regexpAlphaNumeric,''),
                type: "POST",
                contentType: false,
                cache: false,
                processData:false,
                headers : {
                    'csrftoken': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function()
                {
                    $('.page-loader-wrapper').fadeIn(100);
                },
                success: function(data)
                {
                    $(".accessLogs").html(data);
                    $('.page-loader-wrapper').fadeOut();
                    $("html, body").animate({ scrollTop: 0 }, "slow");
                }
            });
        });
        $(document).on("click", '.filterButton', function(event) {
            event.preventDefault();
            var sort = $("select#sort").val();
            var regexpNumeric = /[^0-9]/g;
            var regexpAlphaNumeric = /[^a-z0-9]/g;
            var filter = $("input[name='filter']:checked").attr("id");
            $.ajax({
                url: "access-logs?sort="+sort.replace(regexpNumeric,'')+"&filter="+filter.replace(regexpAlphaNumeric,''),
                type: "POST",
                contentType: false,
                cache: false,
                processData:false,
                headers : {
                    'csrftoken': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function()
                {
                    $('.page-loader-wrapper').fadeIn(100);
                },
                success: function(data)
                {
                    $(".accessLogs").html(data);
                    $('.page-loader-wrapper').fadeOut();
                    $("html, body").animate({ scrollTop: 0 }, "slow");
                }
            });
        });
    });
</script>
<?php } else if ($pageRequest && $pageRequest == 'slider-contents') { ?>
<link rel="stylesheet" type="text/css" href="plugins/sweetalert/css/sweetalert.css">
<script src="plugins/sweetalert/js/sweetalert.min.js"></script>
<script>
    getSliderContents();
    function getSliderContents() {
        $('.page-loader-wrapper').show();
        $.ajax({
            url: "slider-contents-a",
            type: "GET",
            contentType: false,
            cache: false,
            processData: false,
            headers : {
                'csrftoken': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (data) {
                $(".sliderContents").html(data);
                $('.page-loader-wrapper').fadeOut();
            }
        });
    }
    $(document).on('click','.deleteButton',function(e) {
        var sliderContentId = $(this).data('slider-content-id');
        e.preventDefault();
        swal({
            title: "Dikkat!",
            text: "Slider içeriğini silmek istediğinizden eminmisiniz?",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Evet, sil.",
            cancelButtonText: "İptal",
            closeOnConfirm: false,
            closeOnCancel: false,
            showLoaderOnConfirm: true,
        },
        function(isConfirm)
        {
            if (isConfirm)
            {
                setTimeout(function()
                {
                    $.ajax({
                        type:'POST',
                        url:'delete-slider-content-a',
                        data:'id=' + sliderContentId,
                        dataType: 'json',
                        headers : {
                            'csrftoken': $('meta[name="csrf-token"]').attr('content')
                        },
                        success:function(data)
                        {
                            if(data.success)
                            {
                                swal({
                                    title: "Başarılı!",
                                    text: "Slider içeriği başarıyla silindi.",
                                    type: "success",
                                    confirmButtonText: "Tamam",
                                    closeOnConfirm: true
                                });
                                getSliderContents();
                            }
                            else
                            {
                                swal({
                                    title: "Hata!",
                                    text: "Teknik bir problem yaşandı.",
                                    type: "error",
                                    confirmButtonText: "Tamam",
                                    closeOnConfirm: true
                                });
                            }
                        }
                    });
                }, 1000);
            }
            else
            {
                swal({
                    title: "İptal Edildi!",
                    text: "Slider içeriği silme işleminiz iptal edildi.",
                    type: "error",
                    confirmButtonText: "Tamam",
                    closeOnConfirm: true
                });
            }
        });
    });
</script>
<?php } else if ($pageRequest && $pageRequest == 'slider-settings') { ?>
<script>
    $(document).on('change','#auto_slide',function() {
       if ($(this).prop('checked')) {
           $('#autoSlideDuration').show();
       } else {
           $('#autoSlideDuration').hide();
       }
    });
    $('#sliderSettingsForm').on('submit',(function(e)
    {
        e.preventDefault();

        var result = $("#result");
        var submitButton = $('#saveSliderSettingsButton');

        result.empty();

        submitButton.prop('disabled', true);
        submitButton.html("Kaydediliyor...");

        $.ajax({
            url: "slider-settings-a",
            type: "POST",
            data:  new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData:false,
            headers : {
                'csrftoken': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data)
            {
                if(data.success)
                {
                    result.html("<div class='alert alert-success'>Slider ayarları başarıyla kaydedildi.</div>");
                } else {
                    result.html("<div class='alert alert-danger'>" + data.message + "</div>");
                }
                submitButton.prop('disabled', false);
                submitButton.html("Kaydet");
            }
        });
    }));
</script>
<?php } else if ($pageRequest && $pageRequest == 'add-slider-content') { ?>
<link href="plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css" rel="stylesheet" />
<script src="plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
<script type="text/javascript">
    $('.colorpicker').colorpicker({
        input: 'input.colorPickerInput'
    }).on('changeColor', function(e) {
        var colorInput = $(e.currentTarget).find('input[type="hidden"]');
        if (colorInput) {
            if (colorInput.attr('id') === 'title_color') {
                $('#previewTitle').css({'color': colorInput.val()});
            } else {
                $('#previewSubTitle').css({'color': colorInput.val()});
            }
        }
    });

    var lastTitleAnimation = '';
    var lastSubTitleAnimation = '';

    $('#title_animation').change(function() {
        $('#previewTitle').removeClass(lastTitleAnimation).addClass(this.value);
        lastTitleAnimation = this.value;
    });

    $('#sub_title_animation').change(function() {
        $('#previewSubTitle').removeClass(lastSubTitleAnimation).addClass(this.value);
        lastSubTitleAnimation = this.value;
    });

    var lastTextDirection = 'text-center';

    $('#text_direction').change(function() {
        var splittedValue = this.value.split('-');
        if (splittedValue[1] === 'top') {
            $('#previewTextContent').css({'top': '0px', 'bottom': ''});
        } else if (splittedValue[1] === 'bottom') {
            $('#previewTextContent').css({'top': '', 'bottom': '0px'});
        } else {
            $('#previewTextContent').css({'top': '', 'bottom': ''});
        }
        $('#previewTitle, #previewSubTitle').removeClass(lastTextDirection).addClass('text-' + splittedValue[0]);
        lastTextDirection = 'text-' + splittedValue[0];
    });

    var addSliderContentForm = $("#addSliderContentForm");
    addSliderContentForm.on('submit',(function(e)
    {
        e.preventDefault();

        var result = $("#result");
        var submitButton = $('#addSliderContentButton');

        var fileInput = $('#file');
        var fileNativeInput = document.getElementById('file');
        var youtubeUrlInput = $('#youtube_url');

        result.empty();

        if (!fileInput.val() && !youtubeUrlInput.val()) {
            result.html("<div class='alert alert-danger'>Slider içeriği için bir dosya seçin veya youtube urlsi girin.</div>");
            return;
        }

        if (fileInput.val() && youtubeUrlInput.val()) {
            result.html("<div class='alert alert-danger'>Slider içeriği için bir dosya seçilebilir veya youtube urlsi girilebilir. Hangisi kullanmak istiyorsanız diğerini boş bırakmalısınız.</div>");
            return;
        }

        if (youtubeUrlInput.val() && !isYoutubeUrl(youtubeUrlInput.val())) {
            result.html("<div class='alert alert-danger'>Lütfen geçerli bir youtube video urlsi giriniz.</div>");
            return;
        }

        if (fileNativeInput.files.length === 1 && !(fileNativeInput.files[0].type === 'image/png' || fileNativeInput.files[0].type === 'image/jpeg' || fileNativeInput.files[0].type === 'video/mp4')) {
            result.html("<div class='alert alert-danger'>Lütfen <strong>.png, .jpeg, .jpg, .mp4</strong> formatlarında bir dosya seçiniz.</div>");
            return;
        }

        submitButton.prop('disabled', true);
        submitButton.html("Ekleniyor...");

        $('#clearFileInput').hide();

        $.ajax({
            url: "add-slider-content-a",
            type: "POST",
            data:  new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData:false,
            headers : {
                'csrftoken': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data)
            {
                if(data.success)
                {
                    result.html("<div class='alert alert-success'>Slider içeriği başarıyla eklendi.</div>");
                    addSliderContentForm.trigger('reset');
                    $('span#previewMediaContent').html('<img class="bg-black">');
                    $('#previewTitle').text('');
                    $('#previewTitle').css({'font-family': '"Roboto", sans-serif', 'font-size': '40px', 'color': '#fff'});
                    $('#previewTitle').removeClass(lastTitleAnimation);
                    lastTitleAnimation = '';
                    $('#previewSubTitle').text('');
                    $('#previewSubTitle').css({'font-family': '"Roboto", sans-serif', 'font-size': '16px', 'color': '#fff'});
                    $('#previewSubTitle').removeClass(lastSubTitleAnimation);
                    lastSubTitleAnimation = '';
                    $('#previewTitle, #previewSubTitle').removeClass(lastTextDirection).addClass('text-center');
                    lastTextDirection = 'text-center';
                    $('#previewTextContent').css({'top': '', 'bottom': ''});
                } else {
                    result.html("<div class='alert alert-danger'>" + data.message + "</div>");
                    if ($('#file').val()) {
                        $('#clearFileInput').show();
                    }
                }
                submitButton.prop('disabled', false);
                submitButton.html("Ekle");
            }
        });
    }));
</script>
<?php } else if ($pageRequest && $pageRequest == 'edit-slider-content') { ?>
<link href="plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css" rel="stylesheet" />
<script src="plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
<script type="text/javascript">
    $('.colorpicker').colorpicker({
        input: 'input.colorPickerInput'
    }).on('changeColor', function(e) {
        var colorInput = $(e.currentTarget).find('input[type="hidden"]');
        if (colorInput) {
            if (colorInput.attr('id') === 'title_color') {
                $('#previewTitle').css({'color': colorInput.val()});
            } else {
                $('#previewSubTitle').css({'color': colorInput.val()});
            }
        }
    });

    var lastTitleAnimation = '<?=$fetchSliderContent['title_animation']?>';
    var lastSubTitleAnimation = '<?=$fetchSliderContent['sub_title_animation']?>';

    $('#title_animation').change(function() {
        $('#previewTitle').removeClass(lastTitleAnimation).addClass(this.value);
        lastTitleAnimation = this.value;
    });

    $('#sub_title_animation').change(function() {
        $('#previewSubTitle').removeClass(lastSubTitleAnimation).addClass(this.value);
        lastSubTitleAnimation = this.value;
    });

    var lastTextDirection = 'text-<?=explode('-', $fetchSliderContent['text_direction'])[0]?>';

    $('#text_direction').change(function() {
        var splittedValue = this.value.split('-');
        if (splittedValue[1] === 'top') {
            $('#previewTextContent').css({'top': '0px', 'bottom': ''});
        } else if (splittedValue[1] === 'bottom') {
            $('#previewTextContent').css({'top': '', 'bottom': '0px'});
        } else {
            $('#previewTextContent').css({'top': '', 'bottom': ''});
        }
        $('#previewTitle, #previewSubTitle').removeClass(lastTextDirection).addClass('text-' + splittedValue[0]);
        lastTextDirection = 'text-' + splittedValue[0];
    });

    var editSliderContentForm = $("#editSliderContentForm");
    editSliderContentForm.on('submit',(function(e)
    {
        e.preventDefault();

        var result = $("#result");
        var submitButton = $('#editSliderContentButton');

        var fileInput = $('#file');
        var fileNativeInput = document.getElementById('file');
        var youtubeUrlInput = $('#youtube_url');

        result.empty();

        if (fileInput.val() && youtubeUrlInput.val()) {
            result.html("<div class='alert alert-danger'>Slider içeriği için bir dosya seçilebilir veya youtube urlsi girilebilir. Hangisi kullanmak istiyorsanız diğerini boş bırakmalısınız.</div>");
            return;
        }

        if (youtubeUrlInput.val() && !isYoutubeUrl(youtubeUrlInput.val())) {
            result.html("<div class='alert alert-danger'>Lütfen geçerli bir youtube video urlsi giriniz.</div>");
            return;
        }

        if (fileNativeInput.files.length === 1 && !(fileNativeInput.files[0].type === 'image/png' || fileNativeInput.files[0].type === 'image/jpeg' || fileNativeInput.files[0].type === 'video/mp4')) {
            result.html("<div class='alert alert-danger'>Lütfen <strong>.png, .jpeg, .jpg, .mp4</strong> formatlarında bir dosya seçiniz.</div>");
            return;
        }

        submitButton.prop('disabled', true);
        submitButton.html("Düzenleniyor...");

        $('#clearFileInput').hide();

        $.ajax({
            url: "edit-slider-content-a",
            type: "POST",
            data:  new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData:false,
            headers : {
                'csrftoken': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data)
            {
                if(data.success)
                {
                    result.html("<div class='alert alert-success'>Slider içeriği başarıyla düzenlendi.</div>");
                    fileInput.val('');
                    youtubeUrlInput.val('');
                } else {
                    result.html("<div class='alert alert-danger'>" + data.message + "</div>");
                    if ($('#file').val()) {
                        $('#clearFileInput').show();
                    }
                }
                submitButton.prop('disabled', false);
                submitButton.html("Düzenle");
            }
        });
    }));
</script>
<?php } ?>