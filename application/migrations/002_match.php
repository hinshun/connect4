<?php
class Migration_Match extends CI_Migration {

  public function up()
  {
    $this->dbforge->add_field("id int(11) unsigned NOT NULL AUTO_INCREMENT");
    $this->dbforge->add_field("user1_id int(11) unsigned NOT NULL");
    $this->dbforge->add_field("user2_id int(11) unsigned NOT NULL");
    $this->dbforge->add_field("match_status_id int(11) unsigned NOT NULL");
    $this->dbforge->add_field("u1_msg varchar(255) DEFAULT ''");
    $this->dbforge->add_field("u2_msg varchar(255) DEFAULT ''");
    $this->dbforge->add_field("board_state blob");

    $this->dbforge->add_key('id', TRUE);

    $this->dbforge->create_table('match', TRUE);
  }

  public function down()
  {
    $this->dbforge->drop_table('match');
  }

}

