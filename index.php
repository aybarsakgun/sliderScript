<?php
define('VAR1', true);

require_once 'admin/class.user.php';

$getSliderContents = $DB_con->prepare('SELECT * FROM slider_contents WHERE status = :status');
$getSliderContents->execute(array(':status' => 'A'));
$fetchSliderContents = $getSliderContents->fetchAll();

$sliderSettings = $DB_con->prepare('SELECT * FROM slider_settings');
$sliderSettings->execute();
$fetchSliderSettings = $sliderSettings->fetchAll();
$_sliderSettings = [];
foreach ($fetchSliderSettings as $sliderSetting) {
    $_sliderSettings[$sliderSetting['field']] = $sliderSetting['value'];
}

$fileDirectoryPrefix = 'admin/';

$googleFonts = '';

foreach ($fonts as $fontName => $fontFamily) {
    $googleFonts .= 'family=' . str_replace(' ', '+', $fontName) . '&';
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<title><?=$app['name']?></title>
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?<?=$googleFonts?>display=swap" rel="stylesheet">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
		<style type="text/css">
			.carousel,
			.carousel-item,
			.carousel-item.active {
			    height: 400px;
			}
			.carousel-inner {
			    height: 100%;
			}
			.carousel .carousel-item video, .carousel .carousel-item img {
                min-width: 100%;
                min-height: 100%;
                width: auto;
                height: auto;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
				position: relative;
				display: block;
			}
			.view {
				height: 100%;
				position: relative;
				overflow: hidden;
				cursor: default;
			}
			.view .mask {
				position: absolute;
				top: 0;
				right: 0;
				bottom: 0;
				left: 0;
				width: 100%;
				height: 100%;
				overflow: hidden;
				background-attachment: fixed;
			}
			.flex-center {
				display: -webkit-box;
				display: -ms-flexbox;
				display: flex;
				-webkit-box-align: center;
				-ms-flex-align: center;
				align-items: center;
				-webkit-box-pack: center;
				-ms-flex-pack: center;
				justify-content: center;
				height: 100%;
			}
    	</style>
	</head>
	<body>
		<div class="container">
			<div id="slider" class="carousel slide carousel-fade" data-ride="carousel" data-interval="<?php if (!isset($_sliderSettings['auto_slide']) || $_sliderSettings['auto_slide'] == 'P') {?>false<?php } else if (isset($_sliderSettings['auto_slide_duration']) && (int)$_sliderSettings['auto_slide_duration'] > 0) { echo (int)$_sliderSettings['auto_slide_duration'] . '000'; } else { echo '5000'; } ?>">
				<ol class="carousel-indicators">
                    <?php
                    foreach ($fetchSliderContents as $index => $sliderContent) {
                        echo '<li data-target="#slider" data-slide-to="'.$index.'" '.($index == 0 ? 'class="active"' : '').'></li>';
                    }
                    ?>
				</ol>
				<div class="carousel-inner" role="listbox">
                    <?php
                    foreach ($fetchSliderContents as $index => $sliderContent) {
                        ?>
                        <div class="carousel-item <?=$index == 0 ? 'active' : ''?>" <?php if (isset($_sliderSettings['auto_slide']) && $_sliderSettings['auto_slide'] == 'A') {?>data-interval="<?=isset($_sliderSettings['auto_slide_duration']) && (int)$_sliderSettings['auto_slide_duration'] > 0 ? (int)$_sliderSettings['auto_slide_duration'] . '000' : '5000'?>"<?php } ?>>
                            <div class="view">
                                <?php
                                $preview = '<img class="bg-dark">';
                                if (file_exists($fileDirectoryPrefix.$sliderContent['file_path'])) {
                                    $checkFileMimeType = mime_content_type($fileDirectoryPrefix.$sliderContent['file_path']);
                                    if ($checkFileMimeType) {
                                        $fileType = explode('/', $checkFileMimeType)[0];
                                        if ($fileType == 'image') {
                                            $preview = '<img src="'.$fileDirectoryPrefix.$sliderContent['file_path'].'">';
                                        } else if ($fileType == 'video') {
                                            $preview = '<video autoplay loop playsinline muted><source src="'.$fileDirectoryPrefix.$sliderContent['file_path'].'" type="video/mp4"/></video>';
                                        }
                                    }
                                }
                                echo $preview;
                                $textDirectionStyle = '';
                                $textDirection = explode('-', $sliderContent['text_direction']);
                                if ($textDirection[1] == 'top') {
                                    $textDirectionStyle = 'top:0px';
                                } else if ($textDirection[1] == 'bottom') {
                                    $textDirectionStyle = 'bottom:0px';
                                }
                                ?>
                                <div class="full-bg-img flex-center mask rgba-indigo-light text-white text-<?=explode('-', $sliderContent['text_direction'])[0]?>">
                                    <ul class="col-md-12 list-unstyled list-inline position-absolute" style="<?=$textDirectionStyle?>">
                                        <li>
                                            <h1 class="font-weight-bold animate__animated animate__<?=$sliderContent['title_animation']?>" style="font-family: '<?=$sliderContent['title_font']?>', <?=$fonts[$sliderContent['title_font']]?>;font-size: <?=$sliderContent['title_font_size']?>px;color:<?=$sliderContent['title_color']?>"><?=$sliderContent['title']?></h1>
                                        </li>
                                        <li>
                                            <p class="font-weight-bold py-4 animate__animated animate__<?=$sliderContent['sub_title_animation']?>" style="font-family: '<?=$sliderContent['sub_title_font']?>', <?=$fonts[$sliderContent['sub_title_font']]?>;font-size: <?=$sliderContent['sub_title_font_size']?>px;color:<?=$sliderContent['sub_title_color']?>"><?=$sliderContent['sub_title']?></p>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
				</div>
			</div>
		</div>
		<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
	</body>
</html>