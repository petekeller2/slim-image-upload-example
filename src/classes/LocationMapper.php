<?php

class LocationtMapper extends Mapper {

    public function getLocationByDesc($desc) {

        $sql = "SELECT id, lat, lon, location_desc, created
            from locations where location_desc = :location_desc";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(["location_desc" => $desc]);

        return new LocationEntity($stmt->fetch());
    }
}
