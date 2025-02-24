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
        // Parse URL
        $parsedUrl = parse_url($url);
        $path = $parsedUrl['path'];

        // Pisahkan base URL dan path setelah /storage/
        $pattern = "/\/api\/storage\//";
        if (preg_match($pattern, $path, $matches, PREG_OFFSET_CAPTURE)) {
            $storagePos = $matches[0][1] + strlen($matches[0][0]);
            $pathAfterStorage = substr($path, $storagePos);

            // Pisahkan ekstensi file
            $pathParts = pathinfo($pathAfterStorage);
            $filenameWithoutExt = $pathParts['dirname'] . '/' . $pathParts['filename'];
            $filenameWithoutExt = str_replace('/', '#', $filenameWithoutExt); // Ganti / dengan #

            // Bangun URL baru
            $newPath = $filenameWithoutExt . "?extension=" . $pathParts['extension'];
            return $newPath;
        }

        return $url; // Jika tidak cocok dengan format yang diinginkan, kembalikan original URL
    }

    public function revertUrlFormat($formattedUrl)
    {
        // Pisahkan path dan query string
        $urlParts = explode("?", $formattedUrl);
        $path = $urlParts[0];
        parse_str($urlParts[1] ?? '', $queryParams);

        // Ubah # kembali ke /
        $originalPath = str_replace('#', '/', $path);

        // Tambahkan kembali ekstensi jika ada
        if (isset($queryParams['extension'])) {
            $originalPath .= "." . $queryParams['extension'];
        }

        return $originalPath;
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
