<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);


$handle = fopen('./results/test.csv', 'w+');
fputcsv($handle, ['json', 'serialize'], "\t");

echo "Progress :         ";  // 7 characters of padding at the end
$steps = 1000;
for ($i = 0; $i < $steps; $i++) {

// Make a big, honkin test array
// You may need to adjust this depth to avoid memory limit errors
    $testObj = new Test();

// Time json encoding
    $start = microtime(true);
    json_encode($testObj);
    $jsonTime = microtime(true) - $start;
//    printf("JSON encoded in %01.6f seconds\n", $jsonTime);

// Time serialization
    $start = microtime(true);
    serialize($testObj);
    $serializeTime = microtime(true) - $start;
//    printf("PHP serialized in %01.6f seconds\n", $serializeTime);

// Compare them
//    if ($jsonTime < $serializeTime) {
//        printf("json_encode() was roughly %01.2f%% faster than serialize()\n", ($serializeTime / $jsonTime - 1) * 100);
//    } else if ($serializeTime < $jsonTime) {
//        printf("serialize() was roughly %01.2f%% faster than json_encode()\n", ($jsonTime / $serializeTime - 1) * 100);
//    } else {
//        echo "Impossible!\n";
//    }


    fputcsv($handle, [$jsonTime, $serializeTime], "\t");
    echo "\033[7D";      // Move 5 characters backward

    echo str_pad(round(($i + 1) / $steps * 100, 2) . " %", 7, ' ', STR_PAD_RIGHT);

}
echo("\r\n");
fclose($handle);

class Test
{
    var $name = '';
    var $phone = '';
    var $array = [];

    public function __construct()
    {
        $this->name = $this->generateRandomString();
        $this->phone = $this->generateRandomString();
        $this->array = $this->fillArray(1, 5);
        for ($i = 0; $i < rand(1, 10); $i++) {
            $this->{'p' . $i} = $this->generateRandomString(5);
        }
    }

    function fillArray($depth, $max)
    {
        static $seed;
        if (is_null($seed)) {
            $seed = [
                $this->generateRandomString(3),
                rand(0, 10),
                $this->generateRandomString(3),
                rand(0, 10),
                $this->generateRandomString(3),
                rand(0, 10),
                $this->generateRandomString(3),
                rand(0, 10),
                $this->generateRandomString(3),
                rand(0, 10),
            ];
        }
        if ($depth < $max) {
            $node = [];
            foreach ($seed as $key) {
                $node[$key] = $this->fillArray($depth + 1, $max);
            }

            return $node;
        }

        return $this->generateRandomString(5);
    }

    function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}