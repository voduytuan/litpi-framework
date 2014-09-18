<?php

namespace Vendor\Litpi;

class Helper
{
    /**
     * Convert normal url text to hyperlink
     *
     * @param  string $input
     * @return string
     */
    public static function autoLink($input)
    {
        $output = preg_replace("/(http|https|ftp)://([a-z0-9\-\./]+))/", "<a href=\"\\0\">\\0</a>", $input);
        $output = preg_replace(
            "/(([a-z0-9\-\.]+)@([a-z0-9\-\.]+)\.([a-z0-9]+))/",
            "<a href=\"mailto:\\0\">\\0</a>",
            $output
        );

        return $output;
    }

    public static function refineUrl($input)
    {
        $input = strtolower(trim(strip_tags($input)));

        if ($input != '' && preg_match('/(http|ftp|https):\/\/.*/', $input) == 0) {
            $input = 'http://' . $input;
        }

        return $input;
    }

    /**
     * Generate a random number between floor and ceiling
     *
     * @param  int $floor
     * @param  int $ceiling
     * @return int
     */
    public static function randomNumber($floor, $ceiling)
    {
        srand((double) microtime() * 1000000);

        return rand($floor, $ceiling);
    }

    /**
     * Format string of filesize
     *
     * @param  string $s
     * @return string
     */
    public static function formatFileSize($s)
    {
        if ($s >= "1073741824") {
            $s = number_format($s / 1073741824, 2) . " GB";
        } elseif ($s >= "1048576") {
            $s  = number_format($s / 1048576, 2) . " MB";
        } elseif ($s >= "1024") {
            $s = number_format($s / 1024, 2) . " KB";
        } elseif ($s >= "1") {
            $s = $s . " bytes";
        } else {
            $s = "-";
        }

        return $s;

    }

    /**
     * return file extension
     *
     * @param  string $filename
     * @return string
     */
    public static function fileExtension($filename)
    {
        return strtolower(trim(substr(strrchr($filename, '.'), 1)));
    }

    public static function validateEmail($email)
    {
        return preg_match('/^[\w.-]+@([\w.-]+\.)+[a-z]{2,6}$/is', $email);
    }

    public static function getSessionId()
    {
        if (isset($_SESSION['s_id'])) {
            return $_SESSION['s_id'];
        } else {
            $s_id = session_id();
            $_SESSION['s_id'] = $s_id;

            return $s_id;
        }

    }

    /**
    * Ham dung de convert cac ky tu co dau thanh khong dau
    * Dung tot cho cac chuc nang SEO cho browser(vi nhieu engine ko
    * hieu duoc dau tieng viet, nen can phai bo dau tieng viet di)
    *
    * @param mixed $string
    */
    public static function codau2khongdau($string = '', $alphabetOnly = false, $tolower = true)
    {

        $output =  $string;
        if ($output != '') {
            //Tien hanh xu ly bo dau o day
            $search = array(
                '&#225;', '&#224;', '&#7843;', '&#227;', '&#7841;', 				// a' a` a? a~ a.
                '&#259;', '&#7855;', '&#7857;', '&#7859;', '&#7861;', '&#7863;',	// a( a('
                '&#226;', '&#7845;', '&#7847;', '&#7849;', '&#7851;', '&#7853;', 	// a^ a^'..
                '&#273;',											   			// d-
                '&#233;', '&#232;', '&#7867;', '&#7869;', '&#7865;',				// e' e`..
                '&#234;', '&#7871;', '&#7873;', '&#7875;', '&#7877;', '&#7879;',	// e^ e^'
                '&#237;', '&#236;', '&#7881;', '&#297;', '&#7883;',					// i' i`..
                '&#243;', '&#242;', '&#7887;', '&#245;', '&#7885;',					// o' o`..
                '&#244;', '&#7889;', '&#7891;', '&#7893;', '&#7895;', '&#7897;',	// o^ o^'..
                '&#417;', '&#7899;', '&#7901;', '&#7903;', '&#7905;', '&#7907;',	// o* o*'..
                '&#250;', '&#249;', '&#7911;', '&#361;', '&#7909;',					// u'..
                '&#432;', '&#7913;', '&#7915;', '&#7917;', '&#7919;', '&#7921;',	// u* u*'..
                '&#253;', '&#7923;', '&#7927;', '&#7929;', '&#7925;',				// y' y`..

                '&#193;', '&#192;', '&#7842;', '&#195;', '&#7840;',					// A' A` A? A~ A.
                '&#258;', '&#7854;', '&#7856;', '&#7858;', '&#7860;', '&#7862;',	// A( A('..
                '&#194;', '&#7844;', '&#7846;', '&#7848;', '&#7850;', '&#7852;',	// A^ A^'..
                '&#272;',															// D-
                '&#201;', '&#200;', '&#7866;', '&#7868;', '&#7864;',				// E' E`..
                '&#202;', '&#7870;', '&#7872;', '&#7874;', '&#7876;', '&#7878;',	// E^ E^'..
                '&#205;', '&#204;', '&#7880;', '&#296;', '&#7882;',					// I' I`..
                '&#211;', '&#210;', '&#7886;', '&#213;', '&#7884;',					// O' O`..
                '&#212;', '&#7888;', '&#7890;', '&#7892;', '&#7894;', '&#7896;',	// O^ O^'..
                '&#416;', '&#7898;', '&#7900;', '&#7902;', '&#7904;', '&#7906;',	// O* O*'..
                '&#218;', '&#217;', '&#7910;', '&#360;', '&#7908;',					// U' U`..
                '&#431;', '&#7912;', '&#7914;', '&#7916;', '&#7918;', '&#7920;',	// U* U*'..
                '&#221;', '&#7922;', '&#7926;', '&#7928;', '&#7924;'				// Y' Y`..
            );

            $search2 = array(
                'á', 'à', 'ả', 'ã', 'ạ', 				// a' a` a? a~ a.
                'ă', 'ắ', 'ằ', 'ẳ', 'ẵ', 'ặ',	// a( a('
                'â', 'ấ', 'ầ', 'ẩ', 'ẫ', 'ậ', 	// a^ a^'..
                'đ',											   			// d-
                'é', 'è', 'ẻ', 'ẽ', 'ẹ',				// e' e`..
                'ê', 'ế', 'ề', 'ể', 'ễ', 'ệ',	// e^ e^'
                'í', 'ì', 'ỉ', 'ĩ', 'ị',					// i' i`..
                'ó', 'ò', 'ỏ', 'õ', 'ọ',					// o' o`..
                'ô', 'ố', 'ồ', 'ổ', 'ỗ', 'ộ',	// o^ o^'..
                'ơ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ',	// o* o*'..
                'ú', 'ù', 'ủ', 'ũ', 'ụ',					// u'..
                'ư', 'ứ', 'ừ', 'ử', 'ữ', 'ự',	// u* u*'..
                'ý', 'ỳ', 'ỷ', 'ỹ', 'ỵ',				// y' y`..

                'Á', 'À', 'Ả', 'Ã', 'Ạ',					// A' A` A? A~ A.
                'Ă', 'Ắ', 'Ằ', 'Ẳ', 'Ẵ', 'Ặ',	// A( A('..
                'Â', 'Ấ', 'Ầ', 'Ẩ', 'Ẫ', 'Ậ',	// A^ A^'..
                'Đ',															// D-
                'É', 'È', 'Ẻ', 'Ẽ', 'Ẹ',				// E' E`..
                'Ê', 'Ế', 'Ề', 'Ể', 'Ễ', 'Ệ',	// E^ E^'..
                'Í', 'Ì', 'Ỉ', 'Ĩ', 'Ị',					// I' I`..
                'Ó', 'Ò', 'Ỏ', 'Õ', 'Ọ',					// O' O`..
                'Ô', 'Ố', 'Ồ', 'Ổ', 'Ỗ', 'Ộ',	// O^ O^'..
                'Ơ', 'Ớ', 'Ờ', 'Ở', 'Ỡ', 'Ợ',	// O* O*'..
                'Ú', 'Ù', 'Ủ', 'Ũ', 'Ụ',					// U' U`..
                'Ư', 'Ứ', 'Ừ', 'Ử', 'Ữ', 'Ự',	// U* U*'..
                'Ý', 'Ỳ', 'Ỷ', 'Ỹ', 'Ỵ'				// Y' Y`..
            );

            $replace = array(
                'a', 'a', 'a', 'a', 'a',
                'a', 'a', 'a', 'a', 'a', 'a',
                'a', 'a', 'a', 'a', 'a', 'a',
                'd',
                'e', 'e', 'e', 'e', 'e',
                'e', 'e', 'e', 'e', 'e', 'e',
                'i', 'i', 'i', 'i', 'i',
                'o', 'o', 'o', 'o', 'o',
                'o', 'o', 'o', 'o', 'o', 'o',
                'o', 'o', 'o', 'o', 'o', 'o',
                'u', 'u', 'u', 'u', 'u',
                'u', 'u', 'u', 'u', 'u', 'u',
                'y', 'y', 'y', 'y', 'y',

                'A', 'A', 'A', 'A', 'A',
                'A', 'A', 'A', 'A', 'A', 'A',
                'A', 'A', 'A', 'A', 'A', 'A',
                'D',
                'E', 'E', 'E', 'E', 'E',
                'E', 'E', 'E', 'E', 'E', 'E',
                'I', 'I', 'I', 'I', 'I',
                'O', 'O', 'O', 'O', 'O',
                'O', 'O', 'O', 'O', 'O', 'O',
                'O', 'O', 'O', 'O', 'O', 'O',
                'U', 'U', 'U', 'U', 'U',
                'U', 'U', 'U', 'U', 'U', 'U',
                'Y', 'Y', 'Y', 'Y', 'Y'
            );

            //print_r($search);
            $output = str_replace($search, $replace, $output);
            $output = str_replace($search2, $replace, $output);

            if ($alphabetOnly) {
                $output = self::alphabetonly($output);
            }

            if ($tolower) {
                $output = strtolower($output);
            }
        }

        return $output;
    }

    public static function specialchar2normalchar($string = '', $alphabetOnly = false, $tolower = true)
    {
        $output =  $string;
        if ($output != '') {
            //Tien hanh xu ly bo dau o day
            $search = array(
                '&#225;', '&#224;', '&#7843;', '&#227;', '&#7841;',                 // a' a` a? a~ a.
                '&#259;', '&#7855;', '&#7857;', '&#7859;', '&#7861;', '&#7863;',    // a( a('
                '&#226;', '&#7845;', '&#7847;', '&#7849;', '&#7851;', '&#7853;',     // a^ a^'..
                '&#273;',                                                           // d-
                '&#233;', '&#232;', '&#7867;', '&#7869;', '&#7865;',                // e' e`..
                '&#234;', '&#7871;', '&#7873;', '&#7875;', '&#7877;', '&#7879;',    // e^ e^'
                '&#237;', '&#236;', '&#7881;', '&#297;', '&#7883;',                    // i' i`..
                '&#243;', '&#242;', '&#7887;', '&#245;', '&#7885;',                    // o' o`..
                '&#244;', '&#7889;', '&#7891;', '&#7893;', '&#7895;', '&#7897;',    // o^ o^'..
                '&#417;', '&#7899;', '&#7901;', '&#7903;', '&#7905;', '&#7907;',    // o* o*'..
                '&#250;', '&#249;', '&#7911;', '&#361;', '&#7909;',                    // u'..
                '&#432;', '&#7913;', '&#7915;', '&#7917;', '&#7919;', '&#7921;',    // u* u*'..
                '&#253;', '&#7923;', '&#7927;', '&#7929;', '&#7925;',                // y' y`..

                '&#193;', '&#192;', '&#7842;', '&#195;', '&#7840;',                    // A' A` A? A~ A.
                '&#258;', '&#7854;', '&#7856;', '&#7858;', '&#7860;', '&#7862;',    // A( A('..
                '&#194;', '&#7844;', '&#7846;', '&#7848;', '&#7850;', '&#7852;',    // A^ A^'..
                '&#272;',                                                            // D-
                '&#201;', '&#200;', '&#7866;', '&#7868;', '&#7864;',                // E' E`..
                '&#202;', '&#7870;', '&#7872;', '&#7874;', '&#7876;', '&#7878;',    // E^ E^'..
                '&#205;', '&#204;', '&#7880;', '&#296;', '&#7882;',                    // I' I`..
                '&#211;', '&#210;', '&#7886;', '&#213;', '&#7884;',                    // O' O`..
                '&#212;', '&#7888;', '&#7890;', '&#7892;', '&#7894;', '&#7896;',    // O^ O^'..
                '&#416;', '&#7898;', '&#7900;', '&#7902;', '&#7904;', '&#7906;',    // O* O*'..
                '&#218;', '&#217;', '&#7910;', '&#360;', '&#7908;',                    // U' U`..
                '&#431;', '&#7912;', '&#7914;', '&#7916;', '&#7918;', '&#7920;',    // U* U*'..
                '&#221;', '&#7922;', '&#7926;', '&#7928;', '&#7924;'                // Y' Y`..
            );

            $replace = array(
                'á', 'à', 'ả', 'ã', 'ạ',                 // a' a` a? a~ a.
                'ă', 'ắ', 'ằ', 'ẳ', 'ẵ', 'ặ',    // a( a('
                'â', 'ấ', 'ầ', 'ẩ', 'ẫ', 'ậ',     // a^ a^'..
                'đ',                                                           // d-
                'é', 'è', 'ẻ', 'ẽ', 'ẹ',                // e' e`..
                'ê', 'ế', 'ề', 'ể', 'ễ', 'ệ',    // e^ e^'
                'í', 'ì', 'ỉ', 'ĩ', 'ị',                    // i' i`..
                'ó', 'ò', 'ỏ', 'õ', 'ọ',                    // o' o`..
                'ô', 'ố', 'ồ', 'ổ', 'ỗ', 'ộ',    // o^ o^'..
                'ơ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ',    // o* o*'..
                'ú', 'ù', 'ủ', 'ũ', 'ụ',                    // u'..
                'ư', 'ứ', 'ừ', 'ử', 'ữ', 'ự',    // u* u*'..
                'ý', 'ỳ', 'ỷ', 'ỹ', 'ỵ',                // y' y`..

                'Á', 'À', 'Ả', 'Ã', 'Ạ',                    // A' A` A? A~ A.
                'Ă', 'Ắ', 'Ằ', 'Ẳ', 'Ẵ', 'Ặ',    // A( A('..
                'Â', 'Ấ', 'Ầ', 'Ẩ', 'Ẫ', 'Ậ',    // A^ A^'..
                'Đ',                                                            // D-
                'É', 'È', 'Ẻ', 'Ẽ', 'Ẹ',                // E' E`..
                'Ê', 'Ế', 'Ề', 'Ể', 'Ễ', 'Ệ',    // E^ E^'..
                'Í', 'Ì', 'Ỉ', 'Ĩ', 'Ị',                    // I' I`..
                'Ó', 'Ò', 'Ỏ', 'Õ', 'Ọ',                    // O' O`..
                'Ô', 'Ố', 'Ồ', 'Ổ', 'Ỗ', 'Ộ',    // O^ O^'..
                'Ơ', 'Ớ', 'Ờ', 'Ở', 'Ỡ', 'Ợ',    // O* O*'..
                'Ú', 'Ù', 'Ủ', 'Ũ', 'Ụ',                    // U' U`..
                'Ư', 'Ứ', 'Ừ', 'Ử', 'Ữ', 'Ự',    // U* U*'..
                'Ý', 'Ỳ', 'Ỷ', 'Ỹ', 'Ỵ'                // Y' Y`..
            );

            //print_r($search);
            $output = str_replace($search, $replace, $output);
        }

        return $output;
    }

    public static function alphabetonly($string = '')
    {
        $output = $string;
        //replace no alphabet character
        $output = preg_replace("/[^a-zA-Z0-9]/", "-", $output);
        $output = preg_replace("/-+/", "-", $output);
        $output = trim($output, '-');

        return $output;
    }

    public static function getIpAddress($convertToInteger = false)
    {

        $ip = '';

        if ($_SERVER) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $ip = getenv('HTTP_X_FORWARDED_FOR');
            } elseif (getenv('HTTP_CLIENT_IP')) {
                $ip = getenv('HTTP_CLIENT_IP');
            } else {
                $ip = getenv('remote_addr');
            }
        }

        //Convert IP string to Integer
        //Example, IP: 127.0.0.1 --> 2130706433
        if ($convertToInteger) {
            $ip = ip2long($ip);
        }

        return (int) $ip;
    }

    /**
    * Ham dung de strip slash tu 1 chuoi
    *  - neu chuoi do' duoc submit va da duoc them slash(do config: magic_quotes_gpc) thi tien hanh strip slash
    * - nguoc lai, return chuoi
    *
    * @param string $string
    * @return string
    */
    public static function mystripslashes($string)
    {
        if (get_magic_quotes_gpc()) {
            return stripslashes($string);
        } else {
            return $string;
        }
    }




    public static function getLangContent($langPath = '', $module_name = '')
    {
        $lang_content = array();
        $langFile = $langPath . $module_name . '.xml';
        if (file_exists($langFile)) {
            $xml = new \SimpleXMLElement($langFile, null, true);
            foreach ($xml->lines as $line) {
                $lang_content["{$line->attributes()->name}"] = (string) $line;
            }
        }

        return $lang_content;
    }




    public static function getCurrentDateDirName($includeDay = true)
    {
        $dateArr = getdate();

        if ($includeDay) {
            $path = $dateArr['year'] . '/' . $dateArr['month'] . '/' . $dateArr['mday'] . '/';
        } else {
            $path = $dateArr['year'] . '/' . $dateArr['month'] . '/';
        }

        return $path;
    }

    /**
    * Convert date string in format 'dd/mm/yyyy' and time string in format 'hh:mm'to timestamp
    * @param string $datestring
    * @param string $timestring
    */
    public static function strtotimedmy($datestring = '01/01/1970', $timestring = '00:01')
    {
        $timegroup = explode(':', $timestring);
        $dategroup = explode('/', $datestring);

        return mktime(
            (int) trim($timegroup[0]),
            (int) trim($timegroup[1]),
            1,
            (int) trim($dategroup[1]),
            (int) trim($dategroup[0]),
            (int) trim($dategroup[2])
        );
    }


    public static function truncate($phrase, $max_words)
    {
        $phrase_array = explode(' ', $phrase);
        if (count($phrase_array) > $max_words && $max_words > 0) {
            $phrase = implode(' ', array_slice($phrase_array, 0, $max_words)).'...';
        }

        return $phrase;
    }

    public static function refineMoneyString($moneyString = '')
    {
        $money = preg_replace('/[^0-9]/i', '', $moneyString);

        return (float) $money;
    }

    public static function formatPrice($money)
    {
        return number_format($money, 0, '.', ',');
    }

    /**
    * Ham kiem tra coockie co duoc enable trong trinh duyet khong
    *
    * Neu khong enable thi se anh huong toi 1 so chuc nang lien quan toi SESSION
    * cho nen can kiem tra enable coockie thi moi pass mot so chuc nang lien quan toi counting trong session
    * nhu increase view, add comment...
    *
    */

    public static function checkCookieEnable($cookieSample = 'SHASH')
    {
        return isset($_COOKIE[$cookieSample]);
    }


    /**
    * Manual css filter
    *
    * @param mixed $data
    * @return mixed
    */
    public static function xssClean($data)
    {

        include_once('./libs/htmlpurifier/HTMLPurifier.standalone.php');

        $config = \HTMLPurifier_Config::createDefault();
        $config->set('Cache.SerializerPath', SITE_PATH . 'templates/_core/templates_c');
        $purifier = new \HTMLPurifier($config);

        $cleanData = $purifier->purify($data);

        return $cleanData;
    }


    /**
    * Tao token cho cac thao tac add, edit, delete entry,quiz,comment,user...
    * de chong lai tan cong CSRF
    *
    */
    public static function getSecurityToken()
    {
        return md5(self::randomNumber(1, 1000) . session_id());
    }

    /**
    * Ham dung de test general security token
    *
    * duoc tao trong $_SESSION['securityToken'] va duoc truyen vao bang $_GET['token']
    *
    */
    public static function checkSecurityToken()
    {
        return $_GET['token'] == $_SESSION['securityToken'];
    }

    /**
    * Ham replace cac ky tu dash thua (double dash --> single dash, remove first and last dash in url)
    *
    * @param mixed $url
    */
    public static function refineDashInUrl($url)
    {
        $url = preg_replace('/[-]+/', '-', $url);
        if ($url[0] == '-') {
            $url = substr($url, 1);
        }

        if ($url[strlen($url)-1] == '-') {
            $url = substr($url, 0, strlen($url)-1);
        }

        return $url;
    }

    /**
    * Download external file using cURL
    *
    * @param string $img : URL of external file
    * @param string $fullpath : local filepath
    * @param string $type: type of external file.
    */
    public static function saveExternalFile($img, $fullpath, $type = 'image', $isUseCurl = true)
    {

        if ($isUseCurl) {
            //$fullpath = urlencode($fullpath);
            $ch = curl_init($img);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            $rawdata=curl_exec($ch);
            curl_close($ch);

            //check if return error (include html in output)
            if (strpos($rawdata, 'html') === false) {
                $fp = fopen($fullpath, 'w');

                if (!$fp) {
                    return false;
                } elseif (!empty($rawdata)) {
                    fwrite($fp, $rawdata);
                    fclose($fp);

                    return true;
                }
            } else {
                return false;
            }
        } else {
            $file_headers = @get_headers($img);
            if (strpos($file_headers[0], '200') || strpos($file_headers[0], '302') || strpos($file_headers[0], '304')) {
                return file_put_contents($fullpath, file_get_contents($img));
            } else {
                return false;
            }

        }
    }


    public static function curPageURL()
    {
        $pageURL = 'http';
        if ($_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }

        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }

        $pageURL = str_replace('?live', '?', $pageURL);
        $pageURL = str_replace('&live', '', $pageURL);

        return $pageURL;
    }

    /**
    * Ham loc chuoi screenname
    *
    * @param string $screenname
    */
    public static function refineScreenname($screenname)
    {
        $screenname = preg_replace('/[^a-z0-9.]/', '', $screenname);

        return $screenname;
    }

    public static function arrayStriptags($data = array())
    {
        foreach ($data as $k => $v) {
            if (!is_array($v)) {
                $data[$k] = trim(strip_tags($v));
            }
        }

        return $data;
    }

    /**
    * Ham them 1 querystring vao sau url,
    *
    * Neu URL da co dau ? thi khong can them dau ? ma chi can them &...
    * Neu URL chua co dau ? thi them dau ?, sau do them & va query string
    *
    * @param string $url
    * @param string $paramString
    */
    public static function urlAddParam($url, $paramString)
    {
        //neu chua co dua ?
        if (strpos($url, '?') === false) {
            $url .= '?';
        }

        return $url . '&' . $paramString;
    }

    public static function truncateperiod($string, $limit = 80, $pad = '...', $break = '.')
    {
        $string = strip_tags($string);

        // return with no change if string is shorter than $limit
        if (strlen($string) <= $limit) {
            return $string;
        }
        // is $break present between $limit and the end of the string?
        if (false !== ($breakpoint = strpos($string, $break, $limit))) {
            if ($breakpoint < strlen($string) - 1) {
                $string = substr($string, 0, $breakpoint) . $pad;
            }
        }

        return $string;
    }

    /**
    * Goi 1 async post request de khong delay cua main process
    *
    * @param mixed $url
    */
    public static function backgroundHttpPost($url, $paramString = '')
    {
        $parts=parse_url($url);

        $fp = fsockopen($parts['host'], isset($parts['port'])?$parts['port']:80, $errno, $errstr, 30);

        if (!$fp) {
            return false;
        } else {
            $out = "POST ".$parts['path']."?".$parts['query']." HTTP/1.1\r\n";
            $out.= "Host: ".$parts['host']."\r\n";
            $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
            $out.= "Content-Length: ".strlen($paramString)."\r\n";
            $out.= "Connection: Close\r\n\r\n";


            if ($paramString != '') {
                $out.= $paramString;
            }

            fwrite($fp, $out);
            fclose($fp);

            return true;
        }
    }

    /**
    * Goi 1 async get request de khong delay cua main process
    *
    * @param mixed $url
    */
    public static function backgroundHttpGet($url)
    {
        $parts=parse_url($url);

        $fp = fsockopen($parts['host'], isset($parts['port'])?$parts['port']:80, $errno, $errstr, 30);

        if (!$fp) {
            return false;
        } else {
            $out = "GET ".$parts['path']."?".$parts['query']." HTTP/1.1\r\n";
            $out.= "Host: ".$parts['host']."\r\n";
            $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
            $out.= "Connection: Close\r\n\r\n";

            fwrite($fp, $out);
            fclose($fp);

            return true;
        }
    }


    /**
    * Kiem tra neu url khong bat dau bang http://
    *
    * @param mixed $url
    */
    public static function paddingWebsitePrefix($url)
    {
        if (strpos($url, 'http') !== 0) {
            $url = 'http://' . $url;
        }

        return $url;
    }

    /**
    * Boi vi he thong goi mail khong nhan duoc tieng viet trong fullname
    * nen xu ly fullname truoc khi goi email
    *
    * @param mixed $fullname
    */
    public static function refineEmailSendername($fullname)
    {
        $fullname = ucwords(Helper::codau2khongdau($fullname));

        //trademark character will error when sending
        //so convert to htmlentity before sending
        $fullname = htmlentities($fullname);

        return $fullname;
    }


    /**
    * Loai bo ky tu khogn can thiet de chong XSS
    * Loai bo HTML tag, chi giu lai cac ky tu binh thuong, ko format
    *
    * @param mixed $s
    */
    public static function plaintext($s)
    {
        $s = strip_tags($s);
        $s = self::xssClean($s);

        return $s;
    }


    /**
     * Translates a number to a short alhanumeric version
     *
     * Translated any number up to 9007199254740992
     * to a shorter version in letters e.g.:
     * 9007199254740989 --> PpQXn7COf
     *
     * specifiying the second argument true, it will
     * translate back e.g.:
     * PpQXn7COf --> 9007199254740989
     *
     * this function is based on any2dec && dec2any by
     * fragmer[at]mail[dot]ru
     * see: http://nl3.php.net/manual/en/function.base-convert.php#52450
     *
     * If you want the alphaID to be at least 3 letter long, use the
     * $pad_up = 3 argument
     *
     * In most cases this is better than totally random ID generators
     * because this can easily avoid duplicate ID's.
     * For example if you correlate the alpha ID to an auto incrementing ID
     * in your database, you're done.
     *
     * The reverse is done because it makes it slightly more cryptic,
     * but it also makes it easier to spread lots of IDs in different
     * directories on your filesystem. Example:
     * $part1 = substr($alpha_id,0,1);
     * $part2 = substr($alpha_id,1,1);
     * $part3 = substr($alpha_id,2,strlen($alpha_id));
     * $destindir = "/".$part1."/".$part2."/".$part3;
     * // by reversing, directories are more evenly spread out. The
     * // first 26 directories already occupy 26 main levels
     *
     * more info on limitation:
     * - http://blade.nagaokaut.ac.jp/cgi-bin/scat.rb/ruby/ruby-talk/165372
     *
     * if you really need this for bigger numbers you probably have to look
     * at things like: http://theserverpages.com/php/manual/en/ref.bc.php
     * or: http://theserverpages.com/php/manual/en/ref.gmp.php
     * but I haven't really dugg into this. If you have more info on those
     * matters feel free to leave a comment.
     *
     * @author  Kevin van Zonneveld <kevin@vanzonneveld.net>
     * @author  Simon Franz
     * @author  Deadfish
     * @copyright 2008 Kevin van Zonneveld (http://kevin.vanzonneveld.net)
     * @license   http://www.opensource.org/licenses/bsd-license.php New BSD Licence
     * @version   SVN: Release: $Id: alphaID.inc.php 344 2009-06-10 17:43:59Z kevin $
     * @link    http://kevin.vanzonneveld.net/
     *
     * @param mixed   $in      String or long input to translate
     * @param boolean $to_num  Reverses translation when true
     * @param mixed   $pad_up  Number or boolean padds the result up to a specified length
     * @param string  $passKey Supplying a password makes it harder to calculate the original ID
     *
     * @return mixed string or long
     */
    public static function alphaID($in, $to_num = false, $pad_up = false, $passKey = null)
    {
        $index = "abcdefghijkmnpqrstuvwxyz123456789";
        if ($passKey !== null) {
        // Although this function's purpose is to just make the
        // ID short - and not so much secure,
        // with this patch by Simon Franz (http://blog.snaky.org/)
        // you can optionally supply a password to make it harder
        // to calculate the corresponding numeric ID

            for ($n = 0; $n<strlen($index); $n++) {
                $i[] = substr($index, $n, 1);
            }

            $passhash = hash('sha256', $passKey);
            $passhash = (strlen($passhash) < strlen($index))
            ? hash('sha512', $passKey)
            : $passhash;

            for ($n=0; $n < strlen($index); $n++) {
                $p[] = substr($passhash, $n, 1);
            }

            array_multisort($p, SORT_DESC, $i);
            $index = implode($i);
        }

        $base  = strlen($index);

        if ($to_num) {
        // Digital number  <<--  alphabet letter code
            $in  = strrev($in);
            $out = 0;
            $len = strlen($in) - 1;
            for ($t = 0; $t <= $len; $t++) {
                $pow = pow($base, $len - $t);
                $out   = $out + strpos($index, substr($in, $t, 1)) * $pow;
            }

            if (is_numeric($pad_up)) {
                $pad_up--;
                if ($pad_up > 0) {
                    $out -= pow($base, $pad_up);
                }
            }
            $out = sprintf('%F', $out);
            $out = substr($out, 0, strpos($out, '.'));
        } else {
        // Digital number  -->>  alphabet letter code
            if (is_numeric($pad_up)) {
                $pad_up--;
                if ($pad_up > 0) {
                    $in += pow($base, $pad_up);
                }
            }

            $out = "";
            for ($t = floor(log($in, $base)); $t >= 0; $t--) {
                $bcp = pow($base, $t);
                $a   = floor($in / $bcp) % $base;
                $out = $out . substr($index, $a, 1);
                $in  = $in - ($a * $bcp);
            }
            $out = strrev($out); // reverse
        }

        return $out;
    }

    public static function escapetext($input)
    {
        $output = $input;

        $output = addslashes($input);

        return $output;
    }

    public static function unspecialtext($input)
    {
        $output = $input;

        $output = preg_replace('/[~!#$%^&*;,?:\'"]/', '', $output);

        return $output;
    }


    public static function isApcEnable()
    {
        return extension_loaded('apc') && ini_get('apc.enabled');
    }

    /**
     * Return tracking ID for view, search logged for current visitor
     */
    public static function getTrackingId()
    {
        if (isset($_COOKIE['_t'])) {
            return $_COOKIE['_t'];
        } else {
            return '';
        }
    }

    public static function directoryToArray($directory, $recursive)
    {
        $array_items = array();
        if ($handle = opendir($directory)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    if (is_dir($directory. DIRECTORY_SEPARATOR . $file)) {
                        if ($recursive) {
                            $array_items = array_merge(
                                $array_items,
                                self::directoryToArray($directory. DIRECTORY_SEPARATOR . $file, $recursive)
                            );
                        }
                        $file = $directory . DIRECTORY_SEPARATOR . $file;
                        $array_items[] = preg_replace("/\/\//si", DIRECTORY_SEPARATOR, $file);
                    } else {
                        $file = $directory . DIRECTORY_SEPARATOR . $file;
                        $array_items[] = preg_replace("/\/\//si", DIRECTORY_SEPARATOR, $file);
                    }
                }
            }
            closedir($handle);
        }
        return $array_items;
    }
}
