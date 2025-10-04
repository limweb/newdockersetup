<?php

//----------------------------------------------
//FILE NAME:  IndexController.php gen for Servit Framework Controller
//Created by: Tlen<limweb@hotmail.com>
//DATE: 2020-12-24(Thu)  16:51:28

//----------------------------------------------
use    Illuminate\Database\Capsule\Manager as Capsule;
use    \Jacwright\RestServer\RestException;
use    Illuminate\Support\Collection;
use    Carbon\Carbon;

class IndexController extends BaseController
{

    /**
     *@ noAuth
     *@url GET /index
     *----------------------------------------------
     *FILE NAME:  IndexController.php gen for Servit Framework Controller
     *DATE: 2020-12-24(Thu)  16:51:28
     *----------------------------------------------
     */
    public function index()
    {
        // Capsule::enableQueryLog();
        dump('---test---');
        // // $data = Capsule::select('SELECT * FROM [dbo].[Country]');
        // $data = Country::get();
        // // dump(__DIR__);
        // // dump($data);
        // echo $data;
        // $sql = Capsule::getQueryLog();
        // dump($sql);
    }



    /**
     *@ noAuth
     *@ url GET /upload
     *----------------------------------------------
     *FILE NAME:  IndexController.php gen for Servit Framework Controller
     *Created by: Tlen<limweb@hotmail.com>
     *DATE:  2022-05-26(Thu)  18:36:39

     *----------------------------------------------
     */
    public function uploadweb()
    {
        echo 'test upload';

        $html = <<<HTML
            <input type="file" accept="image/*" capture="camera" name="camera" />
            <input id="fileupload" type="file" name="fileupload" />
            <button id="upload-button" onclick="uploadFile()"> Upload </button>
            <script>
            async function uploadFile() {
                let formData = new FormData();
                formData.append("file", fileupload.files[0]);
                await fetch('/upload', {
                method: "POST",
                body: formData
                });
                alert('The file has been uploaded successfully.');
            }
            </script>
       HTML;

        echo $html;
    }



    /**
     *@ noAuth
     *@ url POST /upload
     *----------------------------------------------
     *FILE NAME:  IndexController.php gen for Servit Framework Controller
     *Created by: Tlen<limweb@hotmail.com>
     *DATE:  2022-05-26(Thu)  18:35:58

     *----------------------------------------------
     */
    public function upload()
    {
        try {
            $req = new \Request();
            // dump($req->files->file['name']);
            $file = $req->files->file;
            $arrdata = $req->input->toArray();
            $msg = 'สำเร็จ';
            $type = 'success'; //success,info,error,warning
            $title = 'Successed!';
            $success = true;

            $output_file = $req->files->file['name'];
            $tmpname = $req->files->file['tmp_name'];
            // file_put_contents(SRVPATH.'/buyimages/'.$output_file, file_get_contents($b64img));

            // name" => "Video_2022-05-25_225433.wmv"
            // "full_path" => "Video_2022-05-25_225433.wmv"
            // "type" => "video/x-ms-wmv"
            // "tmp_name" => "/tmp/phpMjckJn"
            // "error" => 0
            // "size" => 10799647
            $target_file = '/tmp/' . $output_file;
            move_uploaded_file($tmpname, $target_file);

            return [
                'filename' => $file,
                'target' => $target_file,
                'status' => '1',
                'success' => $success,
                'msg' => $msg,
                'type' => $type,
                'title' => $title,
                'func' => __CLASS__ . '/' . __FUNCTION__
            ];
        } catch (\Exception $e) {
            return [
                'status' => '0',
                'success' => false,
                'msg' => $e->getMessage(),
                'func' => __CLASS__ . '/' . __FUNCTION__,
            ];
        }
    }
}
