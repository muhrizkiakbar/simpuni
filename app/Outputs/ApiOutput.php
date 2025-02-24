<?php

namespace App\Outputs;

use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class ApiOutput
{
    /**
     * Format the paginated data with separated pagination section.
     *
     * @param LengthAwarePaginator $data
     * @param array $fields
     * @return array
     */

    public function paginated($data, $layout, $options)
    {
        // Check if the paginator is of type LengthAwarePaginator
        if ($data instanceof LengthAwarePaginator) {
            $formattedData = $this->mapData($data, $layout, $options);

            return [
                'data' => $formattedData,
                'pagination' => [
                    'pagination_type' => 'base',
                    'next_page' => $data->nextpageurl(),
                    'previous_page' => $data->previouspageurl(),
                    'per_page' => $data->perpage(),
                ],
            ];
        }

        // Check if the paginator is of type CursorPaginator
        if ($data instanceof CursorPaginator) {
            $formattedData = $this->mapData($data, $layout, $options);

            return [
                'data' => $formattedData,
                'pagination' => [
                    'pagination_type' => 'cursor',
                    'next_page' => $data->nextpageurl(),
                    'previous_page' => $data->previouspageurl(),
                    'per_page' => $data->perpage(),
                ],
            ];
        }

        return $data;
    }

    public function renderJson($data, $layout = "format", $options = [])
    {
        if ($data instanceof LengthAwarePaginator) {
            return $this->paginated($data, $layout, $options);
        } elseif ($data instanceof CursorPaginator) {
            return $this->paginated($data, $layout, $options);
        } else {

            if ($data === []) {
                return [];
            }

            if (array_key_exists('mode', $options)) {
                if ($options["mode"] == "raw_many_data") {
                    return $this->mapData($data, $layout, $options);
                } elseif ($options["mode"] == "raw_data") {
                    return $this->$layout($data, $options);
                }
            }

            return [
                'data' => $this->$layout($data, $options),
            ];
        }
    }

    protected function mapData($data, $layout, $options)
    {
        return $data->map(function ($item) use ($layout, $options) {
            return $this->$layout($item, $options);
        });
    }

    public function convertUrlFormatStorage($url)
    {
        // Parse URL untuk mendapatkan path dan query
        $parsedUrl = parse_url($url);

        // Ambil path dari URL
        $path = $parsedUrl['path'];

        // Pisahkan path berdasarkan `/`
        $pathParts = explode('/', $path);

        // Ambil bagian terakhir dari path (nama file)
        $filename = array_pop($pathParts);

        // Pisahkan nama file dari ekstensi
        $fileParts = explode('.', $filename);

        // Jika ada ekstensi, pisahkan
        if (count($fileParts) > 1) {
            $extension = array_pop($fileParts); // Ambil ekstensi
            $filenameWithoutExt = implode('.', $fileParts); // Gabungkan kembali nama file tanpa ekstensi
        } else {
            return $url; // Jika tidak ada ekstensi, kembalikan URL asli
        }

        // Bangun ulang path tanpa ekstensi
        $newPath = implode('/', $pathParts) . '/' . $filenameWithoutExt;

        // Bangun ulang URL dengan query extension
        $newUrl = "{$parsedUrl['scheme']}://{$parsedUrl['host']}{$newPath}?extension={$extension}";

        // Tambahkan kembali query string jika ada
        if (!empty($parsedUrl['query'])) {
            $newUrl .= "&" . $parsedUrl['query'];
        }

        return $newUrl;
    }


    /**
     * Format the data for a single object or collection.
     *
     * @param mixed $object
     * @param array $fields
     * @return array
     */
    abstract public function format($object, $options);
}
