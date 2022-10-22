<?php

# Original source: https://github.com/bbars/utils/tree/master/php-base32-encode-decode
# usage: include 'totp.php'; echo "Token:". (new Totp())->GenerateToken(base32_decode($key)) ."\n";

    function base32_encode($data)
    {
        $CHARS = 'abcdefghijklmnopqrstuvwxyz234567';
        $dataSize = strlen($data);
        $res = '';
        $remainder = 0;
        $remainderSize = 0;

        for ($i = 0; $i < $dataSize; $i++) {
            $b = ord($data[$i]);
            $remainder = ($remainder << 8) | $b;
            $remainderSize += 8;
            while ($remainderSize > 4) {
                $remainderSize -= 5;
                $c = $remainder & (31 << $remainderSize);
                $c >>= $remainderSize;
                $res .= $CHARS[$c];
            }
        }
        if ($remainderSize > 0) {
            // remainderSize < 5:
            $remainder <<= (5 - $remainderSize);
            $c = $remainder & 31;
            $res .= $CHARS[$c];
        }

        return $res;
    }

    function base32_decode($data)
    {
        $CHARS = 'abcdefghijklmnopqrstuvwxyz234567';
        $data = strtolower($data);
        $dataSize = strlen($data);
        $buf = 0;
        $bufSize = 0;
        $res = '';

        for ($i = 0; $i < $dataSize; $i++) {
            $c = $data[$i];
            $b = strpos($CHARS, $c);
            if ($b === false) {
                throw new \Exception('Encoded string is invalid, it contains unknown char #'.ord($c));
            }
            $buf = ($buf << 5) | $b;
            $bufSize += 5;
            if ($bufSize > 7) {
                $bufSize -= 8;
                $b = ($buf & (0xff << $bufSize)) >> $bufSize;
                $res .= chr($b);
            }
        }

        return $res;
    }

class Hotp
{
    protected $algo;

    public function __construct($algo = 'sha1')
    {
        $this->algo = $algo;
    }

    public function GenerateToken($key, $count = 0, $length = 6)
    {
        $count = $this->packCounter($count);
        $hash = hash_hmac($this->algo, $count, $key);
        $code = $this->genHTOPValue($hash, $length);

        $code = str_pad($code, $length, "0", STR_PAD_LEFT);
        $code = substr($code, (-1 * $length));

        return $code;
    }

    private function packCounter($counter)
    {
        // the counter value can be more than one byte long,
        // so we need to pack it down properly.
        $cur_counter = array(0, 0, 0, 0, 0, 0, 0, 0);
        for ($i = 7; $i >= 0; $i--) {
            $cur_counter[$i] = pack('C*', $counter);
            $counter = $counter >> 8;
        }

        $bin_counter = implode($cur_counter);

        // Pad to 8 chars
        if (strlen($bin_counter) < 8) {
            $bin_counter = str_repeat(chr(0), 8 - strlen($bin_counter)) . $bin_counter;
        }

        return $bin_counter;
    }

    private function genHTOPValue($hash, $length)
    {
        // store calculate decimal
        $hmac_result = [];

        // Convert to decimal
        foreach (str_split($hash, 2) as $hex) {
            $hmac_result[] = hexdec($hex);
        }

        $offset = (int)$hmac_result[count($hmac_result)-1] & 0xf;

        $code = (int)($hmac_result[$offset] & 0x7f) << 24
            | ($hmac_result[$offset+1] & 0xff) << 16
            | ($hmac_result[$offset+2] & 0xff) << 8
            | ($hmac_result[$offset+3] & 0xff);

        return $code % pow(10, $length);
    }

    public static function GenerateSecret($length = 16)
    {
        if ($length % 8 != 0) {
            throw new \Exception("Length must be a multiple of 8");
        }

        $secret = openssl_random_pseudo_bytes($length, $strong);
        if (!$strong) {
            throw new \Exception("Random string generation was not strong");
        }

        return $secret;
    }
}

class Totp extends Hotp
{
    private $startTime;
    private $timeInterval;

    public function __construct($algo = 'sha1', $start = 0, $ti = 30)
    {
        parent::__construct($algo);
        $this->startTime = $start;
        $this->timeInterval = $ti;
    }

    public function GenerateToken($key, $time = null, $length = 6)
    {
        // Pad the key if necessary
        if ($this->algo === 'sha256') {
            $key = $key . substr($key, 0, 12);
        } elseif ($this->algo === 'sha512') {
            $key = $key . $key . $key . substr($key, 0, 4);
        }

        // Get the current unix timestamp if one isn't given
        if (is_null($time)) {
            $time = (new \DateTime())->getTimestamp();
        }

        // Calculate the count
        $now = $time - $this->startTime;
        $count = floor($now / $this->timeInterval);

        // Generate a normal HOTP token
        return parent::GenerateToken($key, $count, $length);
    }
}

?>
