<?php

function imgClone($img) {
    return imagecrop($img, array('x'=>0,'y'=>0,'width'=>imagesx($img),'height'=>imagesy($img)));
}

function imgGetAspectRatio($width, $height) {
    $gcd = static function($width, $height) use (&$gcd) {
        return ($width % $height) ? $gcd($height, $width % $height) : $height;
    };
    $divisor = $gcd($width, $height);
    return $width / $divisor . ':' . $height / $divisor;
}

function imgLetterbox($image, $background = false, $canvas_w, $canvas_h) {
    if (!$background) {
        $background = imagecolorallocate($image, 255, 255, 255);
    }
    $img_h = imagesy($image);
    $img_w = imagesx($image);
    $img = imagecreatetruecolor($canvas_w, $canvas_h);
    imagefill($img, 0, 0, $background);
    $xoffset = round(($canvas_w - $img_w) / 2);
    $yoffset = round(($canvas_h - $img_h) / 2);
    imagecopymerge($img, $image, $xoffset, $yoffset, 0,0, $img_w, $img_h, 100);
    return $img; 
}

function imgExifOrient($image, $filename) {
    $exif = exif_read_data($filename);
    if (!empty($exif['Orientation'])) {
        switch ($exif['Orientation']) {
            case 3:
               return imagerotate($image, 180, 0);
                break;
            case 6:
                return imagerotate($image, 90, 0);
                break;
            case 8:
                return imagerotate($image, -90, 0);
                break;
        }
    }
    return $image;
}

 
function imgProcess($img, $width, $height, $fn) {
	$gdimage_t_h = $height;
	$gdimage_t = imgClone($img);
	$gdimage_t_as = imgGetAspectRatio(imagesx($gdimage_t), imagesy($gdimage_t));
	$gdimage_t_as = explode(':', $gdimage_t_as);
	$gdimage_t_w = round($gdimage_t_as[0]*($height/$gdimage_t_as[1]));
	if ($gdimage_t_w > $width) {
		$gdimage_t_h = round($gdimage_t_as[1]*($width/$gdimage_t_as[0]));
		$gdimage_t_w = $width;
	}
	$gdimage_t = imagescale($gdimage_t, $gdimage_t_w, $gdimage_t_h);
	// $gdimage_t = imgLetterbox($gdimage_t, false, $width, $height);
	imagejpeg($gdimage_t, $fn);
}

function imgGetCamera($photo) {
    $return = "Unavailable";
    $exif_ifd0 = exif_read_data($photo ,'IFD0' ,0);     

    if($exif_ifd0 != FALSE) {
        if (@array_key_exists('Model', $exif_ifd0)) {
            if (@array_key_exists('Make', $exif_ifd0)) {
                if (strpos($exif_ifd0["Model"], $exif_ifd0["Make"]) === 0) {
                    $return = $exif_ifd0["Model"];
                } else {
                    $return = $exif_ifd0["Make"] . " " . $exif_ifd0["Model"];
                }
            } else {
                $return = $exif_ifd0["Model"];
            }
        } elseif (@array_key_exists('Make', $exif_ifd0)) {
            $return = $exif_ifd0["Make"];
        }        
    }

    return $return;
}

function getNextIDNew($tablename) {
	global $conn;

	$asd = $conn->query("SHOW TABLE STATUS LIKE '$tablename'");
	while($row = $asd->fetch()) {
		return $row["Auto_increment"];
	}
}

function getNextID($sql, $rowname) {
	global $conn;
	$i = 1;
	$asd = $conn->query($sql);
	if ($asd->rowCount() > 0) {
		while($row = $asd->fetch(PDO::FETCH_ASSOC)) {
			if($row["$rowname"] = $i) {
				$i++;
			}
		}
	}
	return $i;
}

function getIP()
{
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
              $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
              $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP))
    {
        $ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP))
    {
        $ip = $forward;
    }
    else
    {
        $ip = $remote;
    }

    return $ip;
}

function ordinal($number) {
    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
    if ((($number % 100) >= 11) && (($number%100) <= 13))
        return $number. 'th';
    else
        return $number. $ends[$number % 10];
}

?>
