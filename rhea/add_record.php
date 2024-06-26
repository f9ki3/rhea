<?php
// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the JSON data sent from the client
    $json_data = file_get_contents('php://input');
    
    // Decode the JSON data into a PHP associative array
    $data = json_decode($json_data, true);

    // Extract data from the array
    $ownerName = $data['ownerName'];
    $totalNumber = $data['totalNumber'];
    $categoryValues = $data['categoryValues'];

    // Define the file path to save the XML file
    $xmlFilePath = 'rhea.xml';

    // Check if the XML file already exists
    $xml = new DOMDocument();
    if (file_exists($xmlFilePath)) {
        $xml->load($xmlFilePath); // Load existing XML file
        $root = $xml->documentElement; // Get the root element
    } else {
        // Create a new XML document if file doesn't exist
        $xml->formatOutput = true; // Enable XML formatting for readability
        $root = $xml->createElement('basket_records'); // Create root element
        $xml->appendChild($root);
    }

    // Create a new record element
    $recordElement = $xml->createElement('basket_record');

    // Generate a unique ID for the record
    $recordId = uniqid(); // Generate a unique ID
    $recordElement->setAttribute('id', $recordId); // Set ID attribute

    // Add ownerName and totalNumber as attributes of the record element
    $recordElement->setAttribute('ownerName', $ownerName);
    $recordElement->setAttribute('totalNumber', $totalNumber);

    // Loop through categoryValues to add child elements
    foreach ($categoryValues as $category) {
        $categoryElement = $xml->createElement('category');
        $categoryElement->setAttribute('value', $category['value']);
        $recordElement->appendChild($categoryElement);
    }

    // Append the new record to the root element
    $root->appendChild($recordElement);

    // Save the updated XML document to the specified file path
    $xml->save($xmlFilePath);

    // Respond with a success message including the generated ID
    http_response_code(200);
    echo json_encode(array('message' => 'New record added successfully.', 'id' => $recordId));
} else {
    // Respond with an error if the request method is not POST
    http_response_code(405); // Method Not Allowed
    echo json_encode(array('message' => 'Method not allowed.'));
}
?>
