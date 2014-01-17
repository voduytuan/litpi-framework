<?php

namespace Vendor\Litpi;

/**
* Class dung de check de xem string co phai la string do user go tuy tien
*
* Cac string xem set la badstring: ......, aaaaaaaaaa, bbbbbbbb, cccccccccccc, asdf,
* asdfasdlfk, kljlkjkl, werqewrsdf, asdfasdf...
*/
class BadStringChecker
{
    /**
    * Kiem tra string co phai la bad hay khong
    *
    * @param mixed $string
    * @return boolean $result: true neu la badstring
    */
    public static function isbad($s)
    {
        $result = false;

        //strip html
        $s = strip_tags($s);

        //strip whitespace character: space, tab, newline...
        $s = preg_replace('/\s+/m', ' ', $s);

        //same character sequence detect
        if (preg_match('/(.)\1{4,}/', $s)) {
            $result = true;
        } elseif (self::meaninglessConsonants($s)) {
            $result = true;
        } elseif (self::meaninglessVowels($s)) {
            $result = true;
        } else {
            //detect fix pattern like asdf..
            $pattern = array('asdf', 'asf', 'jkl', 'ajk', 'download', 'http', '..........',
                '@yahoo', '@gmail', 'muon mua', 'muon xem', 'mua o dau', 'xem o dau', 'muốn mua', 'muốn xem');
            for ($i = 0; $i < count($pattern); $i++) {
                if (strpos($s, $pattern[$i]) !== false) {
                    $i = count($pattern);
                    $result = true;
                }
            }
        }

        return $result;
    }

    public static function meaninglessConsonants($input)
    {
        preg_match_all('/[bcdfghkjlmnpqrstvwxyz]{4,}/i', $input, $output);

        return (sizeof($output) && sizeof($output[0]))?$output[0]:false;
    }

    public static function meaninglessVowels($s)
    {
        //remove white space
        $s = str_replace(' ', '', $s);

        return preg_match('/([aeiou])\1{3,}/m', $s);
    }
}
