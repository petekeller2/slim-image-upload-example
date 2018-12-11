<?php

class LocationEntity {

    protected $id;
    protected $lat;
    protected $lon;
    protected $location_desc;


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
        $this->lat = $data['lat'];
        $this->lon = $data['lon'];
        $this->location_desc = $data['location_desc'];

    }

    public function getId() {
        return $this->id;
    }

    public function getLat() {
        return $this->lat;
    }

    public function getLon() {
        return $this->lon;
    }

    public function getLocationDesc() {
        return $this->location_desc;
    }
}
