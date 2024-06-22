<?php
/*
* Telegram bot untuk PPDB 2024 Provinsi Banten
*
* 
* Cari KiBenen_bot di telegram untuk mencoba
* dangdeur@gmail.com (SMKN 2 Pandeglang)
*
*/

//isi token
define('BOT_TOKEN', '-ISI TOKEN BOT-');
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');

//database
$host ='';
$user='';
$password='';
$db='';


function db()
{
  global $host,$user,$password,$db;
  static $koneksi;
    if ($koneksi===NULL){ 
        $koneksi = mysqli_connect ($host,$user,$password,$db);
    }
    return $koneksi;
}


function apiRequestWebhook($method, $parameters) {
  if (!is_string($method)) {
    error_log("Method name must be a string\n");
    return false;
  }

  if (!$parameters) {
    $parameters = array();
  } else if (!is_array($parameters)) {
    error_log("Parameters must be an array\n");
    return false;
  }

  $parameters["method"] = $method;

  $payload = json_encode($parameters);
  header('Content-Type: application/json');
  header('Content-Length: '.strlen($payload));
  echo $payload;

  return true;
}

function exec_curl_request($handle) {
  $response = curl_exec($handle);

  if ($response === false) {
    $errno = curl_errno($handle);
    $error = curl_error($handle);
    error_log("Curl returned error $errno: $error\n");
    curl_close($handle);
    return false;
  }

  $http_code = intval(curl_getinfo($handle, CURLINFO_HTTP_CODE));
  curl_close($handle);

  if ($http_code >= 500) {
    // do not wat to DDOS server if something goes wrong
    sleep(10);
    return false;
  } else if ($http_code != 200) {
    $response = json_decode($response, true);
    error_log("Request has failed with error {$response['error_code']}: {$response['description']}\n");
    if ($http_code == 401) {
      throw new Exception('Invalid access token provided');
    }
    return false;
  } else {
    $response = json_decode($response, true);
    if (isset($response['description'])) {
      error_log("Request was successful: {$response['description']}\n");
    }
    $response = $response['result'];
  }

  return $response;
}

function apiRequest($method, $parameters) {
  if (!is_string($method)) {
    error_log("Method name must be a string\n");
    return false;
  }

  if (!$parameters) {
    $parameters = array();
  } else if (!is_array($parameters)) {
    error_log("Parameters must be an array\n");
    return false;
  }

  foreach ($parameters as $key => &$val) {
    // encoding to JSON array parameters, for example reply_markup
    if (!is_numeric($val) && !is_string($val)) {
      $val = json_encode($val);
    }
  }
  $url = API_URL.$method.'?'.http_build_query($parameters);

  $handle = curl_init($url);
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($handle, CURLOPT_TIMEOUT, 60);

  return exec_curl_request($handle);
}

function apiRequestJson($method, $parameters) {
  if (!is_string($method)) {
    error_log("Method name must be a string\n");
    return false;
  }

  if (!$parameters) {
    $parameters = array();
  } else if (!is_array($parameters)) {
    error_log("Parameters must be an array\n");
    return false;
  }

  $parameters["method"] = $method;

  $handle = curl_init(API_URL);
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($handle, CURLOPT_TIMEOUT, 60);
  curl_setopt($handle, CURLOPT_POST, true);
  curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($parameters));
  curl_setopt($handle, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

  return exec_curl_request($handle);
}

function pendaftar($text,$message_id,$chat_id)
{
  $kk=explode(" ",$text);
  $pilihan=$kk[1];
  if (isset($pilihan))
  {

  
    $koneksi = db();
    $data_kk=array();
    $q='SELECT DISTINCT pilihan_'.$pilihan.',count(pilihan_'.$pilihan.') FROM `pendaftar` GROUP BY pilihan_'.$pilihan;
    $h =mysqli_query($koneksi,$q);
    while($data=$h -> fetch_row())
    {
      $sekolah=explode(" ",$data[0]);
      if($sekolah[0]=="SMKN" && $sekolah[1]=="2" && $sekolah[2]=="PANDEGLANG")
      {
        unset($sekolah[0]);
        unset($sekolah[1]);
        unset($sekolah[2]);
        unset($sekolah[3]);
        $jurusan=implode(" ",$sekolah);
        $data_kk[]=$jurusan.'('.$data[1].')';
      }
    }
    apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Update Data : <b>21 Juni 2024 Pukul 22.00</b>','parse_mode'=>'html'));
    foreach ($data_kk as $k)
    {
      apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => $k,'parse_mode'=>'html'));
    }
  }
  else
  {
    //kalau tidak ada pilihan
    apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Informasi pendaftar tidak dapat ditampilkan karena anda tidak menuliskan pilihan','parse_mode'=>'html'));
    apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Format penulisan yang benar adalah sebagai berikut :','parse_mode'=>'html'));
    apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'pendaftar<spasi><pilihan ke>','parse_mode'=>'html'));
    apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'contoh untuk menampilkan jumlah pendaftar pada pilihan 1 :','parse_mode'=>'html'));
    apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => '<b>pendaftar 1</b>','parse_mode'=>'html'));
    apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'contoh untuk menampilkan jumlah pendaftar pada pilihan 2 :','parse_mode'=>'html'));
    apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => '<b>pendaftar 2</b>','parse_mode'=>'html'));
  }
  
}



function status($text,$message_id,$chat_id)
{
  $perintah=explode(" ",$text);
  if (count($perintah)>1) 
    {
      $nisn=$perintah[1];
      $koneksi = db();
      $q="SELECT * FROM `pendaftar` WHERE `no_un`='".$nisn."'";
      $h =mysqli_query($koneksi,$q);
      
      apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Update Data : <b>21 Juni 2024 Pukul 22.00</b>','parse_mode'=>'html'));
      if ($h->num_rows > 0) 
        {
          $data = $h -> fetch_assoc();
          
          apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Status pendaftaran atas nama <b>'.strtoupper($data['nama']).'</b>','parse_mode'=>'html'));
          apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Asal sekolah : <b>'.strtoupper($data['asal_sekolah']).'</b>','parse_mode'=>'html'));
          apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Nama orangtua/wali : <b>'.strtoupper($data['nama_orang_tua_wali']).'</b>','parse_mode'=>'html'));
          apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Nomor HP : <b>'.$data['nomor_hp_wa_(aktif)'].'</b>','parse_mode'=>'html'));
          
          apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'pilihan jurusan 1 : <b>'.$data['pilihan_1'].'</b>','parse_mode'=>'html'));
          if($data['pilihan_2'] != "")
            {
              apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'pilihan jurusan 2 : <b>'.$data['pilihan_2'].'</b>','parse_mode'=>'html'));
            }
            else
            {
              apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => '<b>TIDAK MEMILIH JURUSAN KEDUA</b>','parse_mode'=>'html'));
            }
          
            if($data['operator'] != "-")
            {
              apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Telah diverifikasi oleh : <b>'.$data['operator'].'</b>','parse_mode'=>'html'));
            }
          else
            {
              apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => '<b>BELUM DIVERIFIKASI</b>','parse_mode'=>'html'));
            }
        }
      else 
        {
          
          apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Status pendaftaran dengan NISN <b>'.$nisn.'</b> tidak ditemukan, pastikan NISN yang dimasukan dan format penulisan perintah adalah benar','parse_mode'=>'html'));
          apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Untuk mengetahui informasi pendaftaran anda, ketik perintah berikut'));
          apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'status<spasi>NISN'));
          apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'contoh :'));
          apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => '<b>status 0123456789</b>','parse_mode'=>'html'));
          
        }
  
    }
  else 
    {
      apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Informasi status pendaftaran anda tidak dapat ditampilkan karena anda tidak menuliskan <b>NISN</b>','parse_mode'=>'html'));
     
      
    }
    apiRequestJson("sendMessage", array('chat_id' => $chat_id, "text" => 'Masukan perintah langsung atau pilih menu dibawah ini', 'reply_markup' => array(
      'keyboard' => array(array('Jadwal', 'Alur Pendaftaran', 'Persyaratan', 'Kuota'),
                          array('Cek Status','Pendaftar')),
      'one_time_keyboard' => true,
      'resize_keyboard' => true)));
  

}

Function cekstatus($message_id,$chat_id)
{
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Untuk mengetahui informasi pendaftaran anda, ketik perintah berikut'));
      apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'status<spasi>NISN'));
      apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'contoh :'));
      apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => '<b>status 0123456789</b>','parse_mode'=>'html'));
      apiRequestJson("sendMessage", array('chat_id' => $chat_id, "text" => 'Masukan perintah langsung atau pilih menu dibawah ini', 'reply_markup' => array(
          'keyboard' => array(array('Jadwal', 'Alur Pendaftaran', 'Persyaratan', 'Kuota'),
                              array('Cek Status','Pendaftar')),
          'one_time_keyboard' => true,
          'resize_keyboard' => true)));
}


function sambutan($message_id,$chat_id)
{
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Selamat datang di KiBenen, layanan informasi SMKN 2 Pandeglang'));
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Perintah yang tersedia saat ini :'));
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'jadwal,alur,persyaratan,kuota,status,pendaftar'));
 
  apiRequestJson("sendMessage", array('chat_id' => $chat_id, "text" => 'Masukan perintah langsung atau pilih menu dibawah ini', 'reply_markup' => array(
      'keyboard' => array(array('Jadwal', 'Alur Pendaftaran', 'Persyaratan', 'Kuota'),
      array('Cek Status','Pendaftar')),
      'one_time_keyboard' => true,
      'resize_keyboard' => true)));

}

function jadwal($message_id,$chat_id)
{
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Jadwal PPDB SMKN 2 Pandeglang adalah sebagai berikut :'));
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Pendaftaran : <b>19-26 Juni 2024</b>','parse_mode'=>'html'));
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Verifikasi : <b>21-26 Juni 2024</b>','parse_mode'=>'html'));
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Tes minat & bakat : <b>1-3 Juli 2024</b>','parse_mode'=>'html'));
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Pengumuman : <b>8 Juli 2024</b>','parse_mode'=>'html'));
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Daftar ulang : <b>9-12 Juli 2024</b>','parse_mode'=>'html'));
  apiRequestJson("sendMessage", array('chat_id' => $chat_id, "text" => 'Pilih menu dibawah ini', 'reply_markup' => array(
    'keyboard' => array(array('Jadwal', 'Alur Pendaftaran', 'Persyaratan', 'Kuota'),
    array('Cek Status','Pendaftar')),
    'one_time_keyboard' => true,
    'resize_keyboard' => true)));
}

function alur($message_id,$chat_id)
{
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Alur pendaftaran PPDB di SMKN 2 Pandeglang'));
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => '1. Calon peserta didik mempersiapkan berkas persyaratan','parse_mode'=>'html'));
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => '2. Calon peserta didik mengakses laman situs PPDB online','parse_mode'=>'html'));
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => '3. Calon peserta didik melakukan pengajuan pendaftaran mandiri dengan mengisi formulir secara online','parse_mode'=>'html'));
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => '4. Calon peserta didik mengunggah dokumen persyaratan','parse_mode'=>'html'));
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => '5. Calon peserta didik memilih SMKN 2 Pandeglang dengan 2 pilihan jurusan','parse_mode'=>'html'));
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => '6. Calon peserta didik mencetak bukti pendaftaran','parse_mode'=>'html'));
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => '7. Calon peserta didik datang ke sekolah mengambil antrian dan datang kembali ke sekolah sesuai dengan jadwal di nomor antrian','parse_mode'=>'html'));
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => '8. Operator sekolah melakukan verifikasi data calon peserta didik','parse_mode'=>'html'));
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => '9. Calon peserta didik mengikuti tes minat dan bakat','parse_mode'=>'html'));
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => '10. Calon peserta didik melihat hasil pengumuman secara online','parse_mode'=>'html'));
  apiRequestJson("sendMessage", array('chat_id' => $chat_id, "text" => 'Pilih menu dibawah ini', 'reply_markup' => array(
    'keyboard' => array(array('Jadwal', 'Alur Pendaftaran', 'Persyaratan', 'Kuota'),
    array('Cek Status','Pendaftar')),
    'one_time_keyboard' => true,
    'resize_keyboard' => true)));
}

function kuota($message_id,$chat_id)
{
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Kapasitas Konsentrasi Keahlian di SMKN 2 Pandeglang'));
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => '1. Agribisnis Tanaman Pangan Dan Hortikultura <b>(ATPH)</b> : 108 peserta didik','parse_mode'=>'html'));
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => '2. Agribisnis Pengolahan Hasil Pertanian <b>(APHP)</b> : 72 peserta didik','parse_mode'=>'html'));
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => '3. Teknik Installasi Tenaga Listrik <b>(TITL)</b> : 72 peserta didik','parse_mode'=>'html'));
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => '4. Teknik Kendaraan Ringan <b>(TKR)</b> : 72 peserta didik','parse_mode'=>'html'));
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => '5. Desain Komunikasi Visual <b>(DKV)</b> : 108 peserta didik','parse_mode'=>'html'));
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => '6. Teknik Komputer Jaringan <b>(TKJ)</b> : 108 peserta didik','parse_mode'=>'html'));
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => '7. Teknik Sepeda Motor <b>(TSM)</b> : 72 peserta didik','parse_mode'=>'html'));
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => '8. Analisis Pengujian Laboratorium <b>(APL)</b> : 72 peserta didik','parse_mode'=>'html'));
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Total : <b>684</b> peserta didik','parse_mode'=>'html'));
  apiRequestJson("sendMessage", array('chat_id' => $chat_id, "text" => 'Pilih menu dibawah ini', 'reply_markup' => array(
    'keyboard' => array(array('Jadwal', 'Alur Pendaftaran', 'Persyaratan', 'Kuota'),
    array('Cek Status','Pendaftar')),
    'one_time_keyboard' => true,
    'resize_keyboard' => true)));
}

function persyaratan($message_id,$chat_id)
{
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Persyaratan yang harus disiapkan dalam proses pendaftaran adalah :'));
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => '1. Akta kelahiran','parse_mode'=>'html'));
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => '2. Kartu keluarga','parse_mode'=>'html'));
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => '3. Rapor semester 1-6','parse_mode'=>'html'));
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => '4. Pas foto 3x4 background merah 2 lembar','parse_mode'=>'html'));
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => '5. Surat Keterangan Lulus asli','parse_mode'=>'html'));
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => '6. Surat keterangan sehat dan bebas buta warna','parse_mode'=>'html'));
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => '7. Kartu NISN','parse_mode'=>'html'));
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => '8. Kartu KIP/PKH/KKS bagi yang memiliki','parse_mode'=>'html'));
  apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => '9. Sertifikat kejuaran lomba (Kabupaten, Provinsi, Nasional) bagi yang memiliki','parse_mode'=>'html'));
  apiRequestJson("sendMessage", array('chat_id' => $chat_id, "text" => 'Pilih menu dibawah ini', 'reply_markup' => array(
    'keyboard' => array(array('Jadwal', 'Alur Pendaftaran', 'Persyaratan', 'Kuota'),
    array('Cek Status','Pendaftar')),
    'one_time_keyboard' => true,
    'resize_keyboard' => true)));
}



function processMessage($message) {
  // process incoming message
  $message_id = $message['message_id'];
  $chat_id = $message['chat']['id'];
  if (isset($message['text'])) {
    // incoming text message
    $text = strtolower($message['text']);

    switch ($text) {
      case strpos($text, "/start")===0:
        sambutan($message_id,$chat_id);
        break;
      case $text === "jadwal":
        jadwal($message_id,$chat_id);
        break;
        case strpos($text, "alur")===0:
        alur($message_id,$chat_id);
        break;
      case $text === "persyaratan":
        persyaratan($message_id,$chat_id);
        break;
      case $text === "kuota":
        kuota($message_id,$chat_id);
        break;
      case strpos($text, "cek")===0:
          cekstatus($message_id,$chat_id);
          break;
      case strpos($text, "status")===0:
        status($text,$message_id,$chat_id);
        break;
      case strpos($text, "pendaftar")===0:
            pendaftar($text,$message_id,$chat_id);
            break;
      default:
        
        apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Perintah tidak ditemukan'));
        break;
    }

  } else {
    apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Pilih tombol atau ketik perintah'));
  }
}


//atur alamat webhook
define('WEBHOOK_URL', 'ISI ALAMAT WEBHOOK');

if (php_sapi_name() == 'cli') {
  // if run from console, set or delete webhook
  apiRequest('setWebhook', array('url' => isset($argv[1]) && $argv[1] == 'delete' ? '' : WEBHOOK_URL));
  exit;
}


$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (!$update) {
  // receive wrong update, must not happen
  exit;
}

if (isset($update["message"])) {
  processMessage($update["message"]);
}
