<?php

class ImageMapper extends Mapper {

    public function getImages() {
        $sql = "SELECT id, location_id, image_desc, image_path, created
            from images";
        $stmt = $this->db->query($sql);

	   $results = [];
        while($row = $stmt->fetch()) {
            $results[] = new ImageEntity($row);
        }
        return $results;
    }

    public function getSortedImages($lat = 0, $lon = 0, $limit = 20, $distance = 50) {


        $sql = "SELECT images.id, locations.lon, locations.lat, images.image_desc, ( 3959 * acos( cos( radians(:lat) ) * cos( radians( locations.lat ) ) * cos( radians( locations.lon ) - radians(:lon) ) + sin( radians(:lat) ) * sin( radians( locations.lat ) ) ) ) AS distance FROM locations INNER JOIN images ON images.location_id = locations.id HAVING distance < :distance ORDER BY distance LIMIT 0 , :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':limit', intval($limit, 10), PDO::PARAM_INT);
        $stmt->bindParam(':lat', $lat, PDO::PARAM_STR);
        $stmt->bindParam(':lon', $lon, PDO::PARAM_STR);
        $stmt->bindParam(':distance', $distance, PDO::PARAM_STR);
        $stmt->execute();

       $results = [];
        while($row = $stmt->fetch()) {
            $results[] = $row;
        }

        return $results;
    }

    public function getImageById($id) {
        $sql = "SELECT id, location_id, image_desc, image_path, created
            from images where id = :image_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(["image_id" => $id]);

	    return new ImageEntity($stmt->fetch());
    }



}
