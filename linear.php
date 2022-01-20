<?php
function fetch ($query) {
	$ch = curl_init();
	$query = urlencode($query);
	$data = "{\"requests\":[{ \"indexName\":\"docs\", " .
		"\"params\":\"hitsPerPage=10&attributesToRetrieve=%5B%22title%22%2C%22slug%22%5D&query=$query\"}]}";

	curl_setopt_array($ch, [
		CURLOPT_POST => 1,
		CURLOPT_URL => 'https://9rxbcyq6dv-dsn.algolia.net/1/indexes/*/queries',
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_POSTFIELDS => $data,
		CURLOPT_HTTPHEADER => [
			'Content-Type: application/json',
			'X-Algolia-Api-Key: fa1104304225b717fcff6b2a55b776d7',
			'X-Algolia-Application-Id: 9RXBCYQ6DV',
		],
		CURLOPT_RETURNTRANSFER => true,      // return web page
		CURLOPT_HEADER         => false,      // return headers
		CURLOPT_FOLLOWLOCATION => true,      // follow redirects
		CURLOPT_USERAGENT      => "Linear.app",
		CURLOPT_AUTOREFERER    => true,      // set referer on redirect
		CURLOPT_CONNECTTIMEOUT => 60,        // timeout on connect
		CURLOPT_TIMEOUT        => 60,        // timeout on response
		CURLOPT_MAXREDIRS      => 10         // stop after 10 redirects
	]);
	$res = curl_exec($ch);
	if (curl_errno($ch)) echo 'Error:' . curl_error($ch);
	curl_close($ch);

	$json = json_decode($res, true);
	if (!empty($json)) $json = $json['results'][0]['hits'];
	$json = array_map(function ($item) {
		$snippet = strip_tags($item['_snippetResult']['content']['value']);
		$snippet = preg_replace('/\s+/', ' ', $snippet);
		return [
			'title' => $item['title'],
			'subtitle' => $snippet,
			'arg' => 'https://linear.app/docs/' . $item['slug'],
		];
	}, $json);
	return json_encode([ 'items' => $json ]);
}

echo fetch('sdd');
