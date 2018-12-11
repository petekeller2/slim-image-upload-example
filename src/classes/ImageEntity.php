<?php

class ImageEntity {

    protected $id;
    protected $image_desc;
    protected $image_path;
    protected $location_id;

    /**
     * Accept an array of data matching properties of this class
     * and create the class
     *
     * @param array $data The data to use to create
     */
    public function __construct(array $data) {
        // no id if we're creating
        if(isset($data['id'])) {
            $this->id = $data['id'];
        }
        $this->image_desc = $data['image_desc'];
        $this->image_path = $data['image_path'];
        $this->location_id = $data['location_id'];

    }

    public function getId() {
        return $this->id;
    }

    public function getImageDesc() {
        return $this->image_desc;
    }

    public function getImagePath() {
        return $this->image_path;
    }

    public function getLocationId() {
        return $this->location_id;
    }
}
