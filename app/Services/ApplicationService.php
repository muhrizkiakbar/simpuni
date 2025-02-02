<?php

namespace App\Services;

class ApplicationService
{
    public function __construct()
    {
        // Example logic for initializing ApiService
    }

    public function convertToFloat($currency)
    {
        // Remove the "Rp" currency symbol and any leading or trailing spaces
        $currency = trim(str_replace('Rp', '', $currency));

        // Remove any periods (thousands separator)
        $currency = str_replace('.', '', $currency);

        // Replace the comma with a period (decimal separator)
        $currency = str_replace(',', '.', $currency);

        // Convert the string to float and return
        return (float) $currency;
    }

    public function sendNotification($user, $title, $description)
    {
        $procject_id = 'simpuni-banjarbaru';
        $fcm = $user->fcm_token;

        $credentialsFilePath = Storage::path('app/json/google-services.json');
        $client = new GoogleClient();
        $client->setAuthConfig($credentialsFilePath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->fetchAccessTokenWithAssertion();
        $token = $client->getAccessToken();

        $access_token = $token['access_token'];

        $headers = [
            "Authorization: Bearer $access_token",
            'Content-Type: application/json'
        ];

        $data = [
            "message" => [
                "token" => $fcm,
                "notification" => [
                    "title" => $title,
                    "body" => $description,
                ],
                'data' => [
                    'user_id' => $user->id
                ]
            ]
        ];
        $payload = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/v1/projects/{$procject_id}/messages:send");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        return $err;
    }
}
