<?php
define('AJAX', TRUE);

require_once 'class.user.php';

sessionStart($app);

if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
	exit("Security");
}
	
if (empty($_SESSION[$app['name'].'Token'])) {
    $_SESSION[$app['name'].'Token'] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION[$app['name'].'Token'];

$headers = apache_request_headers();

if (isset($headers['csrftoken']))
{
    if (!hash_equals($csrfToken, $headers['csrftoken'])) {
        exit("Security");
    }
} else {
    exit("Security");
}

if(loginCheck($DB_con) == false)
{
	header('Location: login');
	exit();
}

$pageRequest = filter_input(INPUT_GET, 'pr', FILTER_SANITIZE_STRING);

if(isset($pageRequest))
{
    if($pageRequest == "logout") {
        echo $_user->logout();
    } else if ($pageRequest == 'access-logs') {
        $checkUser = $DB_con->prepare("SELECT id FROM users WHERE id = :userId");
        $checkUser->execute(array(":userId"=>1));
        if($checkUser->rowCount() == 1)
        {
            $loginAttempts = $DB_con->prepare("SELECT COUNT(userId) AS counter FROM login_attempts WHERE userId = :userId");
            $loginAttempts->execute(array(":userId"=>1));
            $fetchLoginAttempts = $loginAttempts->fetch(PDO::FETCH_ASSOC);
            if($loginAttempts->rowCount() > 0)
            {
                $filterString = "";
                $filterPrefix = "";

                $sortStatus = isset($_GET['sort']) ? (int) $_GET['sort'] : 0;
                
                if($sortStatus == 0)
                {
                    $sortString = "ORDER BY date DESC";
                }
                else if($sortStatus == 1)
                {
                    $sortString = "ORDER BY date ASC";
                }
                else
                {
                    $sortString = "";
                }

                if(isset($_GET["filter"]))
                {
                    $filter = preg_replace('/[^a-z0-9]/', '', filter_input(INPUT_GET, 'filter', FILTER_SANITIZE_STRING));
                    if($filter != "")
                    {
                        if($filter == "filter2")
                        {
                            $filterString .= $filterPrefix . "status = 1 ";
                            $filterPrefix = 'AND ';
                        }
                        else if($filter == "filter3")
                        {
                            $filterString .= $filterPrefix . "status = 0 ";
                            $filterPrefix = 'AND ';
                        }
                    }
                }

                if($filterString != "")
                {
                    $queryString = "SELECT COUNT(userId) AS counter FROM login_attempts WHERE $filterString AND userId = :userId";
                }
                else if($filterString == "")
                {
                    $queryString = "SELECT COUNT(userId) AS counter FROM login_attempts WHERE userId = :userId";
                }

                $query = $DB_con->prepare($queryString);
                $query->execute(array(":userId"=>1));
                $fetchQuery = $query->fetch(PDO::FETCH_ASSOC);

                $perPage = 20;
                $totalRow = $fetchQuery["counter"];
                $totalPage = ceil($totalRow / $perPage);
                $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                if($page < 1) $page = 1;
                if($page > $totalPage) $page = $totalPage;
                $limit = ($page - 1) * $perPage;

                if($filterString != "")
                {
                    $queryString = "SELECT * FROM login_attempts WHERE $filterString AND userId = :userId $sortString LIMIT :limit , :perPage";
                }
                else if($filterString == "")
                {
                    $queryString = "SELECT * FROM login_attempts WHERE userId = :userId $sortString LIMIT :limit , :perPage";
                }

                $query = $DB_con->prepare($queryString);
                $query->execute(array(":userId"=>1,":limit"=>$limit,":perPage"=>$perPage));

                ?>
                <strong>Filtrele: </strong>
                <input type="radio" class="with-gap radio-col-light-blue filterButton" name="filter" id="filter1" <?php if( ( !isset($_GET["filter"]) ) || ( isset($_GET["filter"]) && $filter == "filter1" ) ) { ?>checked<?php } ?>>
                <label for="filter1">Tümü</label>
                <input type="radio" class="with-gap radio-col-light-blue filterButton" name="filter" id="filter2" <?php if(isset($_GET["filter"]) && $filter == "filter2") { ?>checked<?php } ?>>
                <label for="filter2">Başarılı</label>
                <input type="radio" class="with-gap radio-col-light-blue filterButton" name="filter" id="filter3" <?php if(isset($_GET["filter"]) && $filter == "filter3") { ?>checked<?php } ?>>
                <label for="filter3">Başarısız</label>
                <br>
                <?php
                if($fetchQuery["counter"] > 0)
                {
                    if($filterString == "")
                    {
                        ?>
                        <small>Toplam <?=$fetchQuery["counter"]?> tane bulunan sonuçtan <?=$query->rowCount()?> tanesini görüntülüyorsunuz.</small>
                        <?php
                    }
                    else
                    {
                        ?>
                        <small>Toplam <?=$fetchLoginAttempts["counter"]?> tane bulunan sonuçtan, filtrelemenize uygun <?=$fetchQuery["counter"]?> tanesinin <?=$query->rowCount()?> tanesini görüntülüyorsunuz.</small>
                        <?php
                    }
                }
                ?>
                <table class="table table-condensed table-striped">
                    <thead>
                    <tr>
                        <th>IP Adresi</th>
                        <th>Tarayıcı</th>
                        <th class="visible-sm visible-md visible-lg">Tarih</th>
                        <th class="visible-sm visible-md visible-lg">Durum</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if($fetchQuery["counter"] > 0)
                    {
                        while($fetch = $query->fetch(PDO::FETCH_ASSOC))
                        {
                            ?>
                            <tr>
                                <td><?=$fetch["ip_address"]?></td>
                                <td>
                                    <span><?=$fetch["browser"]?></span>
                                    <div class="visible-xs">
                                        <strong>Tarih:</strong> <?=date('d.m.Y h:i:s', strtotime($fetch["date"]))?><br>
                                        <strong>Durum:</strong><br>
                                        <?php if($fetch["status"] == "0") { ?><span class="label label-danger">Başarısız</span><?php } else if($fetch["status"] == "1") { ?><span class="label label-success">Başarılı</span><?php } ?>
                                    </div>
                                </td>
                                <td class="visible-sm visible-md visible-lg"><?=date('d.m.Y h:i:s', strtotime($fetch["date"]))?></td>
                                <td class="visible-sm visible-md visible-lg"><?php if($fetch["status"] == "0") { ?><span class="label label-danger">Başarısız</span><?php } else if($fetch["status"] == "1") { ?><span class="label label-success">Başarılı</span><?php } ?></td>
                            </tr>
                            <?php
                        }
                    }
                    else
                    {
                        ?>
                        <tr>
                            <td colspan="5">Filtrelemenize uygun sonuç bulunamadı.</td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>

                <?php
                if($fetchQuery["counter"] > 0)
                {
                    ?>
                    <ul class="pagination">
                        <?php
                        $showPage = 5;

                        $lowestCenter = ceil($showPage/2);
                        $highestCenter = ($totalPage+1) - $lowestCenter;

                        $pageCenter = $page;
                        if($pageCenter < $lowestCenter) $pageCenter = $lowestCenter;
                        if($pageCenter > $highestCenter) $pageCenter = $highestCenter;

                        $leftPages = round($pageCenter - (($showPage-1) / 2));
                        $rightPages = round((($showPage-1) / 2) + $pageCenter);

                        if($leftPages < 1) $leftPages = 1;
                        if($rightPages > $totalPage) $rightPages = $totalPage;

                        if($page != 1) echo '<li><a class="waves-effect paginateButton" href="javascript:void(0);" id="1"><i class="material-icons">first_page</i></a></li>';
                        else if($page == 1) echo '<li class="disabled"><a href="javascript:void(0);"><i class="material-icons">first_page</i></a></li>';
                        if($page != 1) echo '<li><a class="waves-effect paginateButton" href="javascript:void(0);" id="'.($page-1).'"><i class="material-icons">chevron_left</i></a></li>';
                        else if($page == 1) echo '<li class="disabled"><a href="javascript:void(0);"><i class="material-icons">chevron_left</i></a></li>';

                        for($s = $leftPages; $s <= $rightPages; $s++) {
                            if($page == $s) {
                                echo '<li class="active"><a href="javascript:void(0);">'.$s.'</a></li>';
                            } else {
                                echo '<li><a class="waves-effect paginateButton" href="javascript:void(0);" id="'.$s.'">'.$s.'</a></li>';
                            }
                        }

                        if($page != $totalPage) echo '<li><a class="waves-effect paginateButton" href="javascript:void(0);" id="'.($page+1).'"><i class="material-icons">chevron_right</i></a></li>';
                        else if($page == $totalPage) echo '<li class="disabled"><a href="javascript:void(0);"><i class="material-icons">chevron_right</i></a></li>';
                        if($page != $totalPage) echo '<li><a class="waves-effect paginateButton" href="javascript:void(0);" id="'.$totalPage.'"><i class="material-icons">last_page</i></a></li>';
                        else if($page == $totalPage)  echo '<li class="disabled"><a href="javascript:void(0);"><i class="material-icons">last_page</i></a></li>';
                        ?>
                    </ul>
                    <?php
                }
            }
            else
            {
                ?>
                <div class='notice notice-danger'><strong>Bilgi: </strong>Henüz yöneticinin sisteme giriş kayıtı bulunamadı.</div>
                <?php
            }
        }
        else
        {
            ?>
            <div class='notice notice-danger'><strong>Hata: </strong>Teknik bir hata yaşandı. Tekrar deneyin.</div>
            <?php
        }
    } else if ($pageRequest == 'slider-contents') {
        $sliderContents = $DB_con->prepare('SELECT id, youtube_id, title, file_path, status FROM slider_contents ORDER BY sort ASC');
        $sliderContents->execute();
        if ($sliderContents->rowCount() == 0) {
            exit('<div class="alert alert-info">Henüz sisteme kayıtlı slider içeriği bulunmamakta.</div>');
        }
        ?>
         <div class="table-responsive">
            <table class="table table-condensed table-striped">
                <thead>
                <tr>
                    <th>Görsel</th>
                    <th>Başlık</th>
                    <th class="visible-sm visible-md visible-lg">Durum</th>
                    <th class="visible-sm visible-md visible-lg">İşlem</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($fetchSliderContent = $sliderContents->fetch(PDO::FETCH_ASSOC)) { ?>
                    <tr>
                        <td>
                            <?php
                            if (empty($fetchSliderContent['youtube_id'])) {
                                $preview = '<img src="img/404.png" width="120" class="img-responsive">';
                                if (file_exists($fetchSliderContent['file_path'])) {
                                    $checkFileMimeType = mime_content_type($fetchSliderContent['file_path']);
                                    if ($checkFileMimeType) {
                                        $fileType = explode('/', $checkFileMimeType)[0];
                                        if ($fileType == 'image') {
                                            $preview = '<img src="'.$fetchSliderContent['file_path'].'" width="120" class="img-responsive">';
                                        } else if ($fileType == 'video') {
                                            $preview = '<video autoplay loop playsinline muted width="120"><source src="'.$fetchSliderContent['file_path'].'" type="video/mp4" /></video>';
                                        }
                                    }
                                }
                                echo $preview;
                            } else {
                                echo '<img src="https://i.ytimg.com/vi/'.$fetchSliderContent['youtube_id'].'/default.jpg" width="120" class="img-responsive">';
                            }
                            ?>
                        </td>
                        <td>
                            <?=$fetchSliderContent['title']?>
                            <div class="visible-xs">
                                <strong>Durum: </strong> <span class="label <?=$fetchSliderContent['status'] == 'A' ? 'label-success' : 'label-danger'?>"><?=$fetchSliderContent['status'] == 'A' ? 'Aktif' : 'Pasif'?></span><br>
                                <strong>İşlemler: </strong> <a href="#" class="label label-danger deleteButton" data-slider-content-id="<?=$fetchSliderContent['id']?>">Sil</a><a href="edit-slider-content-<?=$fetchSliderContent['id']?>" class="label label-info">Düzenle</a><br>
                            </div>
                        </td>
                        <td class="visible-sm visible-md visible-lg">
                            <span class="label <?=$fetchSliderContent['status'] == 'A' ? 'label-success' : 'label-danger'?>"><?=$fetchSliderContent['status'] == 'A' ? 'Aktif' : 'Pasif'?></span>
                        </td>
                        <td class="visible-sm visible-md visible-lg">
                            <a href="#" class="label label-danger deleteButton" data-slider-content-id="<?=$fetchSliderContent['id']?>">Sil</a>
                            <a href="edit-slider-content-<?=$fetchSliderContent['id']?>" class="label label-info">Düzenle</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
         </div>
        <?php
    } else if ($pageRequest == 'slider-settings') {
        $settings = [
            'auto_slide',
            'auto_slide_duration'
        ];
        try {
            foreach ($settings as $setting) {
                $value = '';
                if (isset($_POST[$setting])) {
                    $value = filter_input(INPUT_POST, $setting, FILTER_SANITIZE_STRING);
                } else {
                    $value = 'P';
                }
                $saveSliderSettings = $DB_con->prepare('INSERT INTO slider_settings (field, value) VALUES (:field, :value) ON DUPLICATE KEY UPDATE value = :value2');
                $saveSliderSettings->execute(array(':field' => $setting, ':value' => $value, ':value2' => $value));
            }
            exit(result(200));
        }
        catch(PDOException $ex)
        {
            exit(result(500, 'Teknik bir problem yaşandı lütfen daha sonra tekrar deneyin.'));
        }
    } else if ($pageRequest == 'add-slider-content') {
        $file = $_FILES['file'];
        $youtubeUrl = filter_input(INPUT_POST, 'youtube_url', FILTER_SANITIZE_URL);
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
        $titleAnimation = filter_input(INPUT_POST, 'title_animation', FILTER_SANITIZE_STRING);
        $titleColor = filter_input(INPUT_POST, 'title_color', FILTER_SANITIZE_STRING);
        $titleFont = filter_input(INPUT_POST, 'title_font', FILTER_SANITIZE_STRING);
        $titleFontSize = filter_input(INPUT_POST, 'title_font_size', FILTER_SANITIZE_STRING);
        $subTitle = filter_input(INPUT_POST, 'sub_title', FILTER_SANITIZE_STRING);
        $subTitleAnimation = filter_input(INPUT_POST, 'sub_title_animation', FILTER_SANITIZE_STRING);
        $subTitleColor = filter_input(INPUT_POST, 'sub_title_color', FILTER_SANITIZE_STRING);
        $subTitleFont = filter_input(INPUT_POST, 'sub_title_font', FILTER_SANITIZE_STRING);
        $subTitleFontSize = filter_input(INPUT_POST, 'sub_title_font_size', FILTER_SANITIZE_STRING);
        $textDirection = filter_input(INPUT_POST, 'text_direction', FILTER_SANITIZE_STRING);
        $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

        if ((!isset($youtubeUrl) || empty($youtubeUrl)) && (!isset($file) || empty($file['name']))) {
            exit(result(400, 'Slider içeriği için bir dosya seçin veya youtube urlsi girin.'));
        }

        if (!empty($file['name']) && !empty($youtubeUrl)) {
            exit(result(400, 'Slider içeriği için bir dosya seçilebilir veya youtube urlsi girilebilir. Hangisi kullanmak istiyorsanız diğerini boş bırakmalısınız.'));
        }

        $youtubeUrlPattern = '~^(?:https?://)?(?:www[.])?(?:youtube[.]com/watch[?]v=|youtu[.]be/)([^&]{11})~x';

        $isYoutubeUrl = preg_match($youtubeUrlPattern, $youtubeUrl, $youtubeVideoId);

        if (!empty($youtubeUrl) && $isYoutubeUrl != '1') {
            exit(result(400, 'Lütfen geçerli bir youtube video urlsi giriniz.'));
        }

        if (isset($file) && !empty($file['name']) && !($file['type'] === 'image/png' || $file['type'] === 'image/jpeg' || $file['type'] === 'video/mp4')) {
            exit(result(400, 'Lütfen <strong>.png, .jpeg, .jpg, .mp4</strong> formatlarında bir dosya seçiniz.'));
        }

        $filePath =  'uploads/' . generateRandomString(15) . '.' . (isset($youtubeVideoId[1]) ? 'mp4' : explode('/', $file['type'])[1]);

        if (isset($youtubeVideoId[1])) {
            parse_str(file_get_contents('https://www.youtube.com/get_video_info?video_id='.$youtubeVideoId[1].'&cpn=CouQulsSRICzWn5E&eurl&el=adunit'), $parsedVideoInfo);

            if ($parsedVideoInfo['status'] == 'fail') {
                exit(result(500, 'Girmiş olduğunuz youtube urlsine ait bir video bulunamadı.'));
            }

            $videoData = json_decode($parsedVideoInfo['player_response'], true);
            $finalStreamMap = [];

            foreach ($videoData['streamingData']['formats'] as $stream) {
                $streamData = $stream;
                $streamData["mime"] = $streamData["mimeType"];
                $mimeType = explode(";", $streamData["mime"]);
                $streamData["mime"] = $mimeType[0];
                $start = stripos($mimeType[0], "/");
                $format = ltrim(substr($mimeType[0], $start), "/");
                $streamData["format"] = $format;
                unset($streamData["mimeType"]);
                $finalStreamMap[] = $streamData;
            }

            $filePath = explode('.', $filePath)[0] . '.' . (isset($finalStreamMap[0]['format']) ? $finalStreamMap[0]['format'] : 'mp4');

            if (!(isset($finalStreamMap[0]['url']) && !empty($finalStreamMap[0]['url']) && file_put_contents($filePath, fopen($finalStreamMap[0]['url'], 'r')))) {
                exit(result(500, 'Teknik bir problem yaşandı lütfen daha sonra tekrar deneyin.'));
            }
        } else {
            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                exit(result(500, 'Teknik bir problem yaşandı lütfen daha sonra tekrar deneyin.'));
            }
        }

        $response = result(200);

        try {
            $countOfSliderContents = $DB_con->prepare('SELECT COUNT(id) as counter FROM slider_contents')->execute();
            $addSliderContent = $DB_con->prepare('INSERT INTO slider_contents
            (youtube_id, file_path, title, title_color, title_font, title_font_size, title_animation, sub_title, sub_title_color, sub_title_font, sub_title_font_size, sub_title_animation, text_direction, status, sort) VALUES 
            (:youtube_id, :file_path, :title, :title_color, :title_font, :title_font_size, :title_animation, :sub_title, :sub_title_color, :sub_title_font, :sub_title_font_size, :sub_title_animation, :text_direction, :status, :sort)');
            $addSliderContent->execute(array(
                ':youtube_id' => isset($youtubeVideoId[1]) ? $youtubeVideoId[1] : '',
                ':file_path' => $filePath,
                ':title' => $title,
                ':title_color' => $titleColor ? $titleColor : '#fff',
                ':title_font' => $titleFont ? $titleFont : 'Roboto',
                ':title_font_size' => $titleFontSize ? $titleFontSize : '40',
                ':title_animation' => $titleAnimation ? $titleAnimation : '',
                ':sub_title' => $subTitle,
                ':sub_title_color' => $subTitleColor ? $subTitleColor : '#fff',
                ':sub_title_font' => $subTitleFont ? $subTitleFont : 'Roboto',
                ':sub_title_font_size' => $subTitleFontSize ? $subTitleFontSize : '16',
                ':sub_title_animation' => $subTitleAnimation ? $subTitleAnimation : '',
                ':text_direction' => $textDirection ? $textDirection : 'left-center',
                ':status' => $status ? $status : 'A',
                ':sort' => $countOfSliderContents['counter'] + 1
            ));
        }
        catch(PDOException $ex)
        {
            @unlink($filePath);
            $response = result(500, 'Teknik bir problem yaşandı lütfen daha sonra tekrar deneyin.');
        }

        exit($response);
    } else if ($pageRequest == 'edit-slider-content') {
        $sliderContentId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($sliderContentId === false) {
            exit(result(500, 'Teknik bir problem yaşandı lütfen daha sonra tekrar deneyin.'));
        }
        $sliderContent = $DB_con->prepare("SELECT id, file_path FROM slider_contents WHERE id = :id");
        $sliderContent->execute(array(':id' => $sliderContentId));
        $fetchSliderContent = $sliderContent->fetch(PDO::FETCH_ASSOC);

        $file = $_FILES['file'];
        $youtubeUrl = filter_input(INPUT_POST, 'youtube_url', FILTER_SANITIZE_URL);
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
        $titleColor = filter_input(INPUT_POST, 'title_color', FILTER_SANITIZE_STRING);
        $titleFont = filter_input(INPUT_POST, 'title_font', FILTER_SANITIZE_STRING);
        $titleFontSize = filter_input(INPUT_POST, 'title_font_size', FILTER_SANITIZE_STRING);
        $titleAnimation = filter_input(INPUT_POST, 'title_animation', FILTER_SANITIZE_STRING);
        $subTitle = filter_input(INPUT_POST, 'sub_title', FILTER_SANITIZE_STRING);
        $subTitleColor = filter_input(INPUT_POST, 'sub_title_color', FILTER_SANITIZE_STRING);
        $subTitleFont = filter_input(INPUT_POST, 'sub_title_font', FILTER_SANITIZE_STRING);
        $subTitleFontSize = filter_input(INPUT_POST, 'sub_title_font_size', FILTER_SANITIZE_STRING);
        $subTitleAnimation = filter_input(INPUT_POST, 'sub_title_animation', FILTER_SANITIZE_STRING);
        $textDirection = filter_input(INPUT_POST, 'text_direction', FILTER_SANITIZE_STRING);
        $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

        if (!empty($file['name']) && !empty($youtubeUrl)) {
            exit(result(400, 'Slider içeriği için bir dosya seçilebilir veya youtube urlsi girilebilir. Hangisi kullanmak istiyorsanız diğerini boş bırakmalısınız.'));
        }

        $youtubeUrlPattern = '~^(?:https?://)?(?:www[.])?(?:youtube[.]com/watch[?]v=|youtu[.]be/)([^&]{11})~x';

        $isYoutubeUrl = preg_match($youtubeUrlPattern, $youtubeUrl, $youtubeVideoId);

        if (!empty($youtubeUrl) && $isYoutubeUrl != '1') {
            exit(result(400, 'Lütfen geçerli bir youtube video urlsi giriniz.'));
        }

        if (isset($file) && !empty($file['name']) && !($file['type'] === 'image/png' || $file['type'] === 'image/jpeg' || $file['type'] === 'video/mp4')) {
            exit(result(400, 'Lütfen <strong>.png, .jpeg, .jpg, .mp4</strong> formatlarında bir dosya seçiniz.'));
        }

        $filePath = $fetchSliderContent['file_path'];

        if (!((!isset($youtubeUrl) || empty($youtubeUrl)) && (!isset($file) || empty($file['name'])))) {

            $filePath = 'uploads/' . generateRandomString(15) . '.' . (isset($youtubeVideoId[1]) ? 'mp4' : explode('/', $file['type'])[1]);

            if (isset($youtubeVideoId[1])) {
                parse_str(file_get_contents('https://www.youtube.com/get_video_info?video_id=' . $youtubeVideoId[1] . '&cpn=CouQulsSRICzWn5E&eurl&el=adunit'), $parsedVideoInfo);

                if ($parsedVideoInfo['status'] == 'fail') {
                    exit(result(500, 'Girmiş olduğunuz youtube urlsine ait bir video bulunamadı.'));
                }

                $videoData = json_decode($parsedVideoInfo['player_response'], true);
                $finalStreamMap = [];

                foreach ($videoData['streamingData']['formats'] as $stream) {
                    $streamData = $stream;
                    $streamData["mime"] = $streamData["mimeType"];
                    $mimeType = explode(";", $streamData["mime"]);
                    $streamData["mime"] = $mimeType[0];
                    $start = stripos($mimeType[0], "/");
                    $format = ltrim(substr($mimeType[0], $start), "/");
                    $streamData["format"] = $format;
                    unset($streamData["mimeType"]);
                    $finalStreamMap[] = $streamData;
                }

                $filePath = explode('.', $filePath)[0] . '.' . (isset($finalStreamMap[0]['format']) ? $finalStreamMap[0]['format'] : 'mp4');

                if (!(isset($finalStreamMap[0]['url']) && !empty($finalStreamMap[0]['url']) && file_put_contents($filePath, fopen($finalStreamMap[0]['url'], 'r')))) {
                    exit(result(500, 'Teknik bir problem yaşandı lütfen daha sonra tekrar deneyin.'));
                }
            } else {
                if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                    exit(result(500, 'Teknik bir problem yaşandı lütfen daha sonra tekrar deneyin.'));
                }
            }
        }

        $response = result(200);

        try {
            $editSliderContent = $DB_con->prepare('UPDATE slider_contents SET 
            youtube_id = :youtube_id,
            file_path = :file_path,
            title = :title,
            title_color = :title_color,
            title_font = :title_font,
            title_font_size = :title_font_size,
            title_animation = :title_animation,
            sub_title = :sub_title,
            sub_title_color = :sub_title_color,
            sub_title_font = :sub_title_font,
            sub_title_font_size = :sub_title_font_size,
            sub_title_animation = :sub_title_animation,
            text_direction = :text_direction,
            status = :status WHERE id = :id');
            $editSliderContent->execute(array(
                ':youtube_id' => isset($youtubeVideoId[1]) ? $youtubeVideoId[1] : '',
                ':file_path' => $filePath,
                ':title' => $title,
                ':title_color' => $titleColor ? $titleColor : '#fff',
                ':title_font' => $titleFont ? $titleFont : 'Roboto',
                ':title_font_size' => $titleFontSize ? $titleFontSize : '40',
                ':title_animation' => $titleAnimation ? $titleAnimation : '',
                ':sub_title' => $subTitle,
                ':sub_title_color' => $subTitleColor ? $subTitleColor : '#fff',
                ':sub_title_font' => $subTitleFont ? $subTitleFont : 'Roboto',
                ':sub_title_font_size' => $subTitleFontSize ? $subTitleFontSize : '16',
                ':sub_title_animation' => $subTitleAnimation ? $subTitleAnimation : '',
                ':text_direction' => $textDirection ? $textDirection : 'left-center',
                ':status' => $status ? $status : 'A',
                ':id' => $fetchSliderContent['id']
            ));
        }
        catch(PDOException $ex)
        {
            @unlink($filePath);
            $response = result(500, 'Teknik bir problem yaşandı lütfen daha sonra tekrar deneyin.');
        }

        if ($filePath !== $fetchSliderContent['file_path']) {
            @unlink($fetchSliderContent['file_path']);
        }

        exit($response);
    } else if ($pageRequest == 'delete-slider-content') {
        $sliderContentId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if($sliderContentId === false)
        {
            exit(result(400));
        }
        if(!isset($sliderContentId) || empty($sliderContentId))
        {
            exit(result(400));
        }
        $sliderContent = $DB_con->prepare("SELECT id, file_path FROM slider_contents WHERE id = :id");
        $sliderContent->execute(array(":id"=>$sliderContentId));
        if($sliderContent->rowCount() != 1)
        {
            exit(result(400));
        }
        $fetchSliderContent = $sliderContent->fetch(PDO::FETCH_ASSOC);
        $deleteSliderContent = $DB_con->prepare("DELETE FROM slider_contents WHERE id = :id");
        $deleteSliderContent->execute(array(":id"=>$sliderContentId));
        @unlink($fetchSliderContent['file_path']);
        exit(result(200));
    }
} else {
    echo 'Not found';
    exit();
}
