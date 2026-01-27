<?php
/**
 * Test Thumbnail Upload Endpoint
 * 
 * This script tests the thumbnail upload endpoint with the actual mboka2020.PNG image
 */

// Configuration
$baseUrl = 'http://localhost:8000';
$imagePath = __DIR__ . '/resources/views/mboka2020.PNG';

// Step 1: Login to get token
echo "Step 1: Logging in to get authentication token...\n";
$loginData = json_encode([
    'email' => 'admin@example.com',
    'password' => 'password'
]);

$ch = curl_init("$baseUrl/api/v1/auth/login");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, $loginData);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    echo "❌ Login failed with status code: $httpCode\n";
    echo "Response: $response\n";
    exit(1);
}

$loginResponse = json_decode($response, true);
$token = $loginResponse['data']['token'];
echo "✓ Login successful\n";
echo "Token: " . substr($token, 0, 20) . "...\n\n";

// Step 2: Get first product
echo "Step 2: Getting first product...\n";
$ch = curl_init("$baseUrl/api/v1/products?per_page=1");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $token"]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$productsResponse = json_decode($response, true);
$productId = $productsResponse['data'][0]['id'] ?? null;

if (!$productId) {
    echo "❌ Could not get product\n";
    exit(1);
}

echo "✓ Found product ID: $productId\n\n";

// Step 3: Upload thumbnail
echo "Step 3: Uploading thumbnail image...\n";
echo "Image path: $imagePath\n";
echo "File size: " . filesize($imagePath) . " bytes\n";
echo "File exists: " . (file_exists($imagePath) ? 'Yes' : 'No') . "\n\n";

if (!file_exists($imagePath)) {
    echo "❌ Image file not found: $imagePath\n";
    exit(1);
}

$ch = curl_init("$baseUrl/api/v1/products/$productId/thumbnail");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $token"]);

// Create multipart form data
$cfile = curl_file_create($imagePath, 'image/png', 'mboka2020.PNG');
curl_setopt($ch, CURLOPT_POSTFIELDS, ['thumbnail' => $cfile]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Status: $httpCode\n";

if ($error) {
    echo "❌ CURL Error: $error\n";
    exit(1);
}

$uploadResponse = json_decode($response, true);

if ($httpCode === 200) {
    echo "✓ Upload successful!\n\n";
    echo "Response:\n";
    echo "- Thumbnail URL: " . ($uploadResponse['data']['thumbnail'] ?? 'N/A') . "\n";
    echo "\nFull Response:\n";
    echo json_encode($uploadResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
} else {
    echo "❌ Upload failed with status code: $httpCode\n";
    echo "Response: " . json_encode($uploadResponse, JSON_PRETTY_PRINT) . "\n";
}
