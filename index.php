<?php
require_once 'server/pageRoutes.php';

if($pageData['security']){
    require 'classes/General.php';
    $General = new General();
    $General->security();
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8"><meta http-equiv="x-ua-compatible" content="ie=edge">
        <title><?php echo $pageData['title']; ?></title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <link href="http://fonts.googleapis.com/css?family=Adamina" rel="stylesheet" >
        <link href="http://fonts.googleapis.com/css?family=Lobster" rel="stylesheet">
        <link rel="stylesheet" href=<?php echo $pageData['base']."public/css/main.css" ?> >
    </head>
    <body>
        <div id="msgbox"></div>
        <header>
            <div class="container">
                <div class="row">
                    <div class="md-col-12">
                        <h1><?php echo $pageData['heading']; ?></h1>   
                    </div>
                </div>
            </div>
        </header>
        <?php if($pageData['nav']){
            require_once 'views/partials/navigation.php';
        }?>
        <?php echo $pageData['content']; ?>
    
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

        <?php $jsFilesArray= explode('^', $pageData['js']);
            $i = 0;
            $js = "";
            while($i < count($jsFilesArray)){
                $js .= "<script src=".$pageData['base']. "public/js/".$jsFilesArray[$i].".js></script>";
                $i++;
            }
            echo $js;
        ?>

    </body>
</html>
