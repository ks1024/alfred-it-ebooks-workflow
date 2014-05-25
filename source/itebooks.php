<?php

define('API_URL', 'http://it-ebooks-api.info/v1/');
define('SITE_URL', 'http://it-ebooks.info');

require_once('workflows.php');

$wf = new Workflows();
$q = trim(strtolower($argv[1]));

$url = API_URL.'search/'.$q;
$json = $wf->request($url);
$data = json_decode($json);

$books = $data->Books;
$total = $data->Total;

if ($total > 0):
    foreach ($books as $book):
        $id = $book->ID;
        $title = $book->Title;
        $isbn = $book->isbn;
        // get html page content
        $args = array(
            'q'     => $isbn,
            'type'  => 'isbn'
        );
        $params = http_build_query($args);
        $url_isbn = SITE_URL.'/search/?'.$params;
        $html_data = file_get_contents($url_isbn);
        // pattern /book/n¡ã/
        $pattern = '/\/book\/[0-9]+\//';
        preg_match($pattern, $html_data, $matches);
        $url_book_details = SITE_URL.$matches[0];
        
        $url_book = API_URL.'book/'.$id;
        $json_book = $wf->request($url_book);
        $data_book = json_decode($json_book);
        $author = $data_book->Author;
        $year = $data_book->Year;
        $page = $data_book->Page;
        $publisher = $data_book->Publisher;
        $download_link = $data_book->Download;
        
        $wf->result("itebooks-".$id,
                     "$url_book_details",
                     "$title",
                     "by $author | Publisher: $publisher | Pages: $page | Year: $year | ISBN: $isbn ",
                     "icon.png");
    endforeach;
else:
    $wf->result('itebooks.noresult', 
                'http://it-ebooks-search.info/search?q='.$q.'&type=title', 
                'No result found', 
                'no', 
                'icon.png');
endif;

echo $wf->toxml();
