<?php

use Phinx\Migration\AbstractMigration;

class ImageAndLoc extends AbstractMigration
{

    public function up() {
        $locations_table = $this->table('locations');
	$locations_table->engine = “InnoDB”;
	    $locations_table->addColumn('lat', 'float')
	            ->addColumn('lon', 'float')
                    ->addColumn('location_desc', 'string')
              	    ->addColumn('created', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
                    ->create();
	$locations_table->saveData();

        $images_table = $this->table('images');
	$images_table->engine = “InnoDB”;
        $images_table->addColumn('image_path', 'string')
                    ->addColumn('image_desc', 'string')
                    ->addColumn('location_id', 'integer')
              	    ->addColumn('created', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
                    ->addForeignKey('location_id', 'locations', 'id')
                    ->create();
	$images_table->saveData();

    }

    public function down() {
        $this->dropTable('locations');
        $this->dropTable('images');
    }

}
