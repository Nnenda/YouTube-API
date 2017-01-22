<?php

/**
 *
 *
 **/

if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    throw new \Exception('please run "composer require google/apiclient:~2.0" in "' . __DIR__ .'"');
}

require_once __DIR__ . '/vendor/autoload.php';

$htmlBody = '';
$searchString = '';
$viewCounts = array();
$sortFoundVideos = array();
$videoInfo = array();

$client = new Google_Client();

if (isset($_GET['q'])) {
    $searchString = $_GET['q'];

    $DEVELOPER_KEY = 'AIzaSyDi5yLP5nNbRjXgM0sxpHeAOe3sjXnD0_U';

    $client->setDeveloperKey($DEVELOPER_KEY);

    $youtube = new Google_Service_YouTube($client);

    $htmlBody = '';
    $foundIdsToString = '';
    $foundVideo = '';

    try {

        // Поиск
        $foundIds = $youtube->search->listSearch('id', [
                'q' => $_GET['q'],
                'maxResults' => '20',
                'order' => 'date',
        ]);

        // Ids в одну строку
        foreach($foundIds['items'] as $id){
            $foundIdsToString .= sprintf($id['id']['videoId']).',';
        }
        $foundIdsToString = substr($foundIdsToString, 0, -1);

        // Найденные видео
        $foundVideos = $youtube->videos->listVideos('snippet, player, statistics', [
            'id' => $foundIdsToString,
        ]);

        // Сортировка по популярности ( кол-во просмотров )**********************
        foreach ($foundVideos["items"] as $video) {
            $viewCounts[] = $video['statistics']['viewCount'];
        }

        foreach ($foundVideos["items"] as $video) {
            $sortFoundVideos[] = $video;
        }

        array_multisort($viewCounts, SORT_DESC, SORT_REGULAR, $sortFoundVideos);

        //***********************************************************************

        // Информация о видео
        foreach ($sortFoundVideos as $video) {
            $videoInfo[] = $video['snippet'];
        }

        // Вывод в Html строку
        $foundVideo .= sprintf("<tr class='table-bordered'>");
        foreach ($sortFoundVideos as $video){
            $foundVideo .= sprintf("<td>
                                        <div class='text-center set'>
                                            <a>
                                              <b>%s</b>
                                            </a>
                                            <br>
                                            <div class='content' style='display: none'>
                                              <p>%s</p>
                                            </div>
                                            <label>Author: %s</label>
                                            <br>
                                            <label>Published At: %s</label>
                                            
                                         </div>      
                                    </td>",
                $video['snippet']['title'],
                $video['player']["embedHtml"],
                $video['snippet']['channelTitle'],
                substr($video['snippet']['publishedAt'], 0, -5)


            );
            $foundVideo .= sprintf("</tr><tr class='table-bordered'>");
        }

        $htmlBody .= <<<END
        <div class="col-sm-offset-3 col-sm-6">
            <table class="table col-xs-offset-0 col-xs-10">
                    $foundVideo
            </table>
        </div>
END;

    } catch (Google_Service_Exception $e) {
        $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
            htmlspecialchars($e->getMessage()));
    } catch (Google_Exception $e) {
        $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
            htmlspecialchars($e->getMessage()));
    }
}
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>YouTube Search</title>
    <link rel="stylesheet" href="style/bootstrap.css">
    <link rel="stylesheet" href="style/style.css">

    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="js/upper.js"></script>
    <script type="text/javascript" src="js/accordion.js"></script>

</head>
<body>
<img src="images/ArrowUp.png" alt="" class="upper" style="display: none;">
<form method="GET">
    <div class="container col-sm-2 col-sm-offset-5 rounded well text-center" >
        <div class="form-group">
            <input type="text" name="q" id="q" class="form-control" placeholder="Search string" value="<?=$searchString?>">
        </div>
        <button type="submit" class="btn btn-primary col-sm-12">Search</button>
    </div>
</form>
<?=$htmlBody?>
</body>
</html>
