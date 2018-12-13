<?php

class LocationMapper extends Mapper {

    public function getLocationByDesc($desc) {

        $sql = "SELECT id, lat, lon, location_desc, created
            from locations where location_desc = :location_desc";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(["location_desc" => $desc]);

        return new LocationEntity($stmt->fetch());
    }

    public function save(LocationEntity $location) {
        $sql = "insert into locations
            (lat, lon, location_desc) values
            (:lat, :lon, :location_desc)";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            "lat" => $location->getLat(),
            "lon" => $location->getLon(),
            "location_desc" => $location->getLocationDesc(),
        ]);
        if(!$result) {
            throw new Exception("could not save record");
        }
    }
}
