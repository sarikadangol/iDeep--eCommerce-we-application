<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    if (!isset($data->pidx)) {
        echo json_encode(['success' => false, 'message' => 'Missing pidx']);
        exit;
    }

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://a.khalti.com/api/v2/epayment/lookup/",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode(['pidx' => $data->pidx]),
        CURLOPT_HTTPHEADER => [
            "Authorization: Key test_secret_key_ae3e3dfbbbd14d14a5f20a9beab8f08a", // 🔁 your test secret key
            "Content-Type: application/json"
        ]
    ]);

    $response = curl_exec($curl);
    curl_close($curl);
    $res = json_decode($response, true);

    if (isset($res['status']) && $res['status'] === 'Completed') {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Payment not completed']);
    }
}
