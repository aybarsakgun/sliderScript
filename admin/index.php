<?php
define('VAR1', true);

require_once 'class.user.php';

sessionStart($app);

if (empty($_SESSION[$app['name'].'Token'])) {
    $_SESSION[$app['name'].'Token'] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION[$app['name'].'Token'];

if(loginCheck($DB_con) == false)
{
    header("Location: login");
    exit();
}

$getUser = $DB_con->prepare("SELECT id, username, email, name, type FROM users WHERE id = :id");
$getUser->execute(array(":id" => loginCheck($DB_con)));
$user = $getUser->fetch(PDO::FETCH_ASSOC);

$pageRequest = filter_input(INPUT_GET, 'pr', FILTER_SANITIZE_STRING);

if (!in_array($pageRequest, $existPages)) {
    $pageRequest = null;
}

$googleFonts = '';

foreach ($fonts as $fontName => $fontFamily) {
    $googleFonts .= 'family=' . str_replace(' ', '+', $fontName) . '&';
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=Edge">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <meta name="csrf-token" content="<?=$csrfToken?>">
        <title><?=$app['name']?></title>
        <link rel="icon" href="img/favicon.png" type="image/x-icon">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?<?=$googleFonts?>display=swap" rel="stylesheet">
<!--        <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">-->
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">
        <link href="plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        <link href="plugins/node-waves/waves.min.css" rel="stylesheet" />
        <link href="plugins/animate-css/animate.min.css" rel="stylesheet" />
        <link href="css/style.css" rel="stylesheet">
        <link href="css/all-themes.min.css" rel="stylesheet" />
    </head>
    <?php if (!isset($pageRequest)) {?>
    <body class="four-zero-four">
        <div class="four-zero-four-container">
            <div class="error-code">404</div>
            <div class="error-message">Sayfa bulunamadı</div>
            <div class="button-place">
                <a href="home" class="btn btn-default btn-lg waves-effect">Anasayfaya dön</a>
            </div>
        </div>
    <?php
    } else {
    ?>
    <body class="theme-<?=$app['themeColor']?>">
        <div class="page-loader-wrapper">
            <div class="loader">
                <div class="preloader">
                    <div class="spinner-layer pl-<?=$app['themeColor']?>">
                        <div class="circle-clipper left">
                            <div class="circle"></div>
                        </div>
                        <div class="circle-clipper right">
                            <div class="circle"></div>
                        </div>
                    </div>
                </div>
                <p>Yükleniyor...</p>
            </div>
        </div>
        <div class="overlay"></div>
        <nav class="navbar">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a href="javascript:void(0);" class="bars"></a>
                    <a class="navbar-brand" href="home">Slider<strong>Script</strong></a>
                </div>
            </div>
        </nav>
        <section>
            <aside id="leftsidebar" class="sidebar">
                <div class="user-info">
                    <div class="info-container">
                        <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?=$user['name']?></div>
                        <div class="email"><?php if (!empty($user['email'])) { echo $user['email']; } else { echo $user['username']; }?></div>
                        <div class="btn-group user-helper-dropdown">
                            <i class="material-icons logoutButton">exit_to_app</i>
                        </div>
                    </div>
                </div>
                <div class="menu">
                    <ul class="list">
                        <li class="<?php if ($pageRequest == 'home') { ?>active<?php } ?>">
                            <a href="home">
                                <i class="material-icons">home</i>
                                <span>Anasayfa</span>
                            </a>
                        </li>
                        <li class="<?php if ($pageRequest == 'slider-settings') { ?>active<?php } ?>">
                            <a href="slider-settings">
                                <i class="material-icons">settings</i>
                                <span>Slider Ayarları</span>
                            </a>
                        </li>
                        <li class="<?php if ($pageRequest == 'slider-contents' || $pageRequest == 'edit-slider-content') { ?>active<?php } ?>">
                            <a href="slider-contents">
                                <i class="material-icons">photo_library</i>
                                <span>Slider İçerikleri</span>
                            </a>
                        </li>
                        <li class="<?php if ($pageRequest == 'add-slider-content') { ?>active<?php } ?>">
                            <a href="add-slider-content">
                                <i class="material-icons">add_a_photo</i>
                                <span>Slider İçeriği Ekle</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="legal">
                    <div class="copyright">
                        &copy; 2020 <?=$app['name']?>
                    </div>
                </div>
            </aside>
        </section>
        <?php if ($pageRequest == 'home') { ?>
        <section class="content">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="panel panel-default panel-post">
                            <div class="panel-heading">
                                <h4><strong>Yönetici Giriş Kayıtları</strong></h4>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <select class="form-control" id="sort" name="sort">
                                        <option value="0" >Tarihe göre (Önce en yeni giriş)</option>
                                        <option value="1" >Tarihe göre (Önce en eski giriş)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 accessLogs">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php } else if ($pageRequest == 'slider-contents') { ?>
        <section class="content">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="panel panel-default panel-post">
                            <div class="panel-heading">
                                <h4><strong>Slider İçerikleri</strong></h4>
                            </div>
                        </div>
                        <div class="row clearfix">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 sliderContents">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php } else if ($pageRequest == 'slider-settings') {
            $sliderSettings = $DB_con->prepare('SELECT * FROM slider_settings');
            $sliderSettings->execute();
            $fetchSliderSettings = $sliderSettings->fetchAll();
            $_sliderSettings = [];
            foreach ($fetchSliderSettings as $sliderSetting) {
                $_sliderSettings[$sliderSetting['field']] = $sliderSetting['value'];
            }
            ?>
        <section class="content">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="row clearfix">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="card">
                                <div class="header">
                                    <h2>
                                        Slider Ayarları
                                    </h2>
                                </div>
                                <div class="body">
                                    <form id="sliderSettingsForm">
                                        <div class="row clearfix">
                                            <div class="col-md-6">
                                                <h2 class="card-inside-title">Slider içerikleri arasında otomatik geçiş</h2>
                                                <div class="switch">
                                                    <label>Kapalı<input type="checkbox" id="auto_slide" name="auto_slide" value="A" <?php if (isset($_sliderSettings['auto_slide']) && $_sliderSettings['auto_slide'] == 'A') { ?>checked<?php } ?>><span class="lever switch-col-<?=$app['themeColor']?>"></span>Açık</label>
                                                </div>
                                                <div class="p-t-20" id="autoSlideDuration" <?php if (!isset($_sliderSettings['auto_slide']) || $_sliderSettings['auto_slide'] == 'P') { ?>style="display:none;"<?php } ?>>
                                                    <h2 class="card-inside-title">Bir sonraki içeriğe geçiş için belirtilen süre(saniye)</h2>
                                                    <div class="form-group">
                                                        <div class="form-line">
                                                            <input type="text" class="form-control" name="auto_slide_duration" value="<?=isset($_sliderSettings['auto_slide_duration']) ? $_sliderSettings['auto_slide_duration'] : ''?>" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*?)\..*/g, '$1');" placeholder="Saniye">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="result"></div>
                                        <button type="submit" class="btn bg-<?=$app['themeColor']?> m-t-15 waves-effect" id="saveSliderSettingsButton">Kaydet</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php } else if ($pageRequest == 'add-slider-content') { ?>
        <section class="content">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="row clearfix">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="card">
                                <div class="header">
                                    <h2>
                                        Slider İçeriği Ekle
                                    </h2>
                                </div>
                                <div class="body">
                                    <form id="addSliderContentForm">
                                        <div class="row clearfix">
                                            <div class="col-md-12 m-b-20">
                                                <div id="slider" class="carousel slide carousel-fade" data-ride="carousel" data-interval="false">
                                                    <div class="carousel-inner" role="listbox">
                                                        <div class="carousel-item active">
                                                            <div class="view">
                                                                <span id="previewMediaContent">
                                                                    <img class="bg-black">
                                                                </span>
                                                                <div class="full-bg-img flex-center mask col-white">
                                                                    <ul class="col-md-12 list-unstyled" style="position:absolute;" id="previewTextContent">
                                                                        <li>
                                                                            <h1 class="font-bold text-center animated" style="font-family: 'Roboto', sans-serif;font-size: 40px;color:#fff" id="previewTitle"></h1>
                                                                        </li>
                                                                        <li>
                                                                            <p class="font-bold py-4 text-center animated" style="font-family: 'Roboto', sans-serif;font-size: 16px;color:#fff" id="previewSubTitle"></p>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="alert alert-info">Aşağıdan slider içeriği olarak <strong>.png, .jpeg, .jpg, .mp4</strong> formatında dosyalar seçebilirsiniz. Veya youtube video url girerek, youtube video içeriği ekleyebilirsiniz.</div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                <label for="file">Slider İçeriği <a href="#" class="text-danger" id="clearFileInput" style="display:none;">[Seçilen Dosyayı Kaldır]</a></label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input type="file" id="file" name="file" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                <label for="youtube_url">Youtube URL</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <input type="text" id="youtube_url" name="youtube_url" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <h2 class="card-inside-title">
                                                    Başlık Seçenekleri
                                                </h2>
                                                <hr>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                <b>Başlık</b>
                                                <div class="input-group colorpicker" style="padding: 0!important;">
                                                    <div class="form-line">
                                                        <input type="text" id="title" name="title" class="form-control">
                                                        <input type="hidden" id="title_color" name="title_color" class="form-control colorPickerInput" value="#fff">
                                                    </div>
                                                    <span class="input-group-addon"><i style="border: 1px solid #000;"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                <label for="title_animation">Başlık Animasyonu</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <select class="form-control" id="title_animation" name="title_animation">
                                                            <option value="">Animasyonsuz</option>
                                                            <?php
                                                            foreach ($animations as $animation => $index) {
                                                                echo '<option value="'.$animation.'">'.$index.'</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                <label for="title_font">Başlık Fontu</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <select class="form-control" id="title_font" name="title_font">
                                                            <?php
                                                            foreach ($fonts as $font => $fontFamily) {
                                                                echo '<option data-font-family="'.$fontFamily.'" value="'.$font.'">'.$font.'</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                <label for="title_font_size">Başlık Font Büyüklüğü</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <select class="form-control" id="title_font_size" name="title_font_size">
                                                            <?php
                                                            foreach (range(8, 60) as $fontSize) {
                                                                echo '<option value="'.$fontSize.'" '.($fontSize == 40 ? 'selected' : '').'>'.$fontSize.'px</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <h2 class="card-inside-title">
                                                    Alt Başlık Seçenekleri
                                                </h2>
                                                <hr>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                <b>Alt Başlık</b>
                                                <div class="input-group colorpicker" style="padding: 0!important;">
                                                    <div class="form-line">
                                                        <input type="text" id="sub_title" name="sub_title" class="form-control">
                                                        <input type="hidden" id="sub_title_color" name="sub_title_color" class="form-control colorPickerInput" value="#fff">
                                                    </div>
                                                    <span class="input-group-addon"><i style="border: 1px solid #000;"></i></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                <label for="sub_title_animation">Alt Başlık Animasyonu</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <select class="form-control" id="sub_title_animation" name="sub_title_animation">
                                                            <option value="">Animasyonsuz</option>
                                                            <?php
                                                            foreach ($animations as $animation => $index) {
                                                                echo '<option value="'.$animation.'">'.$index.'</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                <label for="sub_title_font">Alt Başlık Fontu</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <select class="form-control" id="sub_title_font" name="sub_title_font">
                                                            <?php
                                                            foreach ($fonts as $font => $fontFamily) {
                                                                echo '<option data-font-family="'.$fontFamily.'" value="'.$font.'">'.$font.'</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                <label for="sub_title_font_size">Alt Başlık Font Büyüklüğü</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <select class="form-control" id="sub_title_font_size" name="sub_title_font_size">
                                                            <?php
                                                            foreach (range(8, 60) as $fontSize) {
                                                                echo '<option value="'.$fontSize.'" '.($fontSize == 16 ? 'selected' : '').'>'.$fontSize.'px</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <hr>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                <label for="text_direction">Yazı Yönü</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <select class="form-control" id="text_direction" name="text_direction">
                                                            <option value="center-center">Merkez Orta</option>
                                                            <option value="right-center">Merkez Sağ</option>
                                                            <option value="left-center">Merkez Sol</option>
                                                            <option value="center-top">Üst Orta</option>
                                                            <option value="right-top">Üst Sağ</option>
                                                            <option value="left-top">Üst Sol</option>
                                                            <option value="center-bottom">Alt Orta</option>
                                                            <option value="right-bottom">Alt Sağ</option>
                                                            <option value="left-bottom">Alt Sol</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                <label for="status">Durum</label>
                                                <div class="form-group">
                                                    <div class="form-line">
                                                        <select class="form-control" id="status" name="status">
                                                            <option value="A">Aktif</option>
                                                            <option value="P">Pasif</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="result"></div>
                                        <button type="submit" class="btn bg-<?=$app['themeColor']?> m-t-15 waves-effect" id="addSliderContentButton">Ekle</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php
        } else if ($pageRequest == 'edit-slider-content') {
            $sliderContentId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            if ($sliderContentId === false) {
                echo 400;
                exit();
            }
            $sliderContent = $DB_con->prepare("SELECT * FROM slider_contents WHERE id = :id");
            $sliderContent->execute(array(':id' => $sliderContentId));
            $fetchSliderContent = $sliderContent->fetch(PDO::FETCH_ASSOC);
            ?>
            <section class="content">
                <div class="container-fluid">
                    <div class="row clearfix">
                        <div class="row clearfix">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="card">
                                    <div class="header">
                                        <h2>
                                            Slider İçeriği Düzenle
                                        </h2>
                                    </div>
                                    <div class="body">
                                        <form id="editSliderContentForm">
                                            <input type="hidden" name="id" value="<?=$fetchSliderContent['id']?>">
                                            <div class="row clearfix">
                                                <div class="col-md-12 m-b-20">
                                                    <div id="slider" class="carousel slide carousel-fade" data-ride="carousel" data-interval="false">
                                                        <div class="carousel-inner" role="listbox">
                                                            <div class="carousel-item active">
                                                                <div class="view">
                                                                    <span id="previewMediaContent">
                                                                        <?php
                                                                        $preview = '<img class="bg-black">';
                                                                        if (file_exists($fetchSliderContent['file_path'])) {
                                                                            $checkFileMimeType = mime_content_type($fetchSliderContent['file_path']);
                                                                            if ($checkFileMimeType) {
                                                                                $fileType = explode('/', $checkFileMimeType)[0];
                                                                                if ($fileType == 'image') {
                                                                                    $preview = '<img src="'.$fetchSliderContent['file_path'].'">';
                                                                                } else if ($fileType == 'video') {
                                                                                    $preview = '<video autoplay loop playsinline muted><source src="'.$fetchSliderContent['file_path'].'" type="video/mp4"/></video>';
                                                                                }
                                                                            }
                                                                        }
                                                                        echo $preview;
                                                                        $textDirectionStyle = '';
                                                                        $textDirection = explode('-', $fetchSliderContent['text_direction']);
                                                                        if ($textDirection[1] == 'top') {
                                                                            $textDirectionStyle = 'top:0px';
                                                                        } else if ($textDirection[1] == 'bottom') {
                                                                            $textDirectionStyle = 'bottom:0px';
                                                                        }
                                                                        ?>
                                                                    </span>
                                                                    <div class="full-bg-img flex-center mask col-white">
                                                                        <ul class="col-md-12 list-unstyled" style="position:absolute;<?=$textDirectionStyle?>" id="previewTextContent">
                                                                            <li>
                                                                                <h1 class="font-bold text-<?=explode('-', $fetchSliderContent['text_direction'])[0]?> animated <?=$fetchSliderContent['title_animation']?>" style="font-family: '<?=$fetchSliderContent['title_font']?>', <?=$fonts[$fetchSliderContent['title_font']]?>;font-size: <?=$fetchSliderContent['title_font_size']?>px;color:<?=$fetchSliderContent['title_color']?>" id="previewTitle"><?=$fetchSliderContent['title']?></h1>
                                                                            </li>
                                                                            <li>
                                                                                <p class="font-bold py-4 text-<?=explode('-', $fetchSliderContent['text_direction'])[0]?> animated <?=$fetchSliderContent['sub_title_animation']?>" style="font-family: '<?=$fetchSliderContent['sub_title_font']?>', <?=$fonts[$fetchSliderContent['sub_title_font']]?>;font-size: <?=$fetchSliderContent['sub_title_font_size']?>px;color:<?=$fetchSliderContent['sub_title_color']?>" id="previewSubTitle"><?=$fetchSliderContent['sub_title']?></p>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <label for="file">Slider İçeriği <a href="#" class="text-danger" id="clearFileInput" style="display:none;">[Seçilen Dosyayı Kaldır]</a></label>
                                                    <div class="form-group">
                                                        <div class="form-line">
                                                            <input type="file" id="file" name="file" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <label for="youtube_url">Youtube URL</label>
                                                    <div class="form-group">
                                                        <div class="form-line">
                                                            <input type="text" id="youtube_url" name="youtube_url" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <h2 class="card-inside-title">
                                                        Başlık Seçenekleri
                                                    </h2>
                                                    <hr>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <b>Başlık</b>
                                                    <div class="input-group colorpicker" style="padding: 0!important;">
                                                        <div class="form-line">
                                                            <input type="text" id="title" name="title" class="form-control" value="<?=$fetchSliderContent['title']?>">
                                                            <input type="hidden" id="title_color" name="title_color" class="form-control colorPickerInput" value="<?=$fetchSliderContent['title_color']?>">
                                                        </div>
                                                        <span class="input-group-addon"><i style="border: 1px solid #000;"></i></span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <label for="title_animation">Başlık Animasyonu</label>
                                                    <div class="form-group">
                                                        <div class="form-line">
                                                            <select class="form-control" id="title_animation" name="title_animation">
                                                                <option value="">Animasyonsuz</option>
                                                                <?php
                                                                foreach ($animations as $animation => $index) {
                                                                    echo '<option value="'.$animation.'" '.($animation == $fetchSliderContent['title_animation'] ? 'selected' : '').'>'.$index.'</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <label for="title_font">Başlık Fontu</label>
                                                    <div class="form-group">
                                                        <div class="form-line">
                                                            <select class="form-control" id="title_font" name="title_font">
                                                                <?php
                                                                foreach ($fonts as $font => $fontFamily) {
                                                                    echo '<option data-font-family="'.$fontFamily.'" value="'.$font.'" '.($font == $fetchSliderContent['title_font'] ? 'selected' : '').'>'.$font.'</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <label for="title_font_size">Başlık Font Büyüklüğü</label>
                                                    <div class="form-group">
                                                        <div class="form-line">
                                                            <select class="form-control" id="title_font_size" name="title_font_size">
                                                                <?php
                                                                foreach (range(8, 60) as $fontSize) {
                                                                    echo '<option value="'.$fontSize.'" '.($fontSize == $fetchSliderContent['title_font_size'] ? 'selected' : '').'>'.$fontSize.'px</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <h2 class="card-inside-title">
                                                        Alt Başlık Seçenekleri
                                                    </h2>
                                                    <hr>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <b>Alt Başlık</b>
                                                    <div class="input-group colorpicker" style="padding: 0!important;">
                                                        <div class="form-line">
                                                            <input type="text" id="sub_title" name="sub_title" class="form-control" value="<?=$fetchSliderContent['sub_title']?>">
                                                            <input type="hidden" id="sub_title_color" name="sub_title_color" class="form-control colorPickerInput" value="<?=$fetchSliderContent['sub_title_color']?>">
                                                        </div>
                                                        <span class="input-group-addon"><i style="border: 1px solid #000;"></i></span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <label for="sub_title_animation">Alt Başlık Animasyonu</label>
                                                    <div class="form-group">
                                                        <div class="form-line">
                                                            <select class="form-control" id="sub_title_animation" name="sub_title_animation">
                                                                <option value="">Animasyonsuz</option>
                                                                <?php
                                                                foreach ($animations as $animation => $index) {
                                                                    echo '<option value="'.$animation.'" '.($animation == $fetchSliderContent['sub_title_animation'] ? 'selected' : '').'>'.$index.'</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <label for="sub_title_font">Alt Başlık Fontu</label>
                                                    <div class="form-group">
                                                        <div class="form-line">
                                                            <select class="form-control" id="sub_title_font" name="sub_title_font">
                                                                <?php
                                                                foreach ($fonts as $font => $fontFamily) {
                                                                    echo '<option data-font-family="'.$fontFamily.'" value="'.$font.'" '.($font == $fetchSliderContent['sub_title_font'] ? 'selected' : '').'>'.$font.'</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <label for="sub_title_font_size">Alt Başlık Font Büyüklüğü</label>
                                                    <div class="form-group">
                                                        <div class="form-line">
                                                            <select class="form-control" id="sub_title_font_size" name="sub_title_font_size">
                                                                <?php
                                                                foreach (range(8, 60) as $fontSize) {
                                                                    echo '<option value="'.$fontSize.'" '.($fontSize == $fetchSliderContent['sub_title_font_size'] ? 'selected' : '').'>'.$fontSize.'px</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <hr>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <label for="text_direction">Yazı Yönü</label>
                                                    <div class="form-group">
                                                        <div class="form-line">
                                                            <select class="form-control" id="text_direction" name="text_direction">
                                                                <option value="center-center" <?php if ($fetchSliderContent['text_direction'] == 'center-center') { echo 'selected'; }?>>Merkez Orta</option>
                                                                <option value="right-center" <?php if ($fetchSliderContent['text_direction'] == 'right-center') { echo 'selected'; }?>>Merkez Sağ</option>
                                                                <option value="left-center" <?php if ($fetchSliderContent['text_direction'] == 'left-center') { echo 'selected'; }?>>Merkez Sol</option>
                                                                <option value="center-top" <?php if ($fetchSliderContent['text_direction'] == 'center-top') { echo 'selected'; }?>>Üst Orta</option>
                                                                <option value="right-top" <?php if ($fetchSliderContent['text_direction'] == 'right-top') { echo 'selected'; }?>>Üst Sağ</option>
                                                                <option value="left-top" <?php if ($fetchSliderContent['text_direction'] == 'left-top') { echo 'selected'; }?>>Üst Sol</option>
                                                                <option value="center-bottom" <?php if ($fetchSliderContent['text_direction'] == 'center-bottom') { echo 'selected'; }?>>Alt Orta</option>
                                                                <option value="right-bottom" <?php if ($fetchSliderContent['text_direction'] == 'right-bottom') { echo 'selected'; }?>>Alt Sağ</option>
                                                                <option value="left-bottom" <?php if ($fetchSliderContent['text_direction'] == 'left-bottom') { echo 'selected'; }?>>Alt Sol</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <label for="status">Durum</label>
                                                    <div class="form-group">
                                                        <div class="form-line">
                                                            <select class="form-control" id="status" name="status">
                                                                <option value="A" <?php if ($fetchSliderContent['status'] == 'A') { echo 'selected'; }?>>Aktif</option>
                                                                <option value="P" <?php if ($fetchSliderContent['status'] == 'P') { echo 'selected'; }?>>Pasif</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="result"></div>
                                            <button type="submit" class="btn bg-<?=$app['themeColor']?> m-t-15 waves-effect" id="editSliderContentButton">Düzenle</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php } ?>
    <?php } ?>
        <script src="plugins/jquery/jquery.min.js"></script>
        <script src="plugins/bootstrap/js/bootstrap.min.js"></script>
        <script src="plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
        <script src="plugins/node-waves/waves.min.js"></script>
        <script src="plugins/jquery-inputmask/jquery.inputmask.bundle.min.js"></script>
        <script src="js/main.js"></script>
        <?php include_once 'pageJS.php'; ?>
    </body>
</html>
