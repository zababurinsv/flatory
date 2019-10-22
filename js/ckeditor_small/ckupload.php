<? 
require_once($_SERVER['DOCUMENT_ROOT']."/site_config.php"); 

$file = time()."_".$_FILES['upload']['name'];
$url = '/images/files/'.$file;
 //extensive suitability check before doing anything with the file...
    if (($_FILES['upload'] == "none") OR (empty($_FILES['upload']['name'])) )
    {
       $message = "No file uploaded.";
    }
    //else if ($_FILES['upload']["size"] == 0)
    //{
    //   $message = "The file is of zero length.";
    //}
    else if (($_FILES['upload']["type"] != "image/pjpeg") AND ($_FILES['upload']["type"] != "image/jpeg") AND ($_FILES['upload']["type"] != "image/png"))
    {
       $message = "Изображение должно быть JPG или PNG формате. Пожалуйста, загрузите JPG или PNG вместо этого.";
    }
    else if (!is_uploaded_file($_FILES['upload']["tmp_name"]))
    {
       $message = "You may be attempting to hack our server. We're on to you; expect a knock on the door sometime soon.";
    }
    else {
      $message = "";
      $url = $file_site_root."/images/files/".$file;
      $move = @ move_uploaded_file($_FILES['upload']['tmp_name'], $url);
      if(!$move)
      {
         $message = "Error moving uploaded file. Check the script is granted Read/Write/Modify permissions.";
      }
      $url = $server_site_root."/images/files/".$file;
    }
 
$funcNum = $_GET['CKEditorFuncNum'] ;
echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '$message');</script>";
?>