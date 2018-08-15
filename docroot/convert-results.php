<html>
<?php
// Endpoint from form input
$endpoint = $_POST["endpoint"];
// API key
$api_key = 'dc3ddd37-a70d-4d35-9e3d-f13adcef4025';
// Event term ID for entire table
$event_term_id = $_POST["term_tid"];

$dom = new DOMDocument;
$form_html = $_POST['table-html'];
$dom->loadHTML($form_html);

// If table has <tbody> only get rows in that element to avoid grabbing <th><tr>
if ($dom->getElementsByTagName('tbody')->length > 0) {
    $rows = $dom->getElementsByTagName('tbody')->item(0)->childNodes;
}
else {
    $rows = $dom->getElementsByTagName('tr');
}
$node_array = array();
foreach ($rows as $key => $row) {
    // Only look for <td>'s. If <th>'s exist, returns an empty result.
    $tds = $row->getElementsByTagName('td');
    // Filter empty results (<th>'s could cause empty results).
    if ($tds->length > 0) {
        $td1 = explode(' ', trim( preg_replace("/\s+/", " ", $tds->item(0)->nodeValue) ) );
        $td2 = trim( preg_replace("/\s+/", " ", $tds->item(1)->nodeValue) );

        // Build Drupal node data structure from form inputs
        $node_array[$key] = new stdClass;
        $node_array[$key]->title = $td1[0]; // attribute name from HTML string
        $node_array[$key]->type = 'attribute_definition';
        $node_array[$key]->field_events->und[0] = array ('tid'=>$event_term_id); // event term ID from form input
        $node_array[$key]->field_short_description->und[0]["value"] = $td2; // description from HTML string

        $node_json = json_encode($node_array[$key]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        // Set header in order to submit JSON data
        curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: application/json')); 
        curl_setopt($ch, CURLOPT_URL, $endpoint.'?api-key='.$api_key);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        //prepare the field values being posted to the JSON service (WITH key authentication)
        $data = $node_json;
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        //make the request
        $result = curl_exec($ch);
        // Then, after your curl_exec call:
        $header = substr($result, 0, $header_size);
        $body = substr($result, $header_size);

        if (curl_errno($ch)) {
            // this would be your first hint that something went wrong
            die('Couldn\'t send request: ' . curl_error($ch));
        } else {
            // check the HTTP status code of the request
            $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($resultStatus == 200) {
                echo '<p>'.$resultStatus.': Request successful</p>';
                $body_parsed = json_decode($body);
                echo '<p><a href="http://newrelic.dev.dd:8083/node/'.$body_parsed->nid.'">'.$body_parsed->nid.'</a></p>';
            } else {
                // the request did not complete as expected. common errors are 4xx
                // (not found, bad request, etc.) and 5xx (usually concerning
                // errors/exceptions in the remote script execution)

                die('Request failed: HTTP status code: ' . $resultStatus.': '.$body);
            }
        }
        curl_close($curl);
    }
}
echo '<p><b>'.count($node_array).' nodes inserted.</b></p>';
?>
</html>