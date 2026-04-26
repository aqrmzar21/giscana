<?php
$data = json_decode(file_get_contents(__DIR__.'/../public/geojson/kawasan-pesisir.geojson'), true);
print_r(array_keys($data['features'][0]['properties']));
