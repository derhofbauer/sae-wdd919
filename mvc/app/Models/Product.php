<?php

namespace App\Models;

use Core\Config;
use Core\Database;
use Core\Models\BaseModel;

/**
 * Class Product
 *
 * @package App\Models
 * @todo    : comment
 */
class Product extends BaseModel
{

    public int $id;
    public string $name;
    public string $description;
    public float $price;
    public int $stock;
    public string $images;

    private object $db;

    // const TABLENAME = 'alternativeTable';

    /**
     * Product constructor.
     *
     * @param array $data
     */
    public function __construct (array $data = [])
    {
        $this->db = new Database();

        if (!empty($data)) {
            $this->fill($data);
        }
    }

    /**
     * @param array $data
     */
    public function fill (array $data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->description = (string)$data['description'];
        $this->price = (float)$data['price'];
        $this->stock = (int)$data['stock'];
        $this->images = (string)$data['images'];
    }

    /**
     * @return array
     */
    public function getImages (): array
    {
        $delimiter = ';';
        $storagePath = Config::get('app.storage-path');
        $uploadPath = Config::get('app.upload-path');

        if (strlen($this->images) > 0) {
            if (strpos($this->images, $delimiter) !== false) {
                $images = explode($delimiter, $this->images);
            } else {
                $images = [$this->images];
            }

            $images = array_map(function ($image) use ($storagePath, $uploadPath) {
                $imagePath = $storagePath . $uploadPath . $image;
                return str_replace('//', '/', $imagePath);
            }, $images);

            return $images;
        }
        return [];
    }

    public function getPrice (): string
    {
        return number_format($this->price, 2, ',', '.') . ' €';
    }

}
