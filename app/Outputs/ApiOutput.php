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

        // Extract components
        $scheme = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] . '://' : '';
        $host = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
        $path = $parsedUrl['path'];

        // Find the position of `/api/storage/`
        $pattern = "/\/api\/storage\//";
        if (preg_match($pattern, $path, $matches, PREG_OFFSET_CAPTURE)) {
            $storagePos = $matches[0][1] + strlen($matches[0][0]);
            $pathBeforeStorage = substr($path, 0, $storagePos);
            $pathAfterStorage = substr($path, $storagePos);

            // Replace `/` with `#` in the path after `/api/storage/`
            $formattedPath = str_replace('/', '#', $pathAfterStorage);

            // Rebuild the full URL
            $newUrl = $scheme . $host . $pathBeforeStorage . $formattedPath;
            return $newUrl;
        }

        return $url; // Return the original if no match is found
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
