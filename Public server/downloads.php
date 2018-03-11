<?php 

//where the files are
$downloads_folder = 'worker/';
$counters_folder = 'counters/';

//has a file name been passed?
if(!empty($_GET['file'])){
    //protect from people getting other files
    $file = basename($_GET['file']);

    //does the file exist?
    if(file_exists($downloads_folder.$file)){

        //update counter - add if dont exist
        if(file_exists($counters_folder.$file.'_counter.txt')){
            $fp = fopen($counters_folder.$file.'_counter.txt', "r");
            $count = fread($fp, 1024);
            fclose($fp);
            $fp = fopen($counters_folder.$file.'_counter.txt', "w");
            fwrite($fp, $count + 1);
            fclose($fp);
        }else{
            $fp = fopen($counters_folder.$file.'_counter.txt', "w+");
            fwrite($fp, 1);
            fclose($fp);
        }

        //set force download headers
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$file.'"');
        header('Content-Transfer-Encoding: binary');
        header('Connection: Keep-Alive');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . sprintf("%u", filesize($downloads_folder.$file)));

        //open and output file contents
        $fh = fopen($downloads_folder.$file, "rb");
        while (!feof($fh)) {
            echo fgets($fh);
            flush();
        }
        fclose($fh);
        exit;
    }else{
        header("HTTP/1.0 404 Not Found");
        exit('File not found!');
    }
}else{
    exit(header("Location: index.php"));
}








?>