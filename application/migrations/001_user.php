<?php
class Migration_User extends CI_Migration {

  public function up()
  {
    $this->dbforge->add_field("id int(11) unsigned NOT NULL AUTO_INCREMENT");
    $this->dbforge->add_field("login varchar(255) NOT NULL DEFAULT ''");
    $this->dbforge->add_field("first varchar(255) NOT NULL DEFAULT ''");
    $this->dbforge->add_field("last varchar(255) NOT NULL DEFAULT ''");
    $this->dbforge->add_field("password varchar(255) NOT NULL DEFAULT ''");
    $this->dbforge->add_field("salt varchar(255) NOT NULL DEFAULT ''");
    $this->dbforge->add_field("email varchar(255) NOT NULL DEFAULT ''");
    $this->dbforge->add_field("user_status_id int(11) unsigned NOT NULL");
    $this->dbforge->add_field("invite_id int(11) unsigned");
    $this->dbforge->add_field("match_id int(11) unsigned");

    $this->dbforge->add_key('id', TRUE);
    $this->dbforge->create_table('user');

    $this->db->query('ALTER TABLE `user` ADD UNIQUE INDEX (`login`, `email`)');
  }

  public function down()
  {
    $this->dbforge->drop_table('user');
  }

}

