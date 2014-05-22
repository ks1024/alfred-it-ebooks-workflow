<?php

define('BASE_URL', 'http://it-ebooks-api.info/v1/');

require_once('workflows.php');

$wf = new Workflows();
$q = trim(strtolower($argv[1]));

$url = BASE_URL.'search/'.$q;
$json = $wf->request($url);
$data = json_decode($json);

$books = $data->Books;
$total = $data->Total;

if ($total > 0):
    foreach ($books as $book):
        $id = $book->ID;
        $title = $book->Title;
        $isbn = $book->isbn;
        $url_book = BASE_URL.'book/'.$id;
        $json_book = $wf->request($url_book);
        $data_book = json_decode($json_book);
        $author = $data_book->Author;
        $year = $data_book->Year;
        $page = $data_book->Page;
        $publisher = $data_book->Publisher;
        
        $wf->result("itebooks-".$id,
                     "http://it-ebooks.info/search/?q=".$isbn."&type=isbn",
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
