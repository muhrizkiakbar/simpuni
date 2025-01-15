<?php

namespace App\Http\Controllers;

abstract class Controller
{
    //
    public function render_json_array($klass, $layout, $data, $options = []) {
        $klass = new $klass();
        $resultRender = $klass->renderJson($data, $layout, $options);
        return response()->json($resultRender, 200);
    }

    public function render_json($klass, $layout, $data, $options = []) {
        $klass = new $klass();
        $resultRender = $klass->renderJson($data, $layout, $options);
        return response()->json($resultRender, 200);
    }
}
