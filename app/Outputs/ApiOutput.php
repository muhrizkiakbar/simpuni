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
            $formattedData = $data->getCollection()->map(function ($item) use ($layout, $options) {
                return $this->$layout($item, $options);
            });

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
            $formattedData = $data->getCollection()->map(function ($item) use ($layout, $options) {
                return $this->$layout($item, $options);
            });
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

        // If it's neither LengthAwarePaginator nor CursorPaginator, just return the raw data
        return $data;
    }

    public function renderJson($data, $layout = "format", $options = [])
    {
        if ($data instanceof LengthAwarePaginator) {
            return $this->paginated($data, $layout, $options);
        }
        elseif ($data instanceof CursorPaginator) {
            return $this->paginated($data, $layout, $options);
        } else {

            if ($data === []) {
                return [];
            }

            if ($options["mode"] ?? "not_raw_data" == "raw_data")
            {
                return $this->$layout($data, $options);
            }

            return [
                'data' => $this->$layout($data, $options),
            ];
        }
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

