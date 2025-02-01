<?php

namespace App\Http\Controllers;

use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Storage;

abstract class Controller
{
    //
    public function render_json_array($klass, $layout, $data, $options = [])
    {
        $klass = new $klass();
        $resultRender = $klass->renderJson($data, $layout, $options);
        return response()->json($resultRender, 200);
    }

    public function render_json($klass, $layout, $data, $options = [])
    {
        $klass = new $klass();
        $resultRender = $klass->renderJson($data, $layout, $options);
        return response()->json($resultRender, 200);
    }

    public function send_notification($user, $title, $description)
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
