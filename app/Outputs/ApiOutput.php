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
        // Parse URL untuk mendapatkan path
        $parsedUrl = parse_url($url);

        // Ambil path dari URL
        $path = $parsedUrl['path'] ?? '';

        // Pisahkan path berdasarkan "/"
        $segments = explode('/', trim($path, '/'));

        // Pastikan URL sesuai dengan pola yang diharapkan
        if (count($segments) < 3) {
            return false; // Tidak sesuai format yang diharapkan
        }

        // Ambil bagian path setelah "storage"
        $storageIndex = array_search('storage', $segments);
        if ($storageIndex === false || $storageIndex + 1 >= count($segments)) {
            return false; // Tidak ada "storage" dalam URL
        }

        // Ambil path setelah "storage"
        $pathSegments = array_slice($segments, $storageIndex + 1);

        // Ambil nama file dan pisahkan ekstensi
        $fileName = array_pop($pathSegments);
        $fileParts = explode('.', $fileName);
        $extension = array_pop($fileParts);
        $fileNameWithoutExt = implode('.', $fileParts);

        // Buat path dengan "#" sebagai pemisah
        $pathParam = implode('#', $pathSegments) . "#$fileNameWithoutExt";

        // Buat URL baru dengan query parameters
        $newUrl = "{$parsedUrl['scheme']}://{$parsedUrl['host']}/api/storage?path=$pathParam&extension=$extension";

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
