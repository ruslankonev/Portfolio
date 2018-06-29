<?php
//Запуск сессий;
session_start();
//если пользователь не авторизован

if (!(isset($_SESSION['Name'])))
{
//идем на страницу авторизации
header("Location: ../../index.html");
exit;
};

?>

<?php
require_once '../blocks/testField.php';

function downloadTable(){
  
    include("../blocks/bd.php");


    $sql = "SELECT * FROM records ";
    $pdo->exec("set names cp1251");
    $result2 = $pdo->query($sql);


    $filename = '../../my.csv';
    $delimetr = ";";

    $fp = fopen($filename, "w+");

    $myrow3 = ["id", "name", "lastname", "email", "country", "token", "etherad", "agree", "ga_clientid", "date"];

    fputcsv($fp, $myrow3, $delimetr);


    while($myrow2 = $result2->fetch()){
  

      fputcsv($fp, $myrow2, $delimetr);

    }

    fclose($fp);


    header('Content-Type: csv');
    header('Content-Disposition: attachment; filename="my.csv"');
    readfile($filename);
    exit();

  
}

/*function printMe(){
  echo "<pre>";
  echo print_r($_POST);
  echo "</pre>";
}*/

if(isset($_POST['getTable'])){
  $tableId = $_POST['getTable'];
  $test = new testField();
  if($test->forname($tableId)){$userList = $tableId;}else{unset($tableId);}

  if($userList == 'Download'){
    downloadTable();
  }
  
  
}



?>

<!DOCTYPE HTML>
<html>
<head>
    <title>Admin</title>
   <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="../css/bootstrap.css" rel="stylesheet"> 
    <script type="text/javascript" src="../js/jquery-3.2.1.min.js"></script>
    
    
</head>

<body>

<?php


include("../blocks/bd.php");
require_once '../blocks/pagination.php';
$pdo->exec("set names utf8");
$result4 = $pdo->query("SELECT * FROM records");

$pagination = new Pagination();

$num = $pagination->num;



$page = $pagination->Page();



$this_page_first_result = $pagination->forLimit();







echo <<<HERE


<div class="container" style="max-width: 1600px; width: 100%;">
    <div class="row">
      <div class="col-lg-12">
        <div class="page-header">
          <div class="alert alert-info" role="alert">
            
            <ul>
              <li><a href="logout.php" class="alert-link">Exit</a>.</li>
            </ul>
          </div>
        </div>

        <div class="col-lg-12  panel panel-default">
          <div "class="panel-heading">
            <h3 class="panel-title"></h3>
          </div>
          <div id="output" class="panel-body" style="overflow: auto">
         <table class="table table-bordered table-inverse">
  <thead>
 
    <tr>
      <th>#</th>
      <th>NAME</th>
      <th>LASTNAME</th>
      <th>EMAIL</th>
      <th>COUNTRY</th>
      <th>TOKEN</th>
      <th>ETHERAD</th>
      <th>AGREE</th>
      <th>GA_CLIENTID</th>
      <th>DATE</th>
    </tr>
  </thead>
  <tbody>
HERE;

/*Cсоставление запроса*/

$sql = "SELECT * FROM records LIMIT " . $this_page_first_result . ',' . $num;
$result2 = $pdo->query($sql);
$number_of_results = $result4->rowCount();
$number_of_pages = ceil($number_of_results/$num);








 $myrow3 = ["id", "name", "lastname", "email", "country", "token", "etherad", "agree", "ga_clientid", "date"];

while($myrow2 = $result2->fetch()){
   printf("
    <tr>
    <th scope='row'>%s</th>
    <td>%s</td>
    <td>%s</td>
    <td>%s</td>
    <td>%s</td>
    <td>%s</td>
    <td>%s</td>
    <td>%s</td>
    <td>%s</td>
    <td>%s</td>
    <tr>

    ", $myrow2['id'], $myrow2['name'], $myrow2['lastname'], $myrow2['email'], $myrow2['country'], $myrow2['token'], $myrow2['etherad'], $myrow2['agree'], 
    $myrow2['ga_clientid'], $myrow2['date']); 
}





echo "</tbody></table><div class='text-center'>
 <nav aria-label='Pagi'><ul class='pagination'>
 ";
$paginationCtrls = '';


if($number_of_pages != 1){

    if ($page > 3) {

        $paginationCtrls .= '<a href="content.php?page=1" class="btn btn-default" role="button"><b><< </b></a>&nbsp;';
    }   
    if ($page > 1) {
        $previous = $page - 1;
        $paginationCtrls .= '<a href="content.php?page='.$previous.'" class="btn btn-default" role="button"><b>< </b></a>';

        for($i = $page-2; $i < $page; $i++){
            if($i > 0){
                $paginationCtrls .= '<a href="content.php?page='.$i.'" class="btn btn-default" role="button">'.$i.'</a>';
            }
        }
    }

    $paginationCtrls .= '<p color:black; class="btn btn-default" role="button"><b>'.$page.'</b></p>';

    for($i = $page+1; $i <= $number_of_pages; $i++){
        $paginationCtrls .= '<a href="content.php?page='.$i.'" class="btn btn-default" role="button">'.$i.'</a>';
        if($i >= $page+2){
            break;
        }
    }

    if ($page != $number_of_pages) {
        $next = $page + 1;
        $paginationCtrls .= '<a href="content.php?page='.$next.'" class="btn btn-default" role="button"><b>></b></a> ';
    }
        if ($page < $number_of_pages-2) {

        $paginationCtrls .= '<a href="content.php?page='.$number_of_pages.'" class="btn btn-default" role="button"><b> >></b></a>';
    }
}

echo $paginationCtrls;








    
        
    

echo <<<HERE
              </ul>
            </nav>
            </div>

          </div>
        </div>

        
      </div>
    </div>
    <div class="text-center">
      <form method="post">
        <input class="btn btn-success" type="submit" name="getTable" value="Download">
      </form>
    </div>
  </div>

</body>
</html>


HERE;








?>